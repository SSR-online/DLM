<?php

namespace App\Policies;

use App\User;
use App\Module;
use App\Node;
use Illuminate\Auth\Access\HandlesAuthorization;

class NodePolicy
{
    use HandlesAuthorization;
    use \App\Traits\Authorize;

    /**
     * Determine whether the user can view the node.
     *
     * @param  \App\User  $user
     * @param  \App\Node  $node
     * @return mixed
     */
    public function view(User $user, Node $node)
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
        return $this->userHasAllowedRole($allowed_roles, $user, $node->module);
    }

    /**
     * Determine whether the user can create nodes.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user, Node $node)
    {
        $allowed_roles = [
            'general' => [
                'urn:lti:instrole:ims/lis/Administrator',
                'urn:lti:sysrole:ims/lis/Administrator',
            ],
            'module' => [
                'urn:lti:role:ims/lis/Instructor',
            ]
        ];
        return $this->userHasAllowedRole($allowed_roles, $user, $node->module);
    }

    /**
     * Determine whether the user can update the node.
     *
     * @param  \App\User  $user
     * @param  \App\Node  $node
     * @return mixed
     */
    public function update(User $user, Node $node)
    {
        $allowed_roles = [
            'general' => [
                'urn:lti:instrole:ims/lis/Administrator',
                'urn:lti:sysrole:ims/lis/Administrator',
            ],
            'module' => [
                'urn:lti:role:ims/lis/Instructor',
            ]
        ];
        return $this->userHasAllowedRole($allowed_roles, $user, $node->module);
    }

    /**
     * Determine whether the user can delete the node.
     *
     * @param  \App\User  $user
     * @param  \App\Node  $node
     * @return mixed
     */
    public function delete(User $user, Node $node)
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
        return $this->userHasAllowedRole($allowed_roles, $user, $node->module);
    }
}
