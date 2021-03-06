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
        $this->client->request('GET',
            $this->generateUrl('quak_shops_admin_index'));

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
        $username = 'admin';
        $password = 'admin1';
        $this->authenticateUser($username, $password);

        $this->client->request('GET',
            $this->generateUrl('quak_shops_admin_index'));
        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    /**
     * @covers Quak\ShopsAdminBundle\Controller\AdminController::newUserAction
     */
    public function testNewUserAction_formOpens_loggedAsAdmin()
    {
        $username = 'admin';
        $password = 'admin1';
        $this->authenticateUser($username, $password);

        $this->client->request('GET',
            $this->generateUrl('quak_shops_admin_user_new'));
        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    /**
     * @covers Quak\ShopsAdminBundle\Controller\AdminController::editUserAction
     */
    public function testEditUserAction_formOpens_loggedAsAdmin()
    {
        $username = 'admin';
        $password = 'admin1';
        $this->authenticateUser($username, $password);

        $this->client->request('GET',
            $this->generateUrl('quak_shops_admin_user_edit', array(
                'userId' => 1
        )));
        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    /**
     * @covers Quak\ShopsAdminBundle\Controller\AdminController::editUserAction
     */
    public function testEditUserAction_notFound_invalidId()
    {
        $username = 'admin';
        $password = 'admin1';
        $this->authenticateUser($username, $password);

        $this->client->request('GET',
            $this->generateUrl('quak_shops_admin_user_edit', array(
                'userId' => 999999999
        )));
        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }
}
