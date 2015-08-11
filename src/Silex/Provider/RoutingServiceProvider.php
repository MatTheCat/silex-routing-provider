<?php

namespace MatTheCat\Routing\Silex\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
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
    public function register(Application $app)
    {
        $app['router.options'] = [];

        $app['router.loader_resolver'] = $app->share(function () {
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
        });

        $app['router'] = $app->share(function (Application $app) {
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
                    'matcher_base_class' => 'Silex\RedirectableUrlMatcher',
                    'matcher_class' => 'Silex\RedirectableUrlMatcher',
                ],
                $app['request_context'],
                $app['logger']
            );

            return $router;
        });

        $app['url_matcher'] = $app->share(function (Application $app) {
            return $app['router'];
        });

        $app['url_generator'] = $app->share(function (Application $app) {
            return $app['router'];
        });
    }

    public function boot(Application $app)
    {
    }
}
