<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\Setting;
use App\Models\Ticket;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\User\UserResource;
use App\Helpers\Helper;
use Illuminate\Database\Eloquent\Builder;

class StatsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function count(): JsonResponse
    {
        $open_tickets = 0;
        $pending_tickets = 0;
        $solved_tickets = 0;
        $without_agent = 0;

        $user = auth()->user();
        if( !empty($user->id) ){

            $uData = [];
            $uData['user'] = $user;            
            $uData['CAN_MANAGE_TICKETS'] = Helper::userHasPermission($uData['user'], "CAN_MANAGE_TICKETS");
            $uData['id'] = $user->id;

            $is_client = Helper::userIs('CLIENT', $user);
            $is_admin = Helper::userIs('ADMIN', $user);
            // $is_operator = Helper::userIs('OPERATOR', $user);
            // $is_engineer = Helper::userIs('ENGINEER', $user);

            



            $mainQuery = Ticket::where('id', '>', 0);
            if($is_client || $is_admin){
                //fetch all tickets
            }
            else if(true){//run for all type of user
                $mainQuery->where(function (Builder $query) use ($uData) {
                    $query->where('created_by', $uData['id']);//assigned to
                    // $query->orWhereIn('department_id', $uData['userDepartmentsIdsArray']);

                    if( $uData['CAN_MANAGE_TICKETS'] ){
                        $query->orWhere('user_id', $uData['id']);
                        $query->orWhere('agent_id', $uData['id']);//who had assigned the ticket to user_id
                        $query->orWhere('closed_by', $uData['id']);
                    }
                });
            }
            
            $open_tickets = (clone $mainQuery)->where('status_id', 1)->count();
            $pending_tickets = (clone $mainQuery)->where('status_id', 2)->count();
            $solved_tickets = (clone $mainQuery)->whereIn('status_id', [4])->count();
            $without_agent = (clone $mainQuery)->whereNull('agent_id')->count();
        }



        return response()->json([
                'open_tickets' => $open_tickets,
                'pending_tickets' => $pending_tickets,
                'solved_tickets' => $solved_tickets,
                'without_agent' => $without_agent,
            ]);
    }

    public function registeredUsers(): JsonResponse
    {
        $graph = [];
        $month = 1;
        while ($month <= 12) {
            $graph[] = User::whereMonth('created_at', '=', $month)->count();
            $month++;
        }
        return response()->json($graph);
    }

    public function openedTickets(): JsonResponse
    {
        $graph = [];
        $month = 1;
        while ($month <= 12) {
            $graph[] = Ticket::whereMonth('created_at', '=', $month)->count();
            $month++;
        }
        return response()->json($graph);
    }
}
