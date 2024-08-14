<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Api\File\FileController;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Ticket\StoreRequest;
use App\Http\Requests\Dashboard\Ticket\TicketReplyRequest;
use App\Http\Requests\File\StoreFileRequest;
use App\Http\Resources\CannedReply\CannedReplyResource;
use App\Http\Resources\Department\DepartmentSelectResource;
use App\Http\Resources\Label\LabelSelectResource;
use App\Http\Resources\Priority\PriorityResource;
use App\Http\Resources\Status\StatusResource;
use App\Http\Resources\Ticket\TicketListResource;
use App\Http\Resources\Ticket\TicketManageResource;
use App\Http\Resources\User\UserDetailsResource;
use App\Models\CannedReply;
use App\Models\Department;
use App\Models\Label;
use App\Models\LocationCategory;
use App\Models\Priority;
use App\Models\Setting;
use App\Models\Status;
use App\Models\TicketType;
use App\Models\Ticket;
use App\Models\TicketReply;
use App\Models\User;
use App\Models\UserRole;
use App\Notifications\Ticket\NewTicketFromAgent;
use App\Notifications\Ticket\NewTicketReplyFromAgentToUser;
use App\Notifications\Ticket\NotifyOnActivityTicketsStakeHolder;
use Auth;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Str;
use Throwable;
use App\Helpers\Helper;
use Illuminate\Support\Facades\Log;

