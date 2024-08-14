<?php

namespace App\Http\Controllers\Api\Dashboard\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Admin\Label\StoreRequest;
use App\Http\Requests\Dashboard\Admin\Label\UpdateRequest;
use App\Http\Resources\Label\TicketTypeResource;
use App\Models\TicketType;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TicketTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return response()->json(TicketTypeResource::collection(TicketType::all()));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return JsonResponse
     */
    public function addEdit(Request $request): JsonResponse
    {   
        // dd($request->input());

        $rules = [
            'type' => 'required',
        ];

        $customMessages = [
            'required' => 'The :attribute field is required.'
        ];

        $this->validate($request, $rules, $customMessages);

        $ticketType = null;
        if( !empty($request->id) ){
            $ticketType = TicketType::where('id', $request->id)->first();            
        }

        if( empty($ticketType->id) ){
            $ticketType = new TicketType();
        }
        
        $ticketType->type = $request->get('type');

        if ($ticketType->save()) {
            return response()->json(['message' => 'Data saved correctly', 'ticketType' => new TicketTypeResource($ticketType)]);
        }
        return response()->json(['message' => __('An error occurred while saving data')], 500);
    }

    /**
     * Display the specified resource.
     *
     * @param  TicketType  $ticketType
     * @return JsonResponse
     */
    public function show(TicketType $ticketType): JsonResponse
    {
        return response()->json(new TicketTypeResource($ticketType));
    }

    
    /**
     * Remove the specified resource from storage.
     *
     * @param  TicketType  $ticketType
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy(TicketType $ticketType): JsonResponse
    {
        
        if ($ticketType->delete()) {
            return response()->json(['message' => 'Data deleted successfully']);
        }
        return response()->json(['message' => __('An error occurred while deleting data')], 500);
    }
}
