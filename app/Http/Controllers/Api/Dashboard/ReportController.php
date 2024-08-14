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
use App\Models\PreventiveMaintenance;
use App\Models\Label;
use App\Models\LocationCategory;
use App\Models\Priority;
use App\Models\Setting;
use App\Models\Status;
use App\Models\Ticket;
use App\Models\TicketReply;
use App\Models\User;
use App\Models\UserRole;
use App\Notifications\Ticket\NewTicketFromAgent;
use App\Notifications\Ticket\NewTicketReplyFromAgentToUser;
use Auth;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Str;
use Throwable;
use DB;
use App\Helpers\Helper;

class ReportController extends Controller
{
    public $defaultColorCodes = [
            '#A93226', '#DFFF00', '#FFBF00', '#FF7F50', '#DE3163', '#9FE2BF', '#40E0D0', '#6495ED', '#CCCCFF', '#FF0000', '#00FF00', '#0000FF', '#800080', '#FF00FF', '#000080', '#0000FF'
        ];

    public function __construct()
    {
        $this->middleware('auth:sanctum', ['except' => ['ticketsReports', 'ticketsReportSummary', 'ticketsReportKPI']]) ;
        // $this->middleware('auth:sanctum');
    }

    private function getAllStatusRowMapOnID(){
        $all_status_rows_on_id = [];
        $rows = Status::all();
        foreach($rows as $row){
            $all_status_rows_on_id[$row->id] = $row;
        }
        return $all_status_rows_on_id;
    }
    private function getTicketStatusInfo($status_id = 1, $color='#1a8fec', &$main_query, &$all_status_rows_on_id){
        $tObj = [];
        $tObj['label'] = $all_status_rows_on_id[$status_id]->name;
        $tObj['color'] = $color;
        $tObj['count'] = (clone $main_query)->where('status_id', $status_id)->count();        
        return $tObj;
    }


