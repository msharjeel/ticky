<?php

namespace App\Http\Resources\Department;

use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DepartmentResource extends JsonResource
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
            'all_agents' => $department->all_agents,
            'public' => $department->public,
            'agents' => $department->agent()->pluck('id'),
            'subDepartments' => $department->subDepartments->toArray(),

            'logo' => $department->getLogo(),
            'gravatar' => $department->getGravatar(),
        ];
    }
}
