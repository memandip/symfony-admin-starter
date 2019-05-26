<?php

namespace MainBundle\Annotations;

use Doctrine\Common\Annotations\Annotation;

/**
 * Class Permissions
 * @package MainBundle\Annotations
 *
 * @Annotation
 */
class Permissions extends Annotation
{
    /**
     * @var string
     */
    public $desc;

    /**
     * @var string
     */
    public $group;

    /**
     * @var string
     */
    public $subGroup;

    /**
     * @var array
     */
    public $child = [];

    /**
     * @var string
     */
    public $parent;

    /**
     * @var string
     */
    public $route;

    /**
     * @return string
     */
    public function getDesc()
    {
        return $this->desc;
    }

    /**
     * @return string
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * @return string
     */
    public function getSubGroup()
    {
        return $this->subGroup;
    }

    /**
     * @return array
     */
    public function getChild()
    {
        return $this->child;
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @return string
     */
    public function getRoute()
    {
        return $this->route;
    }

}