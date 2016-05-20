<?php

namespace MatTheCat\Tests\Silex\Provider;

use MatTheCat\Routing\Silex\Provider\RoutingServiceProvider;
use org\bovigo\vfs\vfsStream;
use Silex\Application;
use Symfony\Component\Yaml\Yaml;

class RoutingServiceProviderTest extends \PHPUnit_Framework_TestCase
{
    /** @var \org\bovigo\vfs\vfsStreamDirectory */
    private $root;

    public function setUp()
    {
        $this->root = vfsStream::setup('root');
    }

    public function testEmptyConfiguration()
    {
        $app = new Application();
        $app->register(new RoutingServiceProvider());

        $this->assertContainsOnlyInstancesOf(
            '\Symfony\Component\Routing\Router',
            [$app['router'], $app['url_generator'], $app['request_matcher']]
        );
    }

    public function testDropIn()
    {
        $app = new Application();
        $app->register(new RoutingServiceProvider());

        $app->get('/fake', null);

        $this->assertSame($app['router']->getRouteCollection(), $app['routes']);
        $this->assertCount(1, $app['routes']);
    }

    public function testRoutingFile()
    {
        $routingConfiguration = [
            'fake' => [
                'path' => '/fake',
                'defaults' => ['_controller' => null],
            ],
        ];
        $routingFile = $this->root->url().'/routing.yml';
        $handle = fopen($routingFile, 'w');
        fwrite($handle, Yaml::dump($routingConfiguration));
        fclose($handle);

        $app = new Application();
        $app->register(new RoutingServiceProvider(), [
            'router.resource' => $routingFile,
        ]);

        $app->boot();

        /** @var \Symfony\Component\Routing\RouterInterface $router */
        $router = $app['router'];

        $this->assertCount(1, $router->getRouteCollection());
        $this->assertSame('/fake', $router->generate('fake'));
    }
}
