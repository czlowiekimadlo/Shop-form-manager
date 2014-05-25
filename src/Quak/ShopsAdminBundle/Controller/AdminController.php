<?php

namespace Quak\ShopsAdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Quak\ShopsCoreBundle\Repository\Repository;

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
        $entityManager = $this->getDoctrine()->getManager();

        $users = $entityManager->getRepository(Repository::USER)
            ->fetchAllGroupedByRole();

        return $this->render(
            'QuakShopsAdminBundle:Admin:index.html.twig',
            array(
                'users' => $users
            )
        );
    }
}