    /**
     * Display reports with filters
     *
     * @param  Request  $request
     * @return JsonResponse
     * @throws Exception
     */
    public function ticketsReports(Request $request)
    {
        $filters = $request->input();
        $response = [];
        $response['bool'] = true;
        $response['message'] = "";
        $response['data'] = [];

        $main_query = Ticket::where('id','>', 0);

        if( !empty($filters['location_category_id']) ){
            $main_query =   $main_query->whereHas('departmentSubLocation', function($dslc) use($filters){
                                 $dslc->where('location_category', $filters['location_category_id']);
                            });
        }

        if( !empty($filters['priority_id']) ){
            $main_query = $main_query->where('priority_id', $filters['priority_id']);
        }

        if( !empty($filters['department_id']) ){
            $main_query = $main_query->where('department_id', $filters['department_id']);
        }

        if( !empty($filters['sub_department_id']) ){
            $main_query = $main_query->where('sub_department_id', $filters['sub_department_id']);
        }

        //who created ticket
        if( !empty($filters['user_id']) ){
            $main_query = $main_query->where('user_id', $filters['user_id']);
        }

        //ticket for agent
        if( !empty($filters['agent_id']) ){
            $main_query = $main_query->where('agent_id', $filters['agent_id']);
        }

        if( !empty($filters['report_from']) && !empty($filters['report_till']) ){
            if( strlen($filters['report_from']) == 10 && strlen($filters['report_till']) == 10 ){
                $filters['report_from'] = $filters['report_from']." 00:00:00";
                $filters['report_till'] = $filters['report_till']." 23:59:59";
                $main_query = $main_query->whereBetween('created_at', [$filters['report_from'], $filters['report_till']]);
            }
        }

        if( !empty($filters['department_location_id']) ){
            $main_query = $main_query->where('department_location_id', $filters['department_location_id']);
        }

        if( !empty($filters['department_sub_location_id']) ){
            $main_query = $main_query->where('department_sub_location_id', $filters['department_sub_location_id']);
        }

        if( !empty($filters['ticket_type_id']) ){
            $main_query = $main_query->where('ticket_type_id', $filters['ticket_type_id']);
        }


        $all_status_rows_on_id = $this->getAllStatusRowMapOnID();

        $all_total = (clone $main_query)->count();

        // --------- ticket_all ---------
        $tObj = [];
        $tObj['label'] = 'All Faults';
        $tObj['color'] = '#59d8a3';
        $tObj['count'] = $all_total;        
        $response['data']['ticket_all'] = $tObj;


        // --------- 1-New Faults ---------
        $response['data']['ticket_new'] = $this->getTicketStatusInfo(1, '#1a8fec', $main_query, $all_status_rows_on_id);

        // --------- 2-Pending Faults ---------
        $response['data']['ticket_pending'] = $this->getTicketStatusInfo(2, '#fb9678', $main_query, $all_status_rows_on_id);
        
        // --------- 3-In Progress ---------
        $response['data']['ticket_in_progress'] = $this->getTicketStatusInfo(3, '#1a8fec', $main_query, $all_status_rows_on_id);
        
        // --------- 4-Closed Faults ---------
        $response['data']['ticket_close'] = $this->getTicketStatusInfo(4, '#fc0017', $main_query, $all_status_rows_on_id);


        if( empty($filters['agent_id']) ){
            // --------- Assigned Faultss ---------
            $tObj = [];
            $tObj['label'] = 'Assigned Faults';
            $tObj['color'] = '#745af3';
            $tObj['count'] = (clone $main_query)->whereNotNull('agent_id')->count();
            $response['data']['ticket_assign'] = $tObj;

            // --------- UnAssign Faultss ---------
            $tObj = [];
            $tObj['label'] = 'UnAssign Faults';
            $tObj['color'] = '#d8b654';
            $tObj['count'] = (clone $main_query)->whereNull('agent_id')->count();
            $response['data']['ticket_un_assign'] = $tObj;
        }






        //set percentage of all tickets
        foreach($response['data'] as $key=>$tObj){
            $tObj['percent'] = 0;
            if($all_total > 0){
                $tObj['percent'] = $tObj['count'] / $all_total * 100;
                $tObj['percent'] = round($tObj['percent'], 2);
            }

            $response['data'][$key] = $tObj;
        }

        return response()->json($response);
    }


    /**
     * Display reports with filters
     *
     * @param  Request  $request
     * @return JsonResponse
     * @throws Exception
     */

    ////// Work has been stop, due to no need of it ///
    public function ticketsReportSummary(Request $request)
    {
        $filters = $request->input();
        $response = [];
        $response['bool'] = true;
        $response['message'] = "";
        $response['data'] = [];

        $main_query = Ticket::where('id','>', 0);

        $all_total = (clone $main_query)->count();

        // --------- ticket_all ---------
        $tObj = [];
        $tObj['label'] = 'All Faultss';
        $tObj['color'] = '#59d8a3';
        $tObj['count'] = $all_total;        
        $response['data']['ticket_all'] = $tObj;


        $resp = (clone $main_query)->groupBy(['department_id', 'sub_department_id', 'department_location_id', 'department_sub_location_id'])
            // ;
            ->get();

        dd($resp);

        // $tObj = [];
        // $tObj['label'] = 'SITE-NAME';
        // $tObj['color'] = '#59d8a3';
        // $tObj['count'] = $all_total;
        // $response['data']['ticket_of_site_'.$site_id] = $tObj;


        return response()->json($response);
    }    

