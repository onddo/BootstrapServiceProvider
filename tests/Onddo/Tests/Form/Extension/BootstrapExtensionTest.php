<?php

namespace Onddo\Tests\Form\Extension;

use Onddo\Form\Extension\BootstrapExtension;

class BootstrapExtensionTest extends \PHPUnit_Framework_TestCase
{
    protected $app;

    public function setUp()
    {
        $this->extension = new BootstrapExtension();
    }

    public function testBootstrapCollectionType()
    {
        $this->assertTrue($this->extension->hasType('bootstrap_collection'), 'bootstrap_collection type not registered');
        $this->assertInstanceOf('Braincrafted\Bundle\BootstrapBundle\Form\Type\BootstrapCollectionType', $this->extension->getType('bootstrap_collection'));
    }

    public function testMoneyType()
    {
        $this->assertTrue($this->extension->hasType('money'), 'money type not registered');
        $this->assertInstanceOf('Braincrafted\Bundle\BootstrapBundle\Form\Type\MoneyType', $this->extension->getType('money'));
    }

    public function testTypeSetterExtension()
    {
        $typeExtensions = array_filter($this->extension->getTypeExtensions('form'), function($value) {
            return $value instanceof \Braincrafted\Bundle\BootstrapBundle\Form\Extension\TypeSetterExtension;
        });
        $this->assertCount(1, $typeExtensions, 'TypeSetterExtension not registered');
    }
}
