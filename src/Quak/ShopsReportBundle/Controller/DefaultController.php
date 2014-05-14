<?php

namespace Quak\ShopsReportBundle\Controller;

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
        return $this->render('QuakShopsReportBundle:Default:index.html.twig', array('name' => $name));
    }
}
