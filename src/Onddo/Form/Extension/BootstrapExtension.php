<?php

namespace Onddo\Form\Extension;

use Symfony\Component\Form\AbstractExtension;
use Braincrafted\Bundle\BootstrapBundle;

class BootstrapExtension extends AbstractExtension
{
    protected function loadTypes()
    {
        return array(
            new BootstrapBundle\Form\Type\BootstrapCollectionType(),
            new BootstrapBundle\Form\Type\MoneyType()
        );
    }

    protected function loadTypeExtensions()
    {
        return array(
            new BootstrapBundle\Form\Extension\TypeSetterExtension()
        );
    }
}
