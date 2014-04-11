<?php

namespace Onddo\Tests\Silex;

use Onddo\Silex\BootstrapServiceProvider;
use Onddo\Form\Extension\BootstrapExtension;
use Silex\Application;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\FormServiceProvider;
use Silex\Provider\SessionServiceProvider;
use SilexAssetic\AsseticServiceProvider;

class BootstrapServiceProviderTest extends \PHPUnit_Framework_TestCase
{
    protected $app;

    public function setUp()
    {
        $this->app = new Application();
        $this->app->register(new TwigServiceProvider());
        $this->app->register(new FormServiceProvider());
        $this->app->register(new SessionServiceProvider());
        $this->app->register(new AsseticServiceProvider(), array(
            'assetic.path_to_web' => sys_get_temp_dir(),
            'assetic.filters' => $this->app->protect(function($fm) {
                $fm->set('less', new \Assetic\Filter\LessFilter());
            })
        ));
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

    public function testTwigAutomaticConfiguration()
    {
        $this->app['bootstrap.auto_configure'] = array('twig' => true);
        $this->app->boot();
        $this->assertContains('@bootstrap/Form/bootstrap.html.twig', $this->app['twig.form.templates'], 'Bootstrap form template should be registered');
    }

    public function testTwigManualConfiguration()
    {
        $this->app['bootstrap.auto_configure'] = array('twig' => false);
        $this->app->boot();
        $this->assertNotContains('@bootstrap/Form/bootstrap.html.twig', $this->app['twig.form.templates'], 'Bootstrap form template should not be registered');
    }

    public function testAsseticAutomaticConfiguration()
    {
        $this->app['bootstrap.auto_configure'] = array('assetic' => true);
        $this->app->boot();
        $am = $this->app['assetic.asset_manager'];
        $this->assertTrue($am->has('bootstrap_css'), 'Bootstrap CSS should be configured on Assetic Manager');
        $this->assertTrue($am->has('bootstrap_js'), 'Bootstrap JS should be configured on Assetic Manager');
        $this->assertTrue($am->has('jquery'), 'JQuery should be configured on Assetic Manager');
    }

    public function testAsseticManualConfiguration()
    {
        $this->app['bootstrap.auto_configure'] = array('assetic' => false);
        $this->app->boot();
        $am = $this->app['assetic.asset_manager'];
        $this->assertFalse($am->has('bootstrap_css'), 'Bootstrap CSS should not be configured on Assetic Manager');
        $this->assertFalse($am->has('bootstrap_js'), 'Bootstrap JS should not be configured on Assetic Manager');
        $this->assertFalse($am->has('jquery'), 'JQuery should not be configured on Assetic Manager');
    }
}
