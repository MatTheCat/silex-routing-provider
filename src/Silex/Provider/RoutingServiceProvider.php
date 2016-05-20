<?php

namespace MatTheCat\Routing\Silex\Provider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Silex\Application;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\Routing\Loader\ClosureLoader;
use Symfony\Component\Routing\Loader\PhpFileLoader;
use Symfony\Component\Routing\Loader\XmlFileLoader;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Routing\Router;

class RoutingServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['router.options'] = [];

        $app['router.loader_resolver'] = function () {
            $fileLocator = new FileLocator();

            $loaderResolver = new LoaderResolver([
                new XmlFileLoader($fileLocator),
                new PhpFileLoader($fileLocator),
                new ClosureLoader(),
            ]);

            if (class_exists('Symfony\Component\Yaml\Parser')) {
                $loaderResolver->addLoader(new YamlFileLoader($fileLocator));
            }

            return $loaderResolver;
        };

        $app['router'] = function (Application $app) {
            $router = new Router(
                new ClosureLoader(),
                function () use ($app) {
                    if (isset($app['router.resource'])) {
                        $userLoader = new DelegatingLoader($app['router.loader_resolver']);
                        $userRoutes = $userLoader->load($app['router.resource']);
                        $app['routes']->addCollection($userRoutes);
                    }

                    $app->flush();

                    return $app['routes'];
                },
                $app['router.options'] + [
                    'debug' => isset($app['debug']) ? $app['debug'] : false,
                    'matcher_base_class' => 'Silex\Provider\Routing\RedirectableUrlMatcher',
                    'matcher_class' => 'Silex\Provider\Routing\RedirectableUrlMatcher',
                ],
                $app['request_context'],
                $app['logger']
            );

            return $router;
        };

        $app['request_matcher'] = function (Application $app) {
            return $app['router'];
        };

        $app['url_generator'] = function (Application $app) {
            return $app['router'];
        };
    }
}