    /**
     * Display reports with filters
     *
     * @param  Request  $request
     * @return JsonResponse
     * @throws Exception
     */
    public function ticketsReportKPI_OLD(Request $request)
    {
        $filters = $request->input();
        $response = [];
        $response['bool'] = true;
        $response['message'] = "";
        $response['data'] = [];

        // $main_query = Ticket::where('id','>', 0);

        // if( !empty($filters['location_category_id']) ){
        //     $main_query =   $main_query->whereHas('departmentSubLocation', function($dslc) use($filters){
        //                          $dslc->where('location_category', $filters['location_category_id']);
        //                     });
        // }

        // if( !empty($filters['priority_id']) ){
        //     $main_query = $main_query->where('priority_id', $filters['priority_id']);
        // }

        

        // //who created ticket
        // if( !empty($filters['user_id']) ){
        //     $main_query = $main_query->where('user_id', $filters['user_id']);
        // }

        // //ticket for agent
        // if( !empty($filters['agent_id']) ){
        //     $main_query = $main_query->where('agent_id', $filters['agent_id']);
        // }

        $main_query = PreventiveMaintenance::where('id','>', 0);


        if( !empty($filters['department_id']) ){
            $main_query = $main_query->where('department_id', $filters['department_id']);
        }

        if( !empty($filters['sub_department_id']) ){
            $main_query = $main_query->where('sub_department_id', $filters['sub_department_id']);
        }

        if( !empty($filters['department_location_id']) ){
            $main_query = $main_query->where('department_location_id', $filters['department_location_id']);
        }

        if( !empty($filters['department_sub_location_id']) ){
            $main_query = $main_query->where('department_sub_location_id', $filters['department_sub_location_id']);
        }

        $main_query->with([
            'subDepartmentLocation',
            'departmentLocation',
            'subDepartment',
            'department' => function($qDep) use ($filters) {
                return $qDep->with([
                    'tickets' => function($qDeTickets) use ($filters) {

                        if( !empty($filters['department_id']) ){
                            $qDeTickets = $qDeTickets->where('department_id', $filters['department_id']);
                        }

                        if( !empty($filters['sub_department_id']) ){
                            $qDeTickets = $qDeTickets->where('sub_department_id', $filters['sub_department_id']);
                        }

                        if( !empty($filters['department_location_id']) ){
                            $qDeTickets = $qDeTickets->where('department_location_id', $filters['department_location_id']);
                        }

                        if( !empty($filters['department_sub_location_id']) ){
                            $qDeTickets = $qDeTickets->where('department_sub_location_id', $filters['department_sub_location_id']);
                        }

                        if( !empty($filters['report_from']) && !empty($filters['report_till']) ){
                            if( strlen($filters['report_from']) == 10 && strlen($filters['report_till']) == 10 ){
                                $filters['report_from'] = $filters['report_from']." 00:00:00";
                                $filters['report_till'] = $filters['report_till']." 23:59:59";
                                $qDeTickets = $qDeTickets->whereBetween('created_at', [$filters['report_from'], $filters['report_till']]);
                            }
                        }

                        return $qDeTickets;
                    }
                ]);
            }
        ]);




        $response['all_rows'] = (clone $main_query)->get();
        $all_total = (clone $main_query)->count();

        // --------- ticket_all ---------
        $ccI = 0;
        foreach($response['all_rows'] as $row){
            if( !empty($row->department) ){
                $tObj = [];
                $tObj['label'] = $row->department->name;
                // dd($row);
                if( !empty($row->subDepartment) ){    
                    $tObj['label'] .= ', '.$row->subDepartment->name;
                }
                if( !empty($row->departmentLocation) ){    
                    $tObj['label'] .= ', '.$row->departmentLocation->location_name;
                }
                if( !empty($row->subDepartmentLocation) ){    
                    $tObj['label'] .= ', '.$row->subDepartmentLocation->location_name;
                }


                $ccI++; if( !isset($this->defaultColorCodes[$ccI]) ){ $ccI = 0; }

                $tObj['color'] = $this->defaultColorCodes[$ccI];

                $tObj['percent'] = $tObj['count'] = $row->department->tickets->count();
                
                $response['data'][] = $tObj;
            }
        }

        return response()->json($response);
    }


