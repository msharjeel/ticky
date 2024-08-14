<?php

namespace App\Http\Controllers\Api\Dashboard\Admin;

use App\Http\Controllers\Controller;
// use App\Http\Requests\Dashboard\Admin\PreventiveMaintenance\StoreRequest;
// use App\Http\Requests\Dashboard\Admin\PreventiveMaintenance\UpdateRequest;
// use App\Http\Resources\PreventiveMaintenance\PreventiveMaintenanceResource;

use App\Models\PreventiveMaintenance;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PreventiveMaintenanceController extends Controller
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
        return response()->json( PreventiveMaintenance::all() );
    }

    public function get_pm_row($input){
        $query = PreventiveMaintenance::where('id', '>', 0);
        if( !empty($input['department_id']) ){
            $query = $query->where('department_id', $input['department_id']);
        }
        if( !empty($input['department_location_id']) ){
            $query = $query->where('department_location_id', $input['department_location_id']);
        }
        if( !empty($input['department_sub_location_id']) ){
            $query = $query->where('department_sub_location_id', $input['department_sub_location_id']);
        }

        $row = $query->first();
        return $row;
    }

    public function preventive_maintenance_fetch(Request $request)
    {
        // dd($request->input());

        $data = $this->get_pm_row($request->input());
            
        return response()->json([
            'success' => 1,
            'data' => $data
        ]);
    }


    public function preventive_maintenance_save(Request $request)
    {
        $success = 0;
        $message = "";
        $row = null;

        // dd($request->input());
        $requires = ['department_id','department_location_id','department_sub_location_id'];
        $all_fields = ['prev_maint_1_done','prev_maint_2_done','prev_maint_3_done','prev_maint_4_done',
'prev_maint_1_date','prev_maint_2_date','prev_maint_3_date','prev_maint_4_date'];

        $all_fields = array_merge($all_fields, $requires);

        
        $input = $request->only($all_fields);
        // $input = $request->input();
        // dd($input);
        $erros = [];
        $data = [];

        foreach ($all_fields as $key) {
            if( !empty($input[$key]) ){
                $data[$key] = $input[$key];
            }
            else{
                $data[$key] = null;
            }         
        }

        foreach ($requires as $key) {
            if( empty($input[$key]) ){
                $erros[] = "Pleaes select value of ".$key;
            }
        }

        
        if( count($erros) > 0){
            $message = implode(',', $erros);
        }
        else{
            // dd( $data );
            $row = $this->get_pm_row($request->input());
            if( !empty($row->id) ){
                $row->update($data);
                $message = "Update successfully.";
            }
            else{
                $row  = PreventiveMaintenance::create($data);
                $message = "Created successfully.";
            }
            $success = 1;
        }
        
            
        return response()->json([
            'success' => $success,
            'message' => $message,
            'data' => $row
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $request->validated();
        $PreventiveMaintenance = new PreventiveMaintenance();
        $PreventiveMaintenance->name = $request->get('name');
        $PreventiveMaintenance->color = $request->get('color');
        if ($PreventiveMaintenance->save()) {
            return response()->json(['message' => 'Data saved correctly', 'PreventiveMaintenance' => new PreventiveMaintenanceResource($PreventiveMaintenance)]);
        }
        return response()->json(['message' => __('An error occurred while saving data')], 500);
    }

    /**
     * Display the specified resource.
     *
     * @param  PreventiveMaintenance  $PreventiveMaintenance
     * @return JsonResponse
     */
    public function show(PreventiveMaintenance $PreventiveMaintenance): JsonResponse
    {
        return response()->json(new PreventiveMaintenanceResource($PreventiveMaintenance));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  PreventiveMaintenance  $PreventiveMaintenance
     * @return JsonResponse
     */
    public function update(Request $request, $id): JsonResponse
    {
        $request->validated();
        $PreventiveMaintenance->name = $request->get('name');
        $PreventiveMaintenance->color = $request->get('color');
        if ($PreventiveMaintenance->save()) {
            return response()->json(['message' => 'Data updated correctly', 'PreventiveMaintenance' => new PreventiveMaintenanceResource($PreventiveMaintenance)]);
        }
        return response()->json(['message' => __('An error occurred while saving data')], 500);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  PreventiveMaintenance  $PreventiveMaintenance
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy($id): JsonResponse
    {
        $PreventiveMaintenance->tickets()->sync([]);
        if ($PreventiveMaintenance->delete()) {
            return response()->json(['message' => 'Data deleted successfully']);
        }
        return response()->json(['message' => __('An error occurred while deleting data')], 500);
    }
}
