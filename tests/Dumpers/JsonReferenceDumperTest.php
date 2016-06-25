<?php

namespace Symfony\Component\Config\Definition\Dumpers;

use Symfony\Component\Config\Definition\Dummies\DummyConfigurationDefinition;
use Symfony\Component\Config\Definition\TestCases\PhpConfigurationTestCase;

class JsonReferenceDumperTest extends PhpConfigurationTestCase
{
    public function testCanDumpJsonConfiguration()
    {
        $reference = new DummyConfigurationDefinition();

        $dumper = new JsonReferenceDumper();
        $dumped = $dumper->dump($reference);
        $matcher = $this->configuration.'/config.json';

        $this->assertEquals($dumped, file_get_contents($matcher));
    }
}
