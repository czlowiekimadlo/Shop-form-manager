<?php

namespace Quak\ShopsCoreBundle\Tests\Controller;

use Quak\ShopsCoreBundle\Tests\FunctionalTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * AccessController tests
 */
class AccessControllerTest extends FunctionalTestCase
{
    /**
     * @covers Quak\ShopsCoreBundle\Controller\AccessController::indexAction
     */
    public function testIndexAction_redirectedToLogin_notLoggedIn()
    {
        $this->client->request('GET', '/');

        $response = $this->client->getResponse();
        $location = $response->headers->get('location');

        $this->assertEquals(Response::HTTP_FOUND, $response->getStatusCode());
        $this->assertEqualRoutes('login', $location);
    }

    /**
     * @covers Quak\ShopsCoreBundle\Controller\AccessController::loginAction
     */
    public function testLoginAction_formOpens_notLoggedIn()
    {
        $this->client->request('GET', '/login');

        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    /**
     * Login test
     *
     * @param string $username      user name
     * @param string $password      password
     * @param string $expectedRoute expected redirect route
     *
     * @dataProvider dataProvider_testLoginAction_userLoggingIn
     */
    public function testLoginAction_userLoggingIn(
        $username,
        $password,
        $expectedRoute
    )
    {
        $this->authenticateUser($username, $password);

        $response = $this->client->getResponse();
        $location = $response->headers->get('location');

        $this->assertEquals(Response::HTTP_FOUND, $response->getStatusCode());
        $this->assertEqualRoutes($expectedRoute, $location);
    }

    /**
     * @return array
     */
    public function dataProvider_testLoginAction_userLoggingIn()
    {
        return array(
            array('admin', 'admin1', 'homepage'),
            array('admin', 'invalid', 'login'),
            array('invalid', 'invalid', 'login')
        );
    }

    /**
     * @covers Quak\ShopsCoreBundle\Controller\AccessController::indexAction
     */
    public function testIndexAction_redirectToAdmin()
    {
        $username = 'admin';
        $password = 'admin1';
        $this->authenticateUser($username, $password);

        $this->client->request('GET', '/');
        $response = $this->client->getResponse();
        $location = $response->headers->get('location');

        $this->assertEquals(Response::HTTP_FOUND, $response->getStatusCode());
        $this->assertEqualRoutes('quak_shops_admin_index', $location);
    }

    /**
     * @covers Quak\ShopsCoreBundle\Controller\AccessController::indexAction
     */
    public function testIndexAction_redirectToReport()
    {
        $username = 'demoShop';
        $password = 'demo';
        $this->authenticateUser($username, $password);

        $this->client->request('GET', '/');
        $response = $this->client->getResponse();
        $location = $response->headers->get('location');

        $this->assertEquals(Response::HTTP_FOUND, $response->getStatusCode());
        $this->assertEqualRoutes('quak_shops_report_index', $location);
    }
}
