<?php
namespace App\Traits;
use App\User;
use App\Module;
use Carbon\Carbon;

trait Authorize {
	/**
	 * Checks if the user has one of the allowed roles,
	 * and check the updated-timestamp on the pivot table
	 * to see if the role is recent. Role assignments are updated
	 * at LTI login, so should always be fresh
	 */
	public function userHasAllowedRole($allowed_roles, User $user, Module $module = null) {
		if($module!=null) {
			$now = new Carbon();
		    $has_module_role = $user->rolesInModule($module)->get()->contains(function ($value, $key) use ($allowed_roles, $now) {
	            return ($value->pivot->updated_at->addDays(1) > $now && in_array($value->lti_identifier, $allowed_roles['module']));
	        });
	        if($has_module_role) { return true; }
		}
		if(!array_key_exists('general', $allowed_roles)) { return false; }
		return $user->roles->contains(function ($value, $key) use ($allowed_roles) {
            return in_array($value->lti_identifier, $allowed_roles['general']);
        });
    }
}