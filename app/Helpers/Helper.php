<?php

namespace App\Helpers;

use Storage;

class Helper{
	public static function getStatusIds($type){
		$ids = [];
		if($type == 'NEW'){
			$ids[] = 1;	
		}
		return $ids;
	}

	//Response is in array, because in future if customer going to create new priorities with little bit change of name but behaviour they are looking for repeative then this logic can be helpfull
	public static function getPriorityIds($type){
		$ids = [];
		if($type == 'Low'){
			$ids = [1];	
		}
		else if($type == 'Medium'){
			$ids = [2];	
		}
		else if($type == 'High'){
			$ids = [3];	
		}
		else if($type == 'Urgent'){
			$ids = [4];	
		}
		return $ids;
	}

	public static function getPriorityExpiryTime($type){
		$format = 'Y-m-d h:i:s';
		$dateTime = Date($format);
		
		$hours = 3600;

		$testing = 0;
		if($testing == 1){
			$hours = 30;//24,12,6,1mint in testing with this logic
		}

		if($type == 'Low'){			
			$dateTime = Date($format, (time() - (48 * $hours)));
		}
		else if($type == 'Medium'){			
			$dateTime = Date($format, (time() - (24 * $hours)));
		}
		else if($type == 'High'){			
			$dateTime = Date($format, (time() - (12 * $hours)));
		}
		else if($type == 'Urgent'){			
			$dateTime = Date($format, (time() - (2 * $hours)));
		} 

		return $dateTime;
	}

	//CAN_CREATE_NEW_TICKET app.php
	//static: role permissions
	//Dynamic: role and user associated permissions
	public static function getPermissions($user){
		$permissions = $user->userRole->permissions;
		$permissions = ($permissions == null || empty($permissions)) ? [] : json_decode($permissions);


		$user_role_group = config('app.user_role_group');


		//Can show customer dropdown, while creating new ticket
		//NEW_TICKET__CAN_SHOW_CUSTOMERS_DROP_DOWN -- start |
		$CAN_SHOW_CUSTOMERS_DROP_DOWN__allowed_to_roles = array_merge(
			$user_role_group['ADMIN'],
			$user_role_group['ENGINEER'],
			$user_role_group['OPERATOR'],
		);
		if( in_array($user->role_id, $CAN_SHOW_CUSTOMERS_DROP_DOWN__allowed_to_roles) ){
			$permissions[] = "NEW_TICKET__CAN_SHOW_CUSTOMERS_DROP_DOWN";
		}
		//NEW_TICKET__CAN_SHOW_CUSTOMERS_DROP_DOWN -- end |
		return $permissions;
	}

	public static function userHasPermission($user, $permissionName){
		$permissions = Helper::getPermissions($user);
		return in_array($permissionName, $permissions);
	}

	//$is_admin = Helper::userIs('ADMIN', $user);
	//$is_operator = Helper::userIs('OPERATOR', $login_user);
    //$is_engineer = Helper::userIs('ENGINEER', $login_user);
	public static function userIs($roleType, $user){
		$is = false;

		$user_role_group = config('app.user_role_group');
		
		if( isset($user_role_group[$roleType]) ){
			$is = in_array($user->role_id, $user_role_group[$roleType]);
		}
		return $is;
	}


	public static function roleCanUseStatus($all_status, $login_user){
		foreach($all_status as $status){
			$status->allowed_for_status_change = Helper::roleCanUseThisStatus($status->id, $login_user);
		}
		return $all_status;
	}

	public static function roleCanUseThisStatus($statusId, $login_user){
		$allowed_for_status_change = 1;
		$STATUS_ID_OF_RESOLVED = 4;
		if( in_array($statusId, [$STATUS_ID_OF_RESOLVED]) ){
			$allowed_for_status_change = 0;
			if( Helper::userIs('ENGINEER', $login_user) ){
				$allowed_for_status_change = 1;
			}	
		}
		return $allowed_for_status_change;
	}

	public static function getTicketStakeHolderIds($ticket){
        $ticketIdStakeHolderIds = [];
        // $ticketIdStakeHolderIds = \App\Models\TicketReply::where('ticket_id', '=', $ticket->id)->distinct()->pluck('user_id')->toArray();
        $ticketIdStakeHolderIds[] = $ticket->created_by;
        $ticketIdStakeHolderIds[] = $ticket->closed_by;
        $ticketIdStakeHolderIds[] = $ticket->agent_id;
        $ticketIdStakeHolderIds[] = $ticket->user_id;
        $ticketIdStakeHolderIds = array_unique($ticketIdStakeHolderIds);

        return $ticketIdStakeHolderIds;
    }

    public static function getValueFromSettings($key){
		return \App\Models\Setting::getDecoded($key);
	}

	public static function deleteFile($filePathInPublicStorage){
		$deleted = false;
		$path = storage_path().'/app/public/'.$filePathInPublicStorage;
      	$fileExist = \Illuminate\Support\Facades\File::exists($path);

      	// $image_path = storage_path($filePathInPublicStorage);
       //  $image_path2 = Storage::url($filePathInPublicStorage);
       //  dd([__LINE__, $filePathInPublicStorage, $image_path, public_path(), $image_path2, $fileExist, $path]);
        

      	if($fileExist){
          unlink($path);
          $deleted = true;
          //Storage::delete($department->logo);
      	}

      	return $deleted;
    }
}
?>