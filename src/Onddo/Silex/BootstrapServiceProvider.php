<?php

namespace Onddo\Silex;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Braincrafted\Bundle\BootstrapBundle;
use Onddo\Form\Extension\BootstrapExtension;
use Assetic\Asset\AssetCollection;
use Assetic\Asset\FileAsset;
use Assetic\Asset\GlobAsset;

class BootstrapServiceProvider implements ServiceProviderInterface
{

    function register(Application $app)
    {
        $vendorDir = is_dir(__DIR__.'/../../../vendor')
            ? realpath(__DIR__.'/../../../vendor')
            : realpath(__DIR__.'/../../../../../../vendor')
        ;

        $app['bootstrap.auto_configure'] = array();
        $app['bootstrap.assets_dir'] = $vendorDir.'/twbs/bootstrap';
        $app['bootstrap.jquery_path'] = $vendorDir.'/yiisoft/jquery/jquery.js';
        $app['bootstrap.braincrafted_assets_dir'] = $vendorDir.'/braincrafted/bootstrap-bundle/Braincrafted/Bundle/BootstrapBundle/Resources';

        $app['twig'] = $app->share($app->extend('twig', function($twig, $app) {
            $twig->addExtension(new BootstrapBundle\Twig\BootstrapBadgeExtension);
            $twig->addExtension(new BootstrapBundle\Twig\BootstrapFormExtension);
            $twig->addExtension(new BootstrapBundle\Twig\BootstrapIconExtension);
            $twig->addExtension(new BootstrapBundle\Twig\BootstrapLabelExtension);

            return $twig;
        }));

        $app['twig.loader.filesystem'] = $app->share($app->extend('twig.loader.filesystem',
            function (\Twig_Loader_Filesystem $twigLoaderFilesystem) use ($app) {
                $twigLoaderFilesystem->addPath($app['bootstrap.braincrafted_assets_dir'].'/views', 'bootstrap');
                return $twigLoaderFilesystem;
            }
        ));

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
        $autoConfigure = $app['bootstrap.auto_configure'] = array_replace(array(
            'twig' => true,
            'assetic' => true
        ), $app['bootstrap.auto_configure']);

        if ($autoConfigure['twig']) {
            $app['twig.form.templates'] = array_merge($app['twig.form.templates'], array('@bootstrap/Form/bootstrap.html.twig'));
        }

        if (isset($app['assetic.asset_manager']) && $autoConfigure['assetic']) {
            $this->configureAssetic($app);
        }
    }

    protected function configureAssetic(Application $app)
    {
        if (!isset($app['assetic.path_to_web'])) {
            throw new \RuntimeException('You must set "assetic.path_to_web" option to use Assetic automatic configuration');
        }

        $app['assetic.asset_manager'] = $app->share(
            $app->extend('assetic.asset_manager', function($am, $app) {
                $bootstrap_css = new AssetCollection(
                    array(
                        new FileAsset($app['bootstrap.assets_dir'].'/less/bootstrap.less'),
                        new FileAsset($app['bootstrap.braincrafted_assets_dir'].'/less/form.less')
                    ),
                    array($app['assetic.filter_manager']->get('less'))
                );
                $bootstrap_css->setTargetPath('css/bootstrap.css');
                $am->set('bootstrap_css', $bootstrap_css);

                $bootstrap_js = new AssetCollection(
                    array(
                        new GlobAsset($app['bootstrap.assets_dir'].'/js/*.js'),
                        new FileAsset($app['bootstrap.braincrafted_assets_dir'].'/js/bc-bootstrap-collection.js')
                    )
                );
                $bootstrap_js->setTargetPath('js/bootstrap.js');
                $am->set('bootstrap_js', $bootstrap_js);

                $jquery = new FileAsset($app['bootstrap.jquery_path']);
                $jquery->setTargetPath('js/jquery.js');
                $am->set('jquery', $jquery);

                return $am;
            })
        );
    }
}
