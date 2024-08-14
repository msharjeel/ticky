<?php

namespace App\Http\Resources\Department;

use App\Models\DepartmentLocation;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DepartmentLocationSelectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        /** @var Department $deprtmentLocation */
        $deprtmentLocation = $this;
        // dd($deprtmentLocation);
        if( !empty($deprtmentLocation->id) ){
            return [
                'id' => $deprtmentLocation->id,
                'department_id' => $deprtmentLocation->department_id,
                'location_name' => $deprtmentLocation->location_name,
                'location_latitude' => $deprtmentLocation->location_latitude,
                'location_longitude'=> $deprtmentLocation->location_longitude,
            ];
        }
        else{
            return null;
        }
    }
}
