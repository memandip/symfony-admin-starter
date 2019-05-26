<?php
namespace MainBundle\Service;

use JMS\DiExtraBundle\Annotation as DI;
use UserBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Symfony\Component\Security\Core\Authorization\Voter\RoleHierarchyVoter;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;


/**
 * Class RoleHierarchyService
 * @package YarshaStudio\MainBundle\Service
 *
 * @DI\Service("service.role_hierarchy")
 */
class RoleHierarchyService extends RoleHierarchyVoter
{
    /**
     * RoleHierarchyService constructor.
     * @param RoleHierarchyInterface $roleHierarchy
     *
     * @DI\InjectParams({"roleHierarchy" = @DI\Inject("security.role_hierarchy")})
     */
    public function __construct(RoleHierarchyInterface $roleHierarchy)
    {
        parent::__construct($roleHierarchy);
    }

    /**
     * Check whether a user is granted a specific role, respecting the role hierarchy.
     *
     * @return boolean Whether this user is granted the $requiredRole
     */
    public function check(User $user, $requiredRole)
    {
        $roles = array();
        foreach ($user->getRoles() as $roleName) {
            $roles[] = new Role($roleName);
        }
        $token = new AnonymousToken('admin$$', '$$nimda', $roles);
        return static::ACCESS_GRANTED == $this->vote($token, null, array($requiredRole));
    }
}