<?php

namespace App\Http\Resources\Status;

use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StatusResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        /** @var Status $status */
        $status = $this;
        return [
            'id' => $status->id,
            'name' => $status->name,
            'allowed_for_status_change' => $status->allowed_for_status_change ?? 0,
            'created_at' => isset($status->created_at) ? $status->created_at->toISOString() : ""
        ];
    }
}
