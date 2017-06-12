<?php

/*
 * This file is part of "musement/monolog-fluentd-bundle".
 *
 * (c) Musement S.p.A. <oss@musement.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Musement\MonologFluentdBundle\Tests\DependencyInjection;

use Musement\MonologFluentdBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    public function testGetConfigTreeBuilder()
    {
        $configuration = new Configuration();
        $treeBuilder = $configuration->getConfigTreeBuilder();
        $this->assertInstanceOf(TreeBuilder::class, $treeBuilder);
    }
}
