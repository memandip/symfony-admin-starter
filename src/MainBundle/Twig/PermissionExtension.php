<?php

namespace YarshaStudio\MainBundle\Twig\Extension;

use MainBundle\Service\PermissionService;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Router;
use JMS\DiExtraBundle\Annotation as DI;

/**
 * Class PermissionExtension
 * @package YarshaStudio\MainBundle\Twig\Extension
 *
 * @DI\Service("ys.twig.permission")
 * @DI\Tag(name="twig.extension")
 */
class PermissionExtension extends \Twig_Extension
{
    /**
     * @var PermissionService
     */
    private $permissionService;

    /**
     * @var Router
     */
    private $router;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * PermissionExtension constructor.
     * @param PermissionService $permissionService
     * @param Router $router
     *
     * @DI\InjectParams({
     *     "permissionService" = @DI\Inject("service.permission"),
     *     "router" = @DI\Inject("router"),
     *     "requestStack" = @DI\Inject("request_stack")
     *     })
     */
    public function __construct(PermissionService $permissionService, Router $router, RequestStack $requestStack)
    {
        $this->permissionService = $permissionService;
        $this->router = $router;
        $this->requestStack = $requestStack;
    }

    public function getName()
    {
        return 'permission_twig';
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('has_access', [$this, 'hasAccess']),
            new \Twig_SimpleFunction('render_menu', [$this, 'renderMenu'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('has_access_in_array', [$this, 'hasAccessInArray'])
        ];
    }

    public function hasAccess($permission)
    {
        return $this->permissionService->hasPermission($permission);
    }

    public function hasAccessInArray($permissions)
    {
        return $this->permissionService->hasPermission($permissions);
    }

    public function renderMenu($permission, $route, $label, $iconClass = 'icon-three-bars')
    {
        $path = $this->router->generate($route);
        $activeClass = $this->requestStack->getCurrentRequest()->get('_route') == $route ? 'active' : '';
        return ($this->hasAccess($permission))
            ? sprintf('<li class="%s"><a href="%s"><i class="%s"></i> %s</a></li>',  $activeClass, $path, $iconClass, $label)
            : '';
    }



}