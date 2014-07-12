<?php

namespace Quak\ShopsReportBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Quak\ShopsCoreBundle\Entity\ShopReport;
use Quak\ShopsReportBundle\Form\Type\ReportType;

/**
 * Reports controller
 */
class ReportController extends Controller
{
    /**
     * Index action
     *
     * @return Response
     */
    public function indexAction()
    {
        $user = $this->getUser();

        return $this->render(
            'QuakShopsReportBundle:Report:index.html.twig',
            array(
                'user' => $user
            )
        );
    }

    /**
     * New report action
     *
     * @return Response
     */
    public function newAction()
    {
        $user = $this->getUser();

        if (!$user->getCurrentReport()) {
            $entityManager = $this->getDoctrine()->getManager();

            $currentReport = new ShopReport;
            $user->setCurrentReport($currentReport);
            $currentReport->setUser($user);

            $entityManager->persist($currentReport);
            $entityManager->flush();
        }

        $currentReport = $user->getCurrentReport();

        return $this->redirect(
            $this->generateUrl('quak_shops_report_form', array(
                'reportId' => $currentReport->getId()
            ))
        );
    }

    /**
     * Report form action
     *
     * @param Request $request
     *
     * @return Response
     */
    public function reportAction(Request $request)
    {
        $user = $this->getUser();
        $currentReport = $user->getCurrentReport();

        if (!$currentReport) {
            return $this->redirect(
                $this->generateUrl('quak_shops_report_index')
            );
        }

        $reportValues = $this->get('report.values.factory');
        $savedData = $reportValues->createArrayFromReport($currentReport);
        $legend = $this->get('repository.registryKey')->getLegend();

        $form = $this->createForm($this->get('form.report'), $savedData);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $data = $form->getData();

            $reportValues->createValuesFromArray($currentReport, $data);
        }

        return $this->render(
            'QuakShopsReportBundle:Report:reportForm.html.twig',
            array(
                'form' => $form->createView(),
                'user' => $user,
                'valid' => $form->isValid(),
                'legend' => $legend
            )
        );
    }
}
