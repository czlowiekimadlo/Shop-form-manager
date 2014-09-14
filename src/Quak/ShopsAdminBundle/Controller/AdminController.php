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
        $adminAccess = $this->get('admin.access');
        $user = $this->getUser();

        $users = $this->get('repository.user')->fetchAllGroupedByRole();
        $users[User::ROLE_SHOP] = $adminAccess->filterForUser(
            $users[User::ROLE_SHOP],
            $user
        );

        $regions = $this->get('repository.region')->findAll();

        $fields = $this->get('repository.formField')->fetchAllSortedByOrdering();

        $reports = $this->get('repository.scheduledReport')->findAll();

        return $this->render(
            'QuakShopsAdminBundle:Admin:index.html.twig',
            array(
                'user' => $user,
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
        $currentUser = $this->getUser();

        $user = new User;

        $form = $this->createForm(new UserType($currentUser), $user);

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

            if ($user->hasRole(User::ROLE_ADMIN) ||
                $user->hasRole(User::ROLE_REGION_ADMIN)) {
                $target = '#administrators';
            } else {
                $target = '#shops';
            }

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
        $currentUser = $this->getUser();

        $userId = $request->attributes->get('userId');

        $user = $this->get('repository.user')->findOneById($userId);

        if (!$user) {
            throw $this->createNotFoundException('The user does not exist');
        }

        $currentPassword = $user->getPassword();

        $form = $this->createForm(new UserType($currentUser), $user);

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

        $model = $this->get('admin.model.shop');
        $model->removeShop($user);

        if ($user->hasRole(User::ROLE_ADMIN) ||
            $user->hasRole(User::ROLE_REGION_ADMIN)) {
            $target = '#administrators';
        } else {
            $target = '#shops';
        }

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

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function editCurrentReportAction(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $currentUser = $this->getUser();

        $shopId = $request->attributes->get('shopId');

        $user = $this->get('repository.user')->findOneById($shopId);

        $reportValues = $this->get('report.values.factory');
        $statusReport = $this->get('model.shopReport')
            ->getCurrentStatusReport($user);
        $savedData = $reportValues->createArrayFromReport(
            $statusReport
        );
        $fields = $this->get('repository.formField')->fetchAllSortedByOrdering();

        $this->get('form.report')->setOverride(true);

        $form = $this->createForm($this->get('form.report'), $savedData);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $data = $form->getData();

            $reportValues->createValuesFromArray($statusReport, $data);
        }

        return $this->render(
            'QuakShopsAdminBundle:Admin:reportForm.html.twig',
            array(
                'form' => $form->createView(),
                'user' => $user,
                'valid' => $form->isValid(),
                'fields' => $fields
            )
        );
    }
}
