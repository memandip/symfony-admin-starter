<?php
/**
 * Created by PhpStorm.
 * User: mandip
 * Date: 3/2/19
 * Time: 10:30 PM
 */

namespace MainBundle\Service;

use JMS\DiExtraBundle\Annotation as DI;
use Knp\Component\Pager\Paginator;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class PaginationService
 * @package MainBundle\Service
 * @DI\Service("service.pagination")
 */
class PaginationService
{

    /**
     * @var RequestStack
     * @DI\Inject("request_stack")
     */
    public $request;

    /**
     * @var Paginator
     * @DI\Inject("knp_paginator")
     */
    public $paginator;

    public function paginate($qb, $perPage = 20, $options = []){
        $page = $this->request->getCurrentRequest()->get('page') ?? 1;
        return $this->paginator->paginate($qb,$page, $perPage, $options);
    }

}
