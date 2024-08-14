<?php

namespace App\Http\Controllers\Api\Dashboard\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Admin\Department\StoreRequest;
use App\Http\Requests\Dashboard\Admin\Department\UpdateRequest;
use App\Http\Resources\Department\DepartmentDetailsResource;
use App\Http\Resources\Department\DepartmentResource;
use App\Http\Resources\User\UserDetailsResource;
use App\Models\Department;
use App\Models\Ticket;
use App\Models\User;
use App\Models\UserRole;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use App\Helpers\Helper;

class DepartmentController extends Controller
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
        $department = Department::where('parent_id', '=', 0);
        
        $department = $department->with([
                            'subDepartments',
                            // 'departmentLocation'
                            'departmentLocation' => function($locationsQ){
                                return $locationsQ->whereNull('parent_id');
                            }
                    ]);

        $department = $department->get();
        // dd($department);

        $department =   response()->json(
                            DepartmentDetailsResource::collection(
                                $department
                            )
                        );
        
        // dd($department);

        return $department;
    }

    /**
     * Display child of department
     *
     * @return JsonResponse
     */
    public function subDepartmentsOf(Request $request, $parent_id): JsonResponse
    {
        return response()->json(DepartmentDetailsResource::collection(Department::where('parent_id', '=', $parent_id)->get()));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreRequest  $request
     * @return JsonResponse
     */
    public function store(StoreRequest $request): JsonResponse
    {
        $request->validated();
        $department = new Department();
        $department->name = $request->get('name');
        $department->all_agents = $request->get('all_agents');
        if( isset($request->parent_id) ){            
            $department->parent_id = $request->get('parent_id') ?? '';
        }

        $department->public = $request->get('public');
        $agents = [];
        if (!$department->all_agents) {
            $agents = $request->get('agents');
        }
        if ($department->save()) {
            $department->agent()->sync($agents);
            return response()->json(['message' => 'Data saved correctly', 'department' => new DepartmentResource($department)]);
        }
        return response()->json(['message' => __('An error occurred while saving data')], 500);
    }

    /**
     * Display the specified resource.
     *
     * @param  Department  $department
     * @return JsonResponse
     */
    public function show(Department $department): JsonResponse
    {
        return response()->json(new DepartmentResource($department));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateRequest  $request
     * @param  Department  $department
     * @return JsonResponse
     */
    public function update(UpdateRequest $request, Department $department): JsonResponse
    {
        // UpdateRequest
        
        // echo __LINE__; dd( [$request->name,$request->get('name')] );
        $request->validated();
        $department->name = $request->get('name');
        $department->all_agents = $request->get('all_agents');
        $department->public = $request->get('public');
        if( isset($request->parent_id) ){            
            $department->parent_id = $request->get('parent_id') ?? '';
        }
        $agents = [];
        if (!$department->all_agents) {
            $agents = $request->get('agents');
        }

        $new_logo = false;
        if ($request->file('department_logo')) {
            $new_logo = $request->file('department_logo')->store('logos_of_departments', 'public');            
        } elseif ($request->get('gravatar') === 'true') {
            $new_logo = null;
        }
            
        if( $new_logo !== false ){
            if(!empty($department->logo) && $department->logo != 'gravatar'){
                Helper::deleteFile($department->logo);
            }
            $department->logo = $new_logo;
        }


        if ($department->save()) {
            $department->agent()->sync($agents);
            return response()->json(['message' => 'Data updated correctly', 'department' => new DepartmentResource($department)]);
        }
        return response()->json(['message' => __('An error occurred while saving data')], 500);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Department  $department
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy(Department $department): JsonResponse
    {
        Ticket::where('department_id', $department->id)->update(['department_id' => null]);
        $department->agent()->sync([]);
        if ($department->delete()) {
            return response()->json(['message' => 'Data deleted successfully']);
        }
        return response()->json(['message' => __('An error occurred while deleting data')], 500);
    }

    public function users()
    {
        $roles = UserRole::where('dashboard_access', true)->pluck('id');
        $agents = User::whereIn('role_id', $roles)->where('status', true)->get();
        return response()->json(UserDetailsResource::collection($agents));
    }
}
