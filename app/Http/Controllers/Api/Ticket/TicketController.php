<?php

namespace App\Http\Controllers\Api\Ticket;

use App\Http\Controllers\Controller;
use App\Http\Requests\Ticket\StoreRequest;
use App\Http\Requests\Ticket\TicketReplyRequest;
use App\Http\Resources\Label\LabelSelectResource;
use App\Http\Resources\Priority\PriorityResource;
use App\Http\Resources\Department\DepartmentSelectResource;
use App\Http\Resources\Status\StatusResource;
use App\Http\Resources\Ticket\TicketDetailsResource;
use App\Http\Resources\Ticket\TicketListResource;
use App\Http\Resources\Ticket\TicketManageResource;
use App\Models\PreventiveMaintenance;
use App\Models\Department;
use App\Models\Setting;
use App\Models\Status;
use App\Models\Ticket;
use App\Models\TicketReply;
use App\Notifications\Ticket\AssignTicketToDepartment;
use App\Notifications\Ticket\NewTicketReplyFromUserToAgent;
use App\Notifications\Ticket\NewTicketReplyFromUserToUser;
use App\Notifications\Ticket\NewTicketRequest;
use Auth;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Str;
use Throwable;

use App\Models\Label;
use App\Models\Priority;

class TicketController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * Display a listing of the resource.
     *
     * @param  Request  $request
     * @return JsonResponse
     * @throws Exception
     */
    public function index(Request $request): JsonResponse
    {
        $sort = json_decode($request->get('sort', json_encode(['order' => 'asc', 'column' => 'created_at'], JSON_THROW_ON_ERROR)), true, 512, JSON_THROW_ON_ERROR);

        $items = Ticket::filter($request->all())
            ->where('user_id', Auth::id())
            ->orderBy($sort['column'], $sort['order'])
            ->paginate((int) $request->get('perPage', 10));
            
        return response()->json([
            'items' => TicketListResource::collection($items->items()),
            'pagination' => [
                'currentPage' => $items->currentPage(),
                'perPage' => $items->perPage(),
                'total' => $items->total(),
                'totalPages' => $items->lastPage()
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreRequest  $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function store(StoreRequest $request): JsonResponse
    {
        $request->validated();
        $ticket = new Ticket();
        $ticket->uuid = Str::uuid();
        $ticket->subject = $request->get('subject');
        $ticket->status_id = 1;
        if ($request->has('department_id')) {
            $ticket->department_id = $request->get('department_id');
        }

        if ($request->has('sub_department_id')) {
            $ticket->sub_department_id = $request->get('sub_department_id');
            if ( empty($ticket->department_id) ) {
                $ticket->department_id = $request->get('sub_department_id');    
            }
        }

        if ($request->has('priority_id')) {
            $ticket->priority_id = $request->get('priority_id');
        }

        if ($request->has('department_location_id')) {
            $ticket->department_location_id = $request->get('department_location_id');
        }

        if ($request->has('department_sub_location_id')) {
            $ticket->department_sub_location_id = $request->get('department_sub_location_id');
        }

        $ticket->user_id = Auth::id();
        $ticket->saveOrFail();
        $ticketReply = new TicketReply();
        $ticketReply->user_id = Auth::id();
        $ticketReply->body = $request->get('body');
        if ($ticket->ticketReplies()->save($ticketReply)) {
            if ($request->has('attachments')) {
                $ticketReply->ticketAttachments()->sync(collect($request->get('attachments'))->map(function ($attachment) {
                    return $attachment['id'];
                }));
            }
            $ticket->user->notify((new NewTicketRequest($ticket))->locale(Setting::getDecoded('app_locale')));
            if ($ticket->department_id !== null) {
                foreach ($ticket->department->agents() as $agent) {
                    $agent->notify(new AssignTicketToDepartment($ticket, $agent));
                }
            }
            return response()->json(['message' => __('Data saved correctly'), 'ticket' => new TicketManageResource($ticket)]);
        }
        return response()->json(['message' => __('An error occurred while saving data')], 500);
    }

    /**
     * Display the specified resource.
     *
     * @param  Ticket  $ticket
     * @return JsonResponse
     */
    public function show(Ticket $ticket): JsonResponse
    {
        if ($ticket->user_id !== Auth::id()) {
            return response()->json(['message' => __('Not found')], 404);
        }
        return response()->json(new TicketDetailsResource($ticket));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  TicketReplyRequest  $request
     * @param  Ticket  $ticket
     * @return JsonResponse
     */
    public function reply(TicketReplyRequest $request, Ticket $ticket): JsonResponse
    {
        if ($ticket->user_id !== Auth::id()) {
            return response()->json(['message' => __('Not found')], 404);
        }
        $request->validated();
        $ticketReply = new TicketReply();
        $ticketReply->user_id = Auth::id();
        $ticketReply->body = $request->get('body');
        if ($ticket->ticketReplies()->save($ticketReply)) {
            if ($request->has('attachments')) {
                $ticketReply->ticketAttachments()->sync(collect($request->get('attachments'))->map(function ($attachment) {
                    return $attachment['id'];
                }));
            }
            $ticket->updated_at = Carbon::now();
            $ticket->save();
            $ticket->user->notify((new NewTicketReplyFromUserToUser($ticket))->locale(Setting::getDecoded('app_locale')));
            if ($ticket->agent) {
                $ticket->agent->notify((new NewTicketReplyFromUserToAgent($ticket, $ticket->agent))->locale(Setting::getDecoded('app_locale')));
            }
            return response()->json(['message' => __('Data saved correctly'), 'ticket' => new TicketManageResource($ticket)]);
        }
        return response()->json(['message' => __('An error occurred while saving data')], 500);
    }

    public function departments(Request $request): JsonResponse
    {
        $data = null;
        $user = Auth::user();
        $call_from = $request->get('call_from') ?? '';
        // dd($user);
        if ($call_from == 'ticket_new' && $user && $user->role_id == 3 && $user->department_id > 0) {//customer
            $data = Department::where('public', '=', true)->where('parent_id', $user->department_id)->get();
        }
        else{
            $data = Department::where('public', '=', true)->get();
        }
        return response()->json(DepartmentSelectResource::collection($data));
    }

    public function filters(Request $request): JsonResponse
    {
        $response = [];

        $call_from = $request->get('call_from') ?? '';
        // $user = Auth::user();
        // dd($user);

        $departmentsData = Department::where('public', '=', true)
        ->where('parent_id', '=', 0)
        ->with(['departmentLocation'=>function($dl){
                            return $dl->whereNull('parent_id')
                                    ->with(['subLocations'=>function($dsl){
                                        return $dsl;
                                    }]);
        }]);

        // if (false && 
        // $call_from == 'ticket_new' && $user && $user->role_id == 3 && $user->department_id > 0) {//customer
        //     $departmentsData = $departmentsData->where('parent_id', $user->department_id)->get();
        //     $departments = DepartmentSelectResource::collection($departmentsData);
        // }
        // else{
            // $departmentsData = Department::where('parent_id', '=', 0)->get();
            $departmentsData = $departmentsData->get();
            $departments = DepartmentSelectResource::collection( $departmentsData );
            
        // }
        
        $response = [
            'labels' => LabelSelectResource::collection(Label::all()),
            'statuses' => StatusResource::collection(Status::all()),
            'priorities' => PriorityResource::collection(Priority::orderBy('value')->get()),
        ];

        $response['departments'] = $departments;

        return response()->json($response);
    }

    public function statuses(): JsonResponse
    {
        return response()->json(StatusResource::collection(Status::all()));
    }

}
