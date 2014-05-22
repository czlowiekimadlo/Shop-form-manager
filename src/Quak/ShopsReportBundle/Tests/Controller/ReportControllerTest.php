<?php
namespace Quak\ShopsReportBundle\Tests\Controller;

use Quak\ShopsCoreBundle\Tests\FunctionalTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Report controller test
 */
class ReportControllerTest extends FunctionalTestCase
{
    /**
     * @covers Quak\ShopsReportBundle\Controller\ReportController::indexAction
     */
    public function testIndexAction_redirectedToLogin_notLoggedIn()
    {
        $this->client->request('GET', '/report/');

        $response = $this->client->getResponse();
        $location = $response->headers->get('location');

        $this->assertEquals(Response::HTTP_FOUND, $response->getStatusCode());
        $this->assertRegExp('/.*\/login$/', $location);
    }
}
