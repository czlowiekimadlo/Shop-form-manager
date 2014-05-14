<?php

namespace Quak\ShopsAdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Temporary filler
 */
class DefaultController extends Controller
{
    /**
     * Temporary filler
     *
     * @param string $name
     *
     * @return Response
     */
    public function indexAction($name)
    {
        return $this->render('QuakShopsAdminBundle:Default:index.html.twig', array('name' => $name));
    }
}
