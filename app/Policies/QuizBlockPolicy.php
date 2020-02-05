<?php

namespace App\Policies;

use App\User;
use App\QuizBlock;
use Illuminate\Auth\Access\HandlesAuthorization;

class QuizBlockPolicy
{
    use HandlesAuthorization;
    use \App\Traits\Authorize;

    /**
     * Determine whether the user can view the module.
     *
     * @param  \App\User  $user
     * @param  \App\Module  $module
     * @return mixed
     */
    public function view(User $user, QuizBlock $block)
    {
        return true;
    }

    /**
     * Determine whether the user can create modules.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function viewResults(User $user, QuizBlock $block)
    {
        $allowed_roles = [
            'general' => [
                'urn:lti:instrole:ims/lis/Administrator',
                'urn:lti:sysrole:ims/lis/Administrator',
                'urn:lti:role:ims/lis/Instructor',
            ]
        ];
        return $this->userHasAllowedRole($allowed_roles, $user);
    }
}
