<?php

namespace Quak\ShopsReportBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('QuakShopsReportBundle:Default:index.html.twig', array('name' => $name));
    }
}
