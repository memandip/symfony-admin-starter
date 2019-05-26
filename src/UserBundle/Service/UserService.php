<?php

namespace UserBundle\Service;

use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Model\UserManager;
use JMS\DiExtraBundle\Annotation as DI;
use MainBundle\Service\RoleHierarchyService;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface;
use UserBundle\Entity\Group;
use UserBundle\Entity\User;
use Symfony\Component\Security\Core\Role\SwitchUserRole;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;


/**
 * Class UserService
 * @package YarshaStudio\UserBundle\Service
 *
 * @DI\Service("service.user")
 */
class UserService
{
    /**
     * @var UserManager
     */
    private $userManager;

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @var RoleHierarchyService
     */
    private $roleHierarchyService;

    /**
     * @var TokenStorage
     */
    private $tokenStorage;


    /**
     * UserService constructor.
     * @param UserManager $userManager
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param RoleHierarchyService $roleHierarchyService
     * @param EntityManager $em
     * @param TokenStorage $tokenStorage
     *
     * @DI\InjectParams({
     *     "userManager" = @DI\Inject("fos_user.user_manager"),
     *     "authorizationChecker" = @DI\Inject("security.authorization_checker"),
     *     "roleHierarchyService" = @DI\Inject("service.role_hierarchy"),
     *     "em" = @DI\Inject("doctrine.orm.entity_manager"),
     *     "tokenStorage" = @DI\Inject("security.token_storage")
     * })
     */
    public function __construct(
            UserManager $userManager,
            AuthorizationCheckerInterface $authorizationChecker,
            RoleHierarchyService $roleHierarchyService,
            EntityManager $em,
            TokenStorage $tokenStorage
    )
    {
        $this->userManager = $userManager;
        $this->authorizationChecker = $authorizationChecker;
        $this->roleHierarchyService = $roleHierarchyService;
        $this->em = $em;
        $this->tokenStorage = $tokenStorage;
    }


    public function getImpersonatorUser()
    {
        if ($this->authorizationChecker->isGranted('ROLE_PREVIOUS_ADMIN')) {
            foreach ($this->tokenStorage->getToken()->getRoles() as $role) {
                if ($role instanceof SwitchUserRole) {
                    return $role->getSource()->getUser();
                }
            }
        }

        return null;
    }

    public function isRoleGranted($role, User $user)
    {
        return $this->roleHierarchyService->check($user, $role);
    }

    public function canSwitchTheUser(User $user, $type = 'admin')
    {
        if( 'admin' == $type)
        {
            return ( $this->authorizationChecker->isGranted('ROLE_ALLOWED_TO_SWITCH')
                and !$this->authorizationChecker->isGranted('ROLE_PREVIOUS_ADMIN')
                and !$user->hasRole('ROLE_SUPER_ADMIN')
                and ($this->tokenStorage->getToken()->getUser() !== $user)
            );
        }

        return false;

    }


    public function findGroupByName($name){

       return $group = $this->em->getRepository(Group::class)->findOneBy([
             'deleted' => false,
             'name' => $name
        ]);

    }

    public function findUserByEmail($email){

        return $group = $this->em->getRepository(User::class)->findOneBy([
            'deleted' => false,
            'email' => $email,
        ]);

    }

    public function findUserByMobileNumber($number){

        return $group = $this->em->getRepository(User::class)->findOneBy([

            'deleted' => false,
            'enabled' => true


        ]);
    }



    public function findGroupByNameForEachMerchant($name){

        return $group = $this->em->getRepository(Group::class)->findOneBy([

            'isDeleted' => false,
            'name' => $name,
        ]);

    }


}