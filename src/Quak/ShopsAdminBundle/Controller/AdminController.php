<?php

namespace Quak\ShopsAdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Quak\ShopsCoreBundle\Entity\User;
use Quak\ShopsCoreBundle\Entity\Region;
use Quak\ShopsCoreBundle\Repository\Repository;
use Quak\ShopsAdminBundle\Form\Type\UserType;
use Quak\ShopsAdminBundle\Form\Type\RegionType;

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
        $users = $this->get('repository.user')->fetchAllGroupedByRole();

        $regions = $this->get('repository.region')->findAll();

        $fields = $this->get('repository.formField')->fetchAllSortedByOrdering();

        $reports = $this->get('repository.scheduledReport')->findAll();

        return $this->render(
            'QuakShopsAdminBundle:Admin:index.html.twig',
            array(
                'users' => $users,
                'regions' => $regions,
                'fields' => $fields,
                'reports' => $reports
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

            $target = $user->hasRole(User::ROLE_ADMIN) ? '#administrators' : '#shops';

            return $this->redirect(
                $this->generateUrl('quak_shops_admin_index') . $target
            );
        }

        return $this->render(
            'QuakShopsAdminBundle:Admin:userForm.html.twig',
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
        $entityManager = $this->getDoctrine()->getManager();

        $userId = $request->attributes->get('userId');

        $user = $this->get('repository.user')->findOneById($userId);

        if (!$user) {
            throw $this->createNotFoundException('The user does not exist');
        }

        $currentPassword = $user->getPassword();

        $form = $this->createForm(new UserType(), $user);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $encoderFactory = $this->container
                ->get('security.encoder_factory');

            $encoder = $encoderFactory->getEncoder($user);

            if ($user->getPassword()) {
                $user->setPassword(
                    $encoder->encodePassword(
                        $user->getPassword(),
                        $user->getSalt()
                    )
                );
            } else {
                $user->setPassword($currentPassword);
            }

            $entityManager->flush();
        }

        return $this->render(
            'QuakShopsAdminBundle:Admin:userForm.html.twig',
            array(
                'form' => $form->createView(),
                'user' => $user
            )
        );
    }

    /**
     * Remove existing user
     *
     * @param int $userId
     *
     * @return Response
     */
    public function removeUserAction($userId)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $user = $this->get('repository.user')->findOneById($userId);

        if (!$user) {
            throw $this->createNotFoundException('The user does not exist');
        }

        $entityManager->remove($user);
        $entityManager->flush();

        $target = $user->hasRole(User::ROLE_ADMIN) ? '#administrators' : '#shops';

        return $this->redirect(
            $this->generateUrl('quak_shops_admin_index') . $target
        );
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function newRegionAction(Request $request)
    {
        $region = new Region;

        $form = $this->createForm(new RegionType(), $region);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($region);
            $entityManager->flush();

            return $this->redirect(
                $this->generateUrl('quak_shops_admin_index') . "#regions"
            );
        }

        return $this->render(
            'QuakShopsAdminBundle:Admin:regionForm.html.twig',
            array(
                'form' => $form->createView()
            )
        );
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function editRegionAction(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $regionId = $request->attributes->get('regionId');

        $region = $this->get('repository.region')->findOneById($regionId);

        if (!$region) {
            throw $this->createNotFoundException('The Region does not exist');
        }

        $form = $this->createForm(new RegionType(), $region);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $entityManager->flush();
        }

        return $this->render(
            'QuakShopsAdminBundle:Admin:regionForm.html.twig',
            array(
                'form' => $form->createView(),
                'region' => $region
            )
        );
    }

    /**
     * @param int $regionId
     *
     * @return Response
     */
    public function removeRegionAction($regionId)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $region = $this->get('repository.region')->findOneById($regionId);

        if (!$region) {
            throw $this->createNotFoundException('The region does not exist');
        }

        $entityManager->remove($region);
        $entityManager->flush();

        return $this->redirect(
            $this->generateUrl('quak_shops_admin_index') . "#regions"
        );
    }
}
