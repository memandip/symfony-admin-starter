<?php
/**
 * Created by PhpStorm.
 * User: mandip
 * Date: 2/13/19
 * Time: 12:02 AM
 */

namespace UserBundle\Security\Handlers;


use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

/**
 * Class LoginSuccessHandler
 * @package UserBundle\Security\Handlers
 * @DI\Service("service.login_success_handler")
 */
class LoginSuccessHandler implements AuthenticationSuccessHandlerInterface
{

    /**
     * @var Router
     * @DI\Inject("router")
     */
    public $router;

    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        // your authentication success logic
        return new RedirectResponse($this->router->generate('admin_dashboard'));
    }

}
