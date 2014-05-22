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
        $this->assertRegExp('/.*\/login$/', $location);
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


}
