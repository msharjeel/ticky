<?php

namespace App\Http\Resources\User;

use App\Http\Resources\UserRole\UserRoleResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Helpers\Helper;

class UserResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        /** @var User $user */
        $user = $this;
        $department = $user->department;
        if(!empty($department) ){
            $department->logo = $department->getLogo();
            $department->gravatar = $department->getGravatar();
        }
        
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'mobile_number' => $user->mobile_number,
            'user_from' => $user->user_from,
            'avatar' => $user->getAvatar(),
            'gravatar' => $user->getGravatar(),
            'permissions' => Helper::getPermissions($user),
            'role' => new UserRoleResource($user->userRole),
            'role_id' => $user->role_id,
            'department_id' => $user->department_id,
            'department' => $department,
            'notified_for_password_change' => $user->notified_for_password_change,
            'status' => (bool) $user->status,
            'created_at' => $user->created_at->toISOString(),
            'updated_at' => $user->updated_at->toISOString()
        ];
    }
}
