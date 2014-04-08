<?php

namespace Onddo\Tests\Silex;

use Onddo\Silex\BootstrapServiceProvider;
use Onddo\Form\Extension\BootstrapExtension;
use Silex\Application;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\FormServiceProvider;
use Silex\Provider\SessionServiceProvider;

class BootstrapServiceProviderTest extends \PHPUnit_Framework_TestCase
{
    protected $app;

    public function setUp()
    {
        $this->app = new Application();
        $this->app->register(new TwigServiceProvider());
        $this->app->register(new FormServiceProvider());
        $this->app->register(new SessionServiceProvider());
        $this->app->register(new BootstrapServiceProvider());
    }

    public function testTwigExtensions()
    {
        $this->assertTrue($this->app['twig']->hasExtension('braincrafted_bootstrap_badge'), 'braincrafted_bootstrap_badge extension not registered on Twig');
        $this->assertTrue($this->app['twig']->hasExtension('braincrafted_bootstrap_form'), 'braincrafted_bootstrap_form extension not registered on Twig');
        $this->assertTrue($this->app['twig']->hasExtension('braincrafted_bootstrap_icon'), 'braincrafted_bootstrap_icon extension not registered on Twig');
        $this->assertTrue($this->app['twig']->hasExtension('braincrafted_bootstrap_label'), 'braincrafted_bootstrap_label extension not registered on Twig');
    }

    public function testTwigLoader() {
        $this->assertContains('bootstrap', $this->app['twig.loader.filesystem']->getNamespaces(), 'Bootstrap namespace not registered on Twig');
        $this->assertStringEndsWith('/vendor/braincrafted/bootstrap-bundle/Braincrafted/Bundle/BootstrapBundle/Resources/views', $this->app['twig.loader.filesystem']->getPaths('bootstrap')[0], 'Bootstrap template path not registered on Twig');
    }

    public function testFormExtension()
    {
        $extensions = array_filter($this->app['form.extensions'], function($value) {
            return $value instanceof BootstrapExtension;
        });
        $this->assertCount(1, $extensions, 'BootstrapExtension not registered');
    }

    public function testFlashMessage()
    {
        $this->assertInstanceOf('Braincrafted\Bundle\BootstrapBundle\Session\FlashMessage', $this->app['bootstrap.flash']);
    }
}
