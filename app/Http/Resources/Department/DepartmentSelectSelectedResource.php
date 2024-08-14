<?php

namespace App\Http\Resources\Department;

use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DepartmentSelectSelectedResource extends JsonResource
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
        return [
            'id' => $department->id,
            'name' => $department->name,
            'parent_id' => $department->parent_id,
            // 'subDepartments' => $department->subDepartments->toArray(),
            // 'departmentLocation'=> $department->departmentLocation->toArray(),
            // 'departmentLocation'=> new DepartmentLocationSelectResource($department->location_id),
        ];
    }
}
