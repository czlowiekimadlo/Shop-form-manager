<?php

namespace Quak\ShopsAdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Quak\ShopsCoreBundle\Entity\User;
use Quak\ShopsCoreBundle\Repository\Repository;
use Quak\ShopsAdminBundle\Form\Type\UserType;

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

    /**
     * Create new user form
     *
     * @param Request $request
     *
     * @return Response
     */
    public function newUserAction(Request $request)
    {
        $user = new User;

        $form = $this->createForm(new UserType(), $user);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $encoderFactory = $this->container
                ->get('security.encoder_factory');

            $encoder = $encoderFactory->getEncoder($user);
            $user->setPassword(
                $encoder->encodePassword(
                    $user->getPassword(),
                    $user->getSalt()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirect(
                $this->generateUrl('quak_shops_admin_index')
            );
        }

        return $this->render(
            'QuakShopsAdminBundle:Admin:newUser.html.twig',
            array(
                'form' => $form->createView()
            )
        );
    }

    /**
     * Edit existing user
     *
     * @param Request $request
     *
     * @return Response
     */
    public function editUserAction(Request $request)
    {

    }
}
