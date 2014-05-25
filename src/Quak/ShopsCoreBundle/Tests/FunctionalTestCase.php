<?php
namespace Quak\ShopsCoreBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Abstract class for functional tests
 */
abstract class FunctionalTestCase extends WebTestCase
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var Symfony\Component\Routing\Router
     */
    protected $router;

    /**
     * @var Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    /**
     * Tests setup
     */
    public function setUp()
    {
        $this->client = static::createClient();

        $this->container = $this->client->getContainer();

        $this->router = $this->container->get('router');
    }

    /**
     * Tests tear down
     */
    public function tearDown()
    {
        $this->client = null;
    }

    /**
     * @param string $username user name
     * @param string $password password
     */
    protected function authenticateUser($username, $password)
    {
        $crawler = $this->client->request('GET', '/login');
        $form = $crawler->selectButton('submit')->form();
        $form['_username'] = $username;
        $form['_password'] = $password;

        $this->client->submit($form);
    }

    /**
     * @param string $route      route alias
     * @param array  $parameters route parameters
     *
     * @return string
     */
    protected function generateUrl($route, array $parameters = array())
    {
        return $this->router->generate($route, $parameters);
    }

    /**
     * @param string      $route      expected route name
     * @param string      $url        url to test
     * @param string|null $comment    assertion comment
     * @param array       $parameters expected route parameters
     */
    protected function assertEqualRoutes($route, $url, $comment = null, array $parameters = array())
    {
        $testedRoute = str_replace('http://localhost', '', $url);
        $expectedRoute = $this->router->generate($route, $parameters);

        $this->assertEquals($expectedRoute, $testedRoute, $comment);
    }
}
