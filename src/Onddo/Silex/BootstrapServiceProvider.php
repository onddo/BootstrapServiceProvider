<?php

namespace Onddo\Silex;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Braincrafted\Bundle\BootstrapBundle;
use Onddo\Form\Extension\BootstrapExtension;

class BootstrapServiceProvider implements ServiceProviderInterface
{

    function register(Application $app)
    {
        $app['twig'] = $app->share($app->extend('twig', function($twig, $app) {
            $twig->addExtension(new BootstrapBundle\Twig\BootstrapBadgeExtension);
            $twig->addExtension(new BootstrapBundle\Twig\BootstrapFormExtension);
            $twig->addExtension(new BootstrapBundle\Twig\BootstrapIconExtension);
            $twig->addExtension(new BootstrapBundle\Twig\BootstrapLabelExtension);

            return $twig;
        }));

        $app['twig.loader.filesystem'] = $app->share($app->extend('twig.loader.filesystem',
            function (\Twig_Loader_Filesystem $twigLoaderFilesystem) {
                $vendorDir = is_dir(__DIR__.'/../../../vendor')
                    ? realpath(__DIR__.'/../../../vendor')
                    : realpath(__DIR__.'/../../../../../../vendor')
                ;
                $twigLoaderFilesystem->addPath($vendorDir.'/braincrafted/bootstrap-bundle/Braincrafted/Bundle/BootstrapBundle/Resources/views', 'bootstrap');
                return $twigLoaderFilesystem;
            }
        ));

        $app['twig.form.templates'] = array_merge($app['twig.form.templates'], array('@bootstrap/Form/bootstrap.html.twig'));

        if (isset($app['form.extensions'])) {
            $app['form.extensions'] = $app->share($app->extend('form.extensions', function ($extensions) use ($app) {
                $extensions[] = new BootstrapExtension();

                return $extensions;
            }));
        }

        if (isset($app['session'])) {
            $app['bootstrap.flash'] = $app->share(function ($app) {
                return new BootstrapBundle\Session\FlashMessage($app['session']);
            });
        }
    }

    function boot(Application $app)
    {

    }
}
