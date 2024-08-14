<?php

namespace App\Http\Resources\Department;

use App\Models\DepartmentLocation;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DepartmentLocationResource2 extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        /** @var Location $location */
        $response = [
            'id' => $this->id,
            'department_id' => $this->department_id,
            'parent_id' => $this->parent_id,
            'location_name' => $this->location_name,
            'location_latitude' => $this->location_latitude,
            'location_longitude' => $this->location_longitude,
            'location_category' => $this->location_category,
        ];

        $response['department'] = $this->department;
        
        $response['parentLocation'] = $this->parentLocation;
        $response['subLocations'] = $this->subLocations->toArray();


        return $response;
    }
}
