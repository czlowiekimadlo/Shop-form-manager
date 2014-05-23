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
        $this->assertEqualRoutes('login', $location);
    }

    /**
     * @covers Quak\ShopsAdminBundle\Controller\AdminController::indexAction
     */
    public function testIndexAction_showAdminIndex_loggedAsAdmin()
    {
        $username = 'demoShop';
        $password = 'demo';
        $this->authenticateUser($username, $password);

        $this->client->request('GET', '/report/');
        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }
}
