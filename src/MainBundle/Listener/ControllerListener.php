<?php

namespace YarshaStudio\MainBundle\Listener;


use Doctrine\Common\Util\ClassUtils;
use Doctrine\Common\Annotations\Reader;
use MainBundle\Annotations\Permissions;
use MainBundle\Service\PermissionService;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Class ControllerListener
 * @package  YarshaStudio\MainBundle\Listener
 *
 * @DI\Service("yarsha.listener.controller", autowire=true)
 * @DI\Tag(name="kernel.event_listener", attributes={"event"="kernel.controller", "method"="onFilterController"})
 */
class ControllerListener
{

    /**
     * @var Reader
     */
    private $reader;

    /**
     * @var PermissionService
     */
    private $permissionService;

    /**
     * @var Request
     */
    private $request;

    /**
     * ControllerListener constructor.
     * @param Reader $annotationReader
     * @param PermissionService $permissionService
     * @param RequestStack $request
     *
     * @DI\InjectParams({
     *  "annotationReader" = @DI\Inject("annotation_reader"),
     *  "permissionService" = @DI\Inject("service.permission"),
     *  "request" = @DI\Inject("request_stack")
     * })
     */
    public function __construct(
        Reader $annotationReader
        , PermissionService $permissionService
        , RequestStack $request
    )
    {
        $this->reader = $annotationReader;
        $this->permissionService = $permissionService;
        $this->request = $request;
    }

    /**
     * @param FilterControllerEvent $event
     */
    public function onFilterController(FilterControllerEvent $event)
    {
        $controller = $event->getController();

        list($object, $method) = $controller;

        if($object instanceof Controller)
        {
            // the controller could be a proxy, e.g. when using the JMSSecurityExtraBundle or JMSDiExtraBundle
            $className = ClassUtils::getClass($object);

            $reflectionClass = new \ReflectionClass($className);
            $reflectionMethod = $reflectionClass->getMethod($method);

            $allAnnotations = $this->reader->getMethodAnnotations($reflectionMethod);

            $permissionAnnotations = array_filter($allAnnotations, function($annotation) {
                return $annotation instanceof Permissions;
            });

            $requiredPermissions = [];

            foreach($permissionAnnotations as $permissionAnnotation)
            {
                $routeFromAnnotation = $permissionAnnotation->getRoute();

                if( !$routeFromAnnotation or $routeFromAnnotation == $this->request->getCurrentRequest()->get('_route'))
                {
                    $requiredPermissions[] = $permissionAnnotation->value;
                }
            }

            if( count($requiredPermissions) )
            {
                $hasPermission = $this->permissionService->hasPermission($requiredPermissions);

                if( ! $hasPermission )
                {
                    throw new AccessDeniedException();
                }
            }
        }




    }

}