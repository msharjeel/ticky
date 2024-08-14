<?php

namespace App\Http\Controllers\Api\Dashboard\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Admin\DepartmentLocation\StoreLocationRequest;
use App\Http\Requests\Dashboard\Admin\DepartmentLocation\UpdateLocationRequest;
use App\Http\Resources\Department\DepartmentLocationResource2;
use App\Http\Resources\User\UserDetailsResource;
use App\Models\Department;
use App\Models\DepartmentLocation;
use App\Models\Ticket;
use App\Models\User;
use App\Models\UserRole;
use App\Models\LocationCategory;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DepartmentLocationsController extends Controller
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
    public function index(Request $request): JsonResponse
    {
        
        $locations = DepartmentLocation::orderBy('id', 'desc');

        if( !empty($request->id) ){
            $locations = $locations->where('id', $request->id);
        }

        if( !empty($request->parent_id) ){
            $locations = $locations->where('parent_id', $request->parent_id);
        } else{
            $locations = $locations->whereNull('parent_id');
        }

        if( !empty($request->department_id) ){
            $locations = $locations->where('department_id', '=', $request->department_id);
        }
        
        $locations = $locations->with(['department', 'parentLocation', 'subLocations']);
        $locations = $locations->get();
        
        // dd($locations);

        $locations =   response()->json(
                            DepartmentLocationResource2::collection(
                                $locations
                            )
                        );
        

        return $locations;
    }

    
    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreLocationRequest  $request
     * @return JsonResponse
     */
    public function store(StoreLocationRequest $request): JsonResponse
    {
        $request->validated();
        
        $input = $request->only(
                    'department_id',
                    'location_name',
                    'location_latitude',
                    'location_longitude'
                );

        $location = null;
        $id = !empty($request->id) ? $request->id : 0;
        if( $id == 0 ){
            $location = new DepartmentLocation();            
        }
        else{
            // $updateResponse = DepartmentLocation::update($input)->where('id', '=', $id);
            $location = DepartmentLocation::find($id);
            // $location->location_name = $input['location_name'];
            // dd(['edit', $locations]);
        }

        if( !empty($request->parent_id) ){
            $input['parent_id'] = $request->parent_id;
        }

        if( !empty($request->location_category) ){
            $input['location_category'] = $request->location_category;
        }

        foreach ($input as $key => $value) {
            $location[$key] = $value;
        }


        

        // dd(['88-',  $input, $locations]);

        if ($location->save()) {
            return response()->json(['success'=>1, 'message' => 'Location saved successfully.', 'location' => new DepartmentLocationResource2($location)]);
        }
        return response()->json(['success'=>0, 'message' => __('An error occurred while saving data')], 500);
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function categoryListing(Request $request): JsonResponse
    {
        
        $response = [];
        $response['bool'] = 1;
        $response['message'] = "";

        $response['data'] = LocationCategory::orderBy('id', 'asc')->get();

        return response()->json($response);
    }



    /**
     * Display the specified resource.
     *
     * @param  DepartmentLocation  $location
     * @return JsonResponse
     */
    public function show(DepartmentLocation $location): JsonResponse
    {
        return response()->json(new DepartmentLocationResource2($location));
    }

    

    /**
     * Remove the specified resource from storage.
     *
     * @param  DepartmentLocation  $location
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy(DepartmentLocation $location): JsonResponse
    {
        if ($location->delete()) {
            return response()->json(['success'=>1, 'message' => 'Data deleted successfully']);
        }
        return response()->json(['success'=>0, 'message' => __('An error occurred while deleting data')], 500);
    }

    public function departmentLocationsDelete(Request $request): JsonResponse
    {
        $id = !empty($request->id) ? $request->id : 0;
        $location = null;
        $msg = '';
        if( $id != 0 ){
            $location = DepartmentLocation::find($id);

            if ($location->delete()) {
                $msg = 'Data deleted successfully';                
                return response()->json(['success'=>1, 'message' => $msg]);
            }
            else{
                $msg =  __('An error occurred while deleting data');
            }
        }else{
            $msg = 'Location not found';
        }

        
        return response()->json(['success'=>0, 'message' =>$msg], 500);
    }
    

    
}
