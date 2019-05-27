<?php

namespace MainBundle\Service;

use JMS\DiExtraBundle\Annotation as DI;
use Doctrine\Common\Annotations\Reader;
use UserBundle\Entity\User;
use MainBundle\Annotations\Permissions;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;


/**
 * Class PermissionService
 * @package MainBundle\Service
 *
 * @DI\Service("service.permission", public=true)
 */
class PermissionService
{

    private $bundles;

    private $rootDir;

    /**
     * @var Reader
     */
    private $reader;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * PermissionService constructor.
     * @param $bundles
     * @param $rootDir
     * @param Reader $annotationReader
     * @param TokenStorageInterface $tokenStorage
     *
     * @DI\InjectParams({
     *     "bundles" = @DI\Inject("%kernel.bundles%"),
     *     "rootDir" = @DI\Inject("%kernel.root_dir%"),
     *     "tokenStorage" = @DI\Inject("security.token_storage"),
     *     })
     */
    public function __construct(
        $bundles
        , $rootDir
        , Reader $annotationReader
        , TokenStorageInterface $tokenStorage
    )
    {
        $this->bundles = $bundles;
        $this->rootDir = $rootDir;
        $this->reader = $annotationReader;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @param null $group
     * @return array
     * @throws \ReflectionException
     */
    public function getAllPermissions($group = null)
    {
        $bundlePrefix = '';

        $permissions = [];

        $groupPermissions = ($group and $group->getPermissions()) ? $group->getPermissions() : [];

        foreach($this->bundles as $name => $class)
        {
            if (substr($name, 0, strlen($bundlePrefix)) != $bundlePrefix) continue;

            $namespaceParts = explode('\\', $class);
            array_pop($namespaceParts);
            $bundlePath = implode('/', $namespaceParts);
            $bundleNamespace = implode('\\', $namespaceParts);
            $rootPath = $this->rootDir.'/../src/';
            $controllerDir = $rootPath.$bundlePath.'/Controller';

//            $module = str_replace('Bundle', ' Module', $namespaceParts[1]);

            if( file_exists($controllerDir))
            {
                $files = scandir($controllerDir);

                foreach($files as $file)
                {
                    if(!is_file($controllerDir . '/' .$file)) continue;

                    list($filename, $ext) = explode('.', $file);

                    if( $ext != 'php') continue;

                    $class = $bundleNamespace.'\\Controller\\'.$filename;

                    $reflectedClass = new \ReflectionClass($class);

                    foreach($reflectedClass->getMethods() as $reflectionMethod)
                    {
                        $annotations = $this->reader->getMethodAnnotations($reflectionMethod);

                        foreach($annotations as $annotation)
                        {
                            if($annotation instanceof Permissions and ($group = $annotation->getGroup()))
                            {
                                $subGroup = $annotation->getSubGroup() ? $annotation->getSubGroup() : 'Common';

                                $permission = $annotation->value;
                                $description = $annotation->getDesc()
                                    ? $annotation->getDesc()
                                    : ucwords(str_replace('_', ' ', $permission))
                                ;
                                $child = $annotation->getChild();

                                $continue = false;
                                if( isset($permissions[$group][$subGroup])){
                                    foreach($permissions[$group][$subGroup] as $k)
                                    {
                                        if ($k['permission'] == $permission)
                                        {
                                            $continue = true;
                                            continue;
                                        }
                                    }
                                }

                                if($continue) continue;

                                $permissions[$group][$subGroup][] = [
                                    'permission' => $permission,
                                    'child' => $child,
                                    'description' => $description,
                                    'parent' => $annotation->getParent(),
                                    'checked' => in_array($permission, $groupPermissions)
                                ];

                            }
                        }
                    }
                }
            }
        }

        return $permissions;
    }

    public function getPermissionsList($group = null)
    {
        $permissions = $this->getAllPermissions($group);

        $user = $this->tokenStorage->getToken() ? $this->tokenStorage->getToken()->getUser() : null;

        if( $user->hasRole('ROLE_SUPER_ADMIN') )
        {
            return $permissions;
        }

        $userPermissions = $this->getPermissionsByUser($user);

        foreach($permissions as $module => $groups)
        {
            foreach($groups as $group => $details)
            {
                $count = 0;
                foreach($details as $detail)
                {
                    if( !in_array($detail['permission'], $userPermissions) )
                    {
                        unset($permissions[$module][$group][$count]);
                    }
                    $count++;
                }

                if( count($permissions[$module][$group]) == 0)
                {
                    unset($permissions[$module][$group]);
                }
            }

            if( count($permissions[$module]) == 0)
            {
                unset($permissions[$module]);
            }
        }

        return $permissions;

    }

    /**
     * @param mixed $permissions
     *
     * @return bool
     */
    public function hasPermission($permissions)
    {
        $user = $this->tokenStorage->getToken() ? $this->tokenStorage->getToken()->getUser() : null;

        if( ! $user or ! $user instanceof User )
        {
            return false;
        }

        if( $user->hasRole('ROLE_SUPER_ADMIN') )
        {
            return true;
        }

        $permissionToCheck = is_array($permissions) ? $permissions : [$permissions];

        return $this->hasPermissionToUser($permissionToCheck, $user);

    }

    public function getPermissionsByUser(User $user)
    {
        $groups = $user->getUserGroups();

        $permissions = [];

        foreach($groups as $g)
        {
            $permissions = array_merge($permissions, $g->getPermissions());
        }

        return $permissions;
    }

    public function hasPermissionToUser($permissions, $user)
    {
        $userPermissions = $this->getPermissionsByUser($user);

        foreach($permissions as $p)
        {
            if( in_array($p, $userPermissions) )
            {
                return true;
            }
        }
        return false;
    }

}