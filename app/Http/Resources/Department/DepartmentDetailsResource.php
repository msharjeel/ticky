<?php

namespace App\Http\Resources\Department;

use App\Http\Resources\User\UserDetailsResource;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DepartmentDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        /** @var Department $department */
        $department = $this;
        // dd($department->subDepartments);
        $response = [
            'id' => $department->id,
            'name' => $department->name,
            'parent_id' => $department->parent_id,
            'all_agents' => (bool) $department->all_agents,
            'public' => (bool) $department->public,
            'agents' => !$department->all_agents ? UserDetailsResource::collection($department->agent->take(5)) : []
        ];

        
        $response['subDepartments'] = [];
        $response['subDepartments'] = $department->subDepartments->toArray();

        $response['departmentLocation'] = [];
        $response['departmentLocation'] = $department->departmentLocation->toArray();
        
        // if( $department->has('subDepartments') )

        // $response['subDepartmentsCount'] = 0;
        // $response['subDepartmentsCount'] = $department->subDepartmentsCount;
        return $response;
    }
}
