<?php

namespace Quak\ShopsCoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('QuakShopsCoreBundle:Default:index.html.twig', array('name' => $name));
    }
}