    public function ticketsReportKPI(Request $request)
    {
        $filters = $request->input();
        $response = [];
        $response['bool'] = true;
        $response['days_diff'] = $this->daysDiff($filters['report_from'], $filters['report_till']);
        $response['message'] = "";
        $response['data'] = [];

        $main_query_clone = null;
        $main_query = PreventiveMaintenance::orderBy('department_id');


        if( !empty($filters['department_id']) ){
            $main_query = $main_query->where('department_id', $filters['department_id']);
        }

        

        if( !empty($filters['department_location_id']) ){
            $main_query = $main_query->where('department_location_id', $filters['department_location_id']);
        }

        

        $main_query_clone = (clone $main_query);

        $main_query->with([
            'departmentLocation',
            'department'
        ]);

        $groupBy = ['department_id','department_location_id'];
        $main_query = $main_query->groupBy($groupBy);

        $response['data'] = $main_query->get();
        // --------- ticket_all ---------
        
        $ccI = 0;
        $colorI = 0;
        foreach($response['data'] as $row){
            if( !empty($row->department) ){
                $label = $row->department->name;
                if( !empty($row->departmentLocation) ){    
                    $label .= ', '.$row->departmentLocation->location_name;
                }
                
                $color = "";
                
                $graph_data = [];              
                foreach([1,2,3,4] as $i){
                    $colorI++; if( !isset($this->defaultColorCodes[$colorI]) ){ $colorI = 0; }
                    $color = $this->defaultColorCodes[$colorI];

                    $graph_data['main_'.$i] = $this->findCountFroGraph( $i, $label, $color, $filters, $row, (clone $main_query_clone) );
                }

                $row->graph_data = $graph_data;

                $date_range_days = $response['days_diff'];

                //date range must have 1 day
                $date_range_days = $date_range_days > 1 ? $date_range_days : 1;
                $per_day_ticket_threshold = Helper::getValueFromSettings('kpi_value');

                $pie_graph_data = json_decode(json_encode($graph_data), true);
                foreach([1,2,3,4] as $i){
                    $tempGR = $pie_graph_data['main_'.$i];

                    $tempGR['percent'] = ($tempGR['count'] * 100) / ($date_range_days * $per_day_ticket_threshold);
                    $tempGR['percent'] = round($tempGR['percent'], 2);//get 2 digits after round

                    $pie_graph_data['main_'.$i] = $tempGR;
                }

                $row->graph_2_data = $pie_graph_data;

                $row->label = $label;
                $row->color = $color;
                

            }
        }
        

        return response()->json($response);
    }

    function daysDiff($date1, $date2){
        $time1 = strtotime($date1);
        $time2 = strtotime($date2);
        $datediff = $time1 - $time2;
        $datediff = abs($datediff);

        return round($datediff / (60 * 60 * 24));
    }


    public function findCountFroGraph($i, $label, $color, $filters, $row, $mainQuery){
        
        $sum_on_column = 'prev_maint_'.$i.'_done';
        $date_check_on_column = 'prev_maint_'.$i.'_date';

        $tObj = [];
        $tObj['label'] = 'Maint-'.$i.' of '.$label;
        $tObj['color'] = $color;
        // $tObj['color'] = $this->defaultColorCodes[$i];
        if( !empty($filters['report_from']) && !empty($filters['report_till']) ){
            if( strlen($filters['report_from']) == 10 && strlen($filters['report_till']) == 10 ){
                $filters['report_from'] = $filters['report_from']." 00:00:00";
                $filters['report_till'] = $filters['report_till']." 23:59:59";
                $mainQuery = $mainQuery->whereBetween($date_check_on_column, [$filters['report_from'], $filters['report_till']]);
            }
        }

        // dd($row);
        if( !empty($row->department_id) ){
            $mainQuery = $mainQuery->where('department_id', $row->department_id);
        }

        if( !empty($row->department_location_id) ){
            $mainQuery = $mainQuery->where('department_location_id', $row->department_location_id);
        }

        


        $groupBy = ['department_id','department_location_id'];        
        $tObj['percent'] = $tObj['count'] = $mainQuery->sum($sum_on_column);

        return $tObj;
    }
}
