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
        $this->assertEquals('http://localhost/login', $location);
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
     * @param string $username         user name
     * @param string $password         password
     * @param string $expectedRedirect expected redirect path
     *
     * @dataProvider dataProvider_testLoginAction_userLoggingIn
     */
    public function testLoginAction_userLoggingIn(
        $username,
        $password,
        $expectedRedirect
    )
    {
        $this->authenticateUser($username, $password);

        $response = $this->client->getResponse();
        $location = $response->headers->get('location');

        $this->assertEquals(Response::HTTP_FOUND, $response->getStatusCode());
        $this->assertEquals($expectedRedirect, $location);
    }

    /**
     * @return array
     */
    public function dataProvider_testLoginAction_userLoggingIn()
    {
        return array(
            array('admin', 'admin1', 'http://localhost/'),
            array('admin', 'invalid', 'http://localhost/login'),
            array('invalid', 'invalid', 'http://localhost/login')
        );
    }
}
