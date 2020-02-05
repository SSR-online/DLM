<?php

namespace App\Policies;

use App\User;
use App\Module;
use Illuminate\Auth\Access\HandlesAuthorization;

class ModulePolicy
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
    public function view(User $user, Module $module)
    {
        $allowed_roles = [
            'general' => [
                'urn:lti:instrole:ims/lis/Administrator',
                'urn:lti:sysrole:ims/lis/Administrator',
            ],
            'module' => [
                'urn:lti:role:ims/lis/Instructor',
                'urn:lti:role:ims/lis/Learner'
            ]
        ];
        return $this->userHasAllowedRole($allowed_roles, $user, $module);
    }

    /**
     * Determine whether the user can create modules.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        $allowed_roles = [
            'general' => [
                'urn:lti:instrole:ims/lis/Administrator',
                'urn:lti:sysrole:ims/lis/Administrator',
            ]
        ];
        return $this->userHasAllowedRole($allowed_roles, $user);
    }

    /**
     * Determine whether the user can update the module.
     *
     * @param  \App\User  $user
     * @param  \App\Module  $module
     * @return mixed
     */
    public function update(User $user, Module $module)
    {
        $allowed_roles = [
            'general' => [
                'urn:lti:instrole:ims/lis/Administrator',
                'urn:lti:sysrole:ims/lis/Administrator',
            ],
            'module' => [
                'urn:lti:role:ims/lis/Instructor'
            ]
        ];
        return $this->userHasAllowedRole($allowed_roles, $user, $module);
    }

    /**
     * Determine whether the user can delete the module.
     *
     * @param  \App\User  $user
     * @param  \App\Module  $module
     * @return mixed
     */
    public function delete(User $user, Module $module)
    {
        $allowed_roles = [
            'general' => [
                'urn:lti:instrole:ims/lis/Administrator',
                'urn:lti:sysrole:ims/lis/Administrator',
            ],
            'module' => [
                'urn:lti:role:ims/lis/Instructor'
            ]
        ];
        return $this->userHasAllowedRole($allowed_roles, $user, $module);
    }
}
