<?php
namespace YarshaStudio\UserBundle\Twig;


use Doctrine\ORM\EntityManager;
use UserBundle\Entity\User;
use JMS\DiExtraBundle\Annotation as DI;
use UserBundle\Service\UserService;


/**
 * Class UserTwigExtension
 * @package YarshaStudio\VehicleManagementBundle\Twig
 *
 * @DI\Service("twig.user_extension")
 * @DI\Tag(name="twig.extension")
 */
class UserTwigExtension extends \Twig_Extension
{

    /**
     * @var UserService
     */
    private $userService;

    /**
     * @var EntityManager
     */
    private $em;


    /**
     * UserTwigExtension constructor.
     * @param UserService $userService
     *
     * @DI\InjectParams({
     *     "userService"=@DI\Inject("service.user"),
     *     "em"=@DI\Inject("doctrine.orm.entity_manager"),
     * })
     */
    public function __construct(UserService $userService, EntityManager $em)
    {
        $this->userService = $userService;
        $this->em = $em;
    }

    public function getName()
    {
        return "ys_twig_user_extension";
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('impersonator', [$this, 'getImpersonator']),
            new \Twig_SimpleFunction('impersonator_user_list_path', [$this, 'getImpersonatorUserListPath']),
            new \Twig_SimpleFunction('can_switch_user', [$this, 'canSwitchTheUser']),
        ];
    }


    public function getImpersonatorUserListPath()
    {
        $impersonator = $this->userService->getImpersonatorUser();

        $user = $this->em->getRepository(User::class)->find($impersonator->getId());

        if( !$user )
        {
          return 'fos_user_security_logout';
        }

        if( $this->userService->isRoleGranted('ROLE_SUPER_ADMIN', $user) )
        {
            return 'users_list';
        }

        return 'fos_user_security_logout';
    }

    public function canSwitchTheUser(User $user, $type = 'admin')
    {
        return $this->userService->canSwitchTheUser($user, $type);
    }


}



