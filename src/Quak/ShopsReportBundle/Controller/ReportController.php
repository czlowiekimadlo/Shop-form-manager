<?php

namespace Quak\ShopsReportBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

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
        return $this->render('QuakShopsReportBundle:Report:index.html.twig');
    }
}
