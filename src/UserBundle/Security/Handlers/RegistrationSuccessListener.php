<?php
/**
 * Created by PhpStorm.
 * User: mandip
 * Date: 2/20/19
 * Time: 11:37 PM
 */

namespace UserBundle\Security\Handlers;


use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\FOSUserEvents;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use JMS\DiExtraBundle\Annotation as DI;

/**
 * Class RegistrationSuccessListener
 * @package UserBundle\Security\Handlers
 * @DI\Service("registration.success.listener")
 * @DI\Tag("kernel.event_listener", attributes={"event"="fos_user.registration.success","method"="onRegistrationSuccess"})
 */
class RegistrationSuccessListener implements EventSubscriberInterface
{

    /**
     * @var Router
     * @DI\Inject("router")
     */
    public $router;

    public static function getSubscribedEvents(){
        return [
            FOSUserEvents::REGISTRATION_SUCCESS => 'onRegistrationSuccess'
        ];
    }

    public function onRegistrationSuccess(FormEvent $event){
        $url = $this->router->generate('admin_dashboard');
        $event->setResponse(new RedirectResponse($url));
    }

}
