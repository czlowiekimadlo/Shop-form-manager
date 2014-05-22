<?php
namespace Quak\ShopsAdminBundle\Tests\Controller;

use Quak\ShopsCoreBundle\Tests\FunctionalTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Administration controller tests
 */
class AdminControllerTest extends FunctionalTestCase
{
    /**
     * @covers Quak\ShopsAdminBundle\Controller\AdminController::indexAction
     */
    public function testIndexAction_redirectedToLogin_notLoggedIn()
    {
        $this->client->request('GET', '/admin/');

        $response = $this->client->getResponse();
        $location = $response->headers->get('location');

        $this->assertEquals(Response::HTTP_FOUND, $response->getStatusCode());
        $this->assertRegExp('/.*\/login$/', $location);
    }
}
