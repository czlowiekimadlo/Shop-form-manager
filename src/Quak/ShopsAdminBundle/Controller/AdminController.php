<?php

namespace Quak\ShopsAdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Administration controller
 */
class AdminController extends Controller
{
    /**
     * Administration index
     *
     * @return Response
     */
    public function indexAction()
    {
        return $this->render('QuakShopsAdminBundle:Admin:index.html.twig');
    }
}