class TicketController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum', ['except' => ['ticketsReports', 'ticketsReportSummary']]) ;
        // $this->middleware('auth:sanctum');
    }
    //Admin dashboard/ticket listing 
    /**
     * Display a listing of the resource.
     * 
     * @param  Request  $request
     * @return JsonResponse
     * @throws Exception
     */
    public function index(Request $request): JsonResponse
    {

        $only_my_tickets = $request->only_my_tickets ?? 0;
        $only_my_tickets = $only_my_tickets == 1 ? 1 : 0;

        /** @var User $user */
        $user = Auth::user();
        $uData = [];
        $uData['id'] = $user->id;
        // $uData['userDepartmentsIdsArray'] = $user->departments()->pluck('id')->toArray();
        // $uData['userDepartmentsIdsArray'] = $user->department_id > 0 ? [$user->department_id] : [];
        $sort = json_decode($request->get('sort', json_encode(['order' => 'asc', 'column' => 'created_at'], JSON_THROW_ON_ERROR)), true, 512, JSON_THROW_ON_ERROR);

        $is_admin = Helper::userIs('ADMIN', $user);
        $is_operator = Helper::userIs('OPERATOR', $user);
        $is_engineer = Helper::userIs('ENGINEER', $user);


        $main_query = null;

        //Get all tickets
        if($only_my_tickets == 1){
            $main_query = Ticket::filter($request->all())
                ->where(function (Builder $query) use ($uData) {
                    $query->orWhere('created_by', $uData['id']);                    
                });
        }
        else if($is_admin && $only_my_tickets == 0){
            $main_query = Ticket::filter($request->all());
        }        
        //Get tickets 
        //-created by user
        //-assigned to user
        //-closed by user
        //-ticket belongs to user department   
        else if( 
            in_array($user->role_id, 
                        [
                            $is_engineer
                        ]
                    )
            && $only_my_tickets == 0 
        ){
            $main_query = Ticket::filter($request->all())
                ->where(function (Builder $query) use ($uData) {
                    $query->where('user_id', $uData['id']);//assigned to
                    $query->orWhere('created_by', $uData['id']);
                    $query->orWhere('agent_id', $uData['id']);//who had assigned the ticket to user_id
                    $query->orWhere('closed_by', $uData['id']);
                    // $query->orWhereIn('department_id', $uData['userDepartmentsIdsArray']);                    
                });
        }
        else{
            $main_query = Ticket::filter($request->all())
                ->where(function (Builder $query) use ($uData) {
                    $query->where('user_id', $uData['id']);//assigned to
                    $query->orWhere('created_by', $uData['id']);
                    $query->orWhere('agent_id', $uData['id']);//who had assigned the ticket to user_id
                    $query->orWhere('closed_by', $uData['id']);
                });
        }

        
        

        $items = $main_query
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
        $ticket->status_id = $request->get('status_id');        
        $ticket->department_id = $request->get('department_id');
        $ticket->sub_department_id = $request->get('sub_department_id');
        if ($request->has('department_location_id')) {
            $ticket->department_location_id = $request->get('department_location_id');
        }

        if ($request->has('department_sub_location_id')) {
            $ticket->department_sub_location_id = $request->get('department_sub_location_id');
        }

        if( !empty($request->priority_id) ){
            $ticket->priority_id = $request->get('priority_id');
        }
        
        if( !empty($request->user_id) ){
            $ticket->user_id = $request->get('user_id');
        }

        if( !empty($request->ticket_type_id) ){
            $ticket->ticket_type_id = $request->get('ticket_type_id');
        }

        $ticket->agent_id = Auth::id();
        $ticket->created_by = Auth::id();
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

            if( !empty($ticket->user) ){
                $ticket->user->notify((new NewTicketFromAgent($ticket))->locale(Setting::getDecoded('app_locale')));
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
        /** @var User $user */
        $user = Auth::user();
        if (!$ticket->verifyUser($user)) {
            return response()->json(['message' => __('You do not have permissions to manage this ticket')], 403);
        }
        return response()->json(new TicketManageResource($ticket));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  TicketReplyRequest  $request
     * @param  Ticket  $ticket
     * @return JsonResponse
     */
    public function reply(Ticket $ticket, TicketReplyRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$ticket->verifyUser($user)) {
            return response()->json(['message' => __('You do not have permissions to manage this ticket')], 403);
        }

        if( !empty($request->status_id) ){
            if ( Helper::roleCanUseThisStatus($request->status_id, $user) == 0 ) {
                return response()->json(['message' => __('You are not allowed to set this status')], 403);
            }
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
            $ticket->status_id = $request->get('status_id');
            $ticket->updated_at = Carbon::now();
            $ticket->save();
            
            $this->notifyOnActivityToStakeHolders($ticket, $ticketReply);

            return response()->json(['message' => __('Data saved correctly'), 'ticket' => new TicketManageResource($ticket)]);
        }
        return response()->json(['message' => __('An error occurred while saving data')], 500);
    }

    private function notifyOnActivityToStakeHolders($ticket, $ticketReply){
        $ticketIdStakeHolderIds = Helper::getTicketStakeHolderIds($ticket);
        $stakeHoldersInfo = User::whereIn('id', $ticketIdStakeHolderIds)->get();
        // dd($stakeHoldersInfo);
        //if ticket is assigned to user, then notify that person on reply
        // foreach(['user', 'agent', 'createdBy', 'closedBy'] as $index){
        //     $stakeHolder = $ticket->{$index};

        foreach ($stakeHoldersInfo as $stakeHolder) {
            // echo dump($stakeHolder);
            if( !empty($stakeHolder) && !empty($stakeHolder->id) && !empty($stakeHolder->email) ){
                try {
                    $stakeHolder->notify(
                        (new NotifyOnActivityTicketsStakeHolder($ticket, $stakeHolder, $ticketReply))
                        ->locale( Setting::getDecoded('app_locale') )
                    );
                // Catch the error
                } catch(\Swift_TransportException $e){
                    if($e->getMessage()) {
                       // dd(['message'=>$e->getMessage(), 'stakeHolder' => $stakeHolder]);
                        $exception =  [
                            'developer_message'=>'Error in method notifyOnActivityToStakeHolders ', 
                            'stakeHolder' => [
                                                'id' => $stakeHolder->id,
                                                'email' => $stakeHolder->email
                                            ],
                            'message'=>$e->getMessage()
                        ];
                        Log::error($exception);
                    }             
                }
            }
            // break;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Ticket  $ticket
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy(Ticket $ticket): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$ticket->verifyUser($user)) {
            return response()->json(['message' => __('You do not have permissions to manage this ticket')], 403);
        }
        $ticket->labels()->sync([]);
        foreach ($ticket->ticketReplies()->get() as $ticketReply) {
            $ticketReply->ticketAttachments()->sync([]);
        }
        $ticket->ticketReplies()->delete();
        if ($ticket->delete()) {
            return response()->json(['message' => 'Data deleted successfully']);
        }
        return response()->json(['message' => __('An error occurred while deleting data')], 500);
    }

    public function filters(Request $request): JsonResponse
    {
        $user_role_group = config('app.user_role_group');
        $login_user = Auth::user();

        $is_operator = Helper::userIs('OPERATOR', $login_user);
        $is_engineer = Helper::userIs('ENGINEER', $login_user);
        $is_client = Helper::userIs('CLIENT', $login_user);

        // $roles = UserRole::where('dashboard_access', true)->pluck('id');
        // $agents = User::whereIn('role_id', $roles)->where('status', true)->get();
        $department = Department::where('parent_id', '=', 0)
                      ->with(['departmentLocation'=>function($dl){
                            return $dl->whereNull('parent_id')
                                    ->with(['subLocations'=>function($dsl){
                                        return $dsl;
                                    }]);
                      }])
                      ->get();
        


        // dd([$is_operator,$is_engineer, $login_user]);

        $all_users = null;
        if( 
            $is_operator ||
            $is_engineer ||
            ( !empty($request->called_from) && 
                    ($request->called_from == 'reports')
                ) 
        ){
            
            $all_users = User::where('status', true);
                        

            if($is_engineer){
                $eng_and_network_eng =  array_merge(
                                            $user_role_group['NETWORK_IT'],
                                            $user_role_group['ENGINEER'],
                                            $user_role_group['TECHNICIAN'],
                                        );
                $all_users = $all_users
                                ->whereIn('role_id', 
                                    $eng_and_network_eng
                                );

            }            
            else if($is_client){
                $eng_and_network_eng =  array_merge(
                                            $user_role_group['NETWORK_IT'],
                                            $user_role_group['TECHNICIAN'],
                                            $user_role_group['ENGINEER'],
                                            $user_role_group['OPERATOR'],
                                        );
                $all_users = $all_users
                                ->whereIn('role_id', 
                                    $eng_and_network_eng
                                );
            }
            //only return engineers of same deparment as login user belongs
            else if($is_operator){
                $all_users = $all_users
                                ->whereIn('role_id', 
                                    $user_role_group['ENGINEER']
                                )
                                ->where('department_id', $login_user->department_id);
            }            

            $all_users = $all_users->get();            
        }
        else {
            $all_users = User::where('status', true)->get();
        }


        
        $all_labels = Label::all();
        $all_priority = Priority::orderBy('value')->get();
        $all_status =  Status::all();
        $locationCategoryList = LocationCategory::all();

        $ticket_types =  TicketType::all();

        $required_with_all = true;

        if($required_with_all == true){
            $temp = new Department();
            $temp->name = "All";
            $department->prepend($temp);

            // $temp = new User();
            // $temp->name = "All";
            // $agents->prepend($temp);
            
            // $temp = new User();
            // $temp->name = "All";
            // $all_users->prepend($temp);
            
            $temp = new Label();
            $temp->name = "All";
            $all_labels->prepend($temp);
            
            $temp = new Priority();
            $temp->name = "All";
            $all_priority->prepend($temp);
            
            $temp = new Status();
            $temp->name = "All";
            $all_status->prepend($temp);
            $all_status = Helper::roleCanUseStatus($all_status, $login_user);

            $temp = new LocationCategory();
            $temp->id = "";
            $temp->name = "All";
            $locationCategoryList->prepend($temp);

            $temp = new TicketType();
            $temp->id = "";
            $temp->type = "All";
            $ticket_types->prepend($temp);
        }

        // dd($all_users->pluck('id'));

        $response = [
            'agents' => UserDetailsResource::collection($all_users),//$agents),
            'customers' => UserDetailsResource::collection($all_users),
            'departments' => DepartmentSelectResource::collection($department),
            'labels' => LabelSelectResource::collection($all_labels),
            'statuses' => StatusResource::collection($all_status),
            'priorities' => PriorityResource::collection($all_priority),
            'locationCategoryList' => $locationCategoryList,
            'ticket_types' => $ticket_types
        ];

        return response()->json($response);

        
    }

    public function cannedReplies(): JsonResponse
    {
        return response()->json(CannedReplyResource::collection(CannedReply::where(function ($builder) {
            /** @var Builder|CannedReply $builder */
            $builder->where('shared', '=', true)
                ->orWhere('user_id', '=', Auth::id());
        })->get()));
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     * @throws Exception
     */
    public function quickActions(Request $request): JsonResponse
    {
        $action = $request->get('action');
        /** @var User $user */
        $user = Auth::user();
        $tickets = Ticket::whereIn('id', $request->get('tickets'));
        if ($user &&  Helper::userIs('ADMIN', $user) == false) {
            $tickets->where(function (Builder $query) use ($user) {
                $query->where('agent_id', $user->id);
                $query->orWhere('closed_by', $user->id);
                $query->orWhereIn('department_id', $user->departments()->pluck('id')->toArray());
                $query->orWhere(function (Builder $query) use ($user) {
                    $departments = array_unique(array_merge($user->departments()->pluck('id')->toArray(), Department::where('all_agents', 1)->pluck('id')->toArray()));
                    $query->whereNull('agent_id');
                    $query->whereIn('department_id', $departments);
                });
            });
        }
        if ($tickets->count() < 1) {
            return response()->json(['message' => __('You have not selected a ticket or do not have permissions to perform this action')], 403);
        }
        if ($action === 'agent') {
            $tickets->update(['agent_id' => $request->get('value')]);
            return response()->json(['message' => __('Tickets assigned to the selected agent')]);
        }
        if ($action === 'department') {
            $tickets->update(['department_id' => $request->get('value')]);
            return response()->json(['message' => __('Tickets assigned to the selected department')]);
        }
        if ($action === 'label') {
            foreach ($tickets->get() as $ticket) {
                /** @var Ticket $ticket */
                $ticket->labels()->syncWithoutDetaching($request->get('value'));
                $ticket->updated_at = Carbon::now();
                $ticket->save();
            }
            return response()->json(['message' => __('The label has been added to selected tickets')]);
        }
        if ($action === 'priority') {
            $tickets->update(['priority_id' => $request->get('value')]);
            return response()->json(['message' => __('The priority of the selected tickets has been changed')]);
        }
        if ($action === 'delete') {
            foreach ($tickets->get() as $ticket) {
                /** @var Ticket $ticket */
                $ticket->labels()->sync([]);
                foreach ($ticket->ticketReplies()->get() as $ticketReply) {
                    $ticketReply->ticketAttachments()->sync([]);
                }
                $ticket->ticketReplies()->delete();
                $ticket->delete();
            }
            return response()->json(['message' => __('The selected tickets have been deleted')]);
        }
        return response()->json(['message' => __('Quick action not found')], 404);
    }

    /**
     * @param  Ticket  $ticket
     * @param  Request  $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function ticketQuickActions(Ticket $ticket, Request $request): JsonResponse
    {
        $value = $request->get('value');
        $action = $request->get('action');
        /** @var User $user */
        $user = Auth::user();
        if (!$ticket->verifyUser($user)) {
            return response()->json(['message' => __('You do not have permissions to manage this ticket')], 403);
        }
        if ($action === 'agent') {
            // $ticket->agent_id = $value;

            $ticket->user_id = $value;
            $ticket->agent_id = $user->id;

            $ticket->saveOrFail();
            return response()->json(['message' => __('Ticket assigned to the selected agent'), 'ticket' => new TicketManageResource($ticket), 'access' => $ticket->verifyUser($user)]);
        }
        if ($action === 'department') {
            $ticket->department_id = $value;
            $ticket->saveOrFail();
            return response()->json(['message' => __('Ticket assigned to the selected department'), 'ticket' => new TicketManageResource($ticket), 'access' => $ticket->verifyUser($user)]);
        }
        if ($action === 'label') {
            $ticket->labels()->syncWithoutDetaching($request->get('value'));
            $ticket->updated_at = Carbon::now();
            $ticket->save();
            return response()->json(['message' => __('Label has been assigned to ticket'), 'ticket' => new TicketManageResource($ticket), 'access' => true]);
        }
        if ($action === 'priority') {
            $ticket->priority_id = $value;
            $ticket->saveOrFail();
            return response()->json(['message' => __('Ticket priority has been changed'), 'ticket' => new TicketManageResource($ticket), 'access' => true]);
        }
        return response()->json(['message' => __('Quick action not found')], 404);
    }

    public function removeLabel(Ticket $ticket, Request $request): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$ticket->verifyUser($user)) {
            return response()->json(['message' => __('You do not have permissions to manage this ticket')], 403);
        }
        if ($ticket->labels()->detach($request->get('label'))) {
            return response()->json(['message' => __('Data saved correctly'), 'ticket' => new TicketManageResource($ticket)]);
        }
        return response()->json(['message' => __('An error occurred while saving data')], 500);
    }

    public function uploadAttachment(StoreFileRequest $request): JsonResponse
    {
        return (new FileController())->uploadAttachment($request);
    }

}
