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
     * Tests setup
     */
    public function setUp()
    {
        $this->client = static::createClient();
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
}
