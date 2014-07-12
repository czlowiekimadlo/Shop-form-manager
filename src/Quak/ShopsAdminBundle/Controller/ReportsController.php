<?php
namespace Quak\ShopsAdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Quak\ShopsCoreBundle\Entity\FormField;
use Quak\ShopsCoreBundle\Entity\RegistryKey;
use Quak\ShopsCoreBundle\Entity\ScheduledReport;
use Quak\ShopsCoreBundle\Repository\Repository;
use Quak\ShopsAdminBundle\Form\Type\FormFieldType;
use Quak\ShopsAdminBundle\Form\Type\LegendType;
use Quak\ShopsAdminBundle\Form\Type\ScheduledReportType;

/**
 * Reporting configuration controller
 */
class ReportsController extends Controller
{
    /**
     * Create new field form
     *
     * @param Request $request
     *
     * @return Response
     */
    public function newFormFieldAction(Request $request)
    {
        $field = new FormField;

        $form = $this->createForm(new FormFieldType(), $field);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($field);
            $entityManager->flush();

            return $this->redirect(
                $this->generateUrl('quak_shops_admin_index') . "#form"
            );
        }

        return $this->render(
            'QuakShopsAdminBundle:Reports:fieldForm.html.twig',
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
    public function editFormFieldAction(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $fieldId = $request->attributes->get('fieldId');

        $field = $this->get('repository.formField')->findOneById($fieldId);

        if (!$field) {
            throw $this->createNotFoundException('The field does not exist');
        }

        $form = $this->createForm(new FormFieldType(), $field);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $entityManager->flush();
        }

        return $this->render(
            'QuakShopsAdminBundle:Reports:fieldForm.html.twig',
            array(
                'form' => $form->createView(),
                'field' => $field
            )
        );
    }

    /**
     * @param int $fieldId
     *
     * @return Response
     */
    public function removeFormFieldAction($fieldId)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $field = $this->get('repository.formField')->findOneById($fieldId);

        if (!$field) {
            throw $this->createNotFoundException('The field does not exist');
        }

        $entityManager->remove($field);
        $entityManager->flush();

        return $this->redirect(
            $this->generateUrl('quak_shops_admin_index') . "#form"
        );
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function editLegendAction(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $legend = $this->get('repository.registryKey')->getLegend();

        $form = $this->createForm(new LegendType(), $legend);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $entityManager->persist($legend);
            $entityManager->flush();
        }

        return $this->render(
            'QuakShopsAdminBundle:Reports:legendForm.html.twig',
            array(
                'form' => $form->createView()
            )
        );
    }

    /**
     * Create new schedule form
     *
     * @param Request $request
     *
     * @return Response
     */
    public function newScheduleAction(Request $request)
    {
        $schedule = new ScheduledReport;

        $form = $this->createForm(new ScheduledReportType(), $schedule);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($schedule);
            $entityManager->flush();

            return $this->redirect(
                $this->generateUrl('quak_shops_admin_index') . "#reporting"
            );
        }

        return $this->render(
            'QuakShopsAdminBundle:Reports:scheduleForm.html.twig',
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
    public function editScheduleAction(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $scheduleId = $request->attributes->get('scheduleId');

        $report = $this->get('repository.scheduledReport')->findOneById($scheduleId);

        if (!$report) {
            throw $this->createNotFoundException('The report does not exist');
        }

        $form = $this->createForm(new ScheduledReportType(), $report);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $entityManager->flush();
        }

        return $this->render(
            'QuakShopsAdminBundle:Reports:scheduleForm.html.twig',
            array(
                'form' => $form->createView(),
                'report' => $report
            )
        );
    }

    /**
     * @param int $scheduleId
     *
     * @return Response
     */
    public function removeScheduleAction($scheduleId)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $report = $this->get('repository.scheduledReport')->findOneById($scheduleId);

        if (!$report) {
            throw $this->createNotFoundException('The report does not exist');
        }

        $entityManager->remove($report);
        $entityManager->flush();

        return $this->redirect(
            $this->generateUrl('quak_shops_admin_index') . "#reporting"
        );
    }

    /**
     * @return Response
     */
    public function sendReportsAction()
    {
        $this->get('reporter')->runSchedule();

        return $this->redirect(
            $this->generateUrl('quak_shops_admin_index') . "#reporting"
        );
    }

    /**
     * @return Response
     */
    public function lookupReportsAction()
    {
        $xml = $this->get('reporter')->generateCurrentReport();

        $response = new Response();

        $response->headers->set('Content-Type', 'application/xml');
        $response->headers->set('Content-Disposition', 'attachment;filename="current_report.xml"');

        $response->setContent($xml);

        return $response;
    }
}