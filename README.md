[![Build Status](https://travis-ci.org/MatTheCat/silex-routing-provider.svg?branch=master)](https://travis-ci.org/MatTheCat/silex-routing-provider)

# RoutingServiceProvider

The *RoutingServiceProvider* leverages the [Symfony routing component](http://symfony.com/doc/current/components/routing/introduction.html) for Silex.

## Parameters

- **router.ressource**: Resource which will be loaded to get a route collection.
- **router.options**: [Router's options](https://github.com/symfony/Routing/blob/master/Router.php#L117).

## Services

- **router.loader_resolver**: An instance of `Symfony\Component\Config\Loader\LoaderResolver`. You can extend it to add new loaders.
- **router**: An instance of `Symfony\Component\Routing\Router`.
- **url_matcher**: Alias of **router**.
- **url_generator**: Alias of **router**.

## Registering

```php
use MatTheCat\Routing\Silex\Provider\RoutingServiceProvider;

$app->register(new RoutingServiceProvider());
```