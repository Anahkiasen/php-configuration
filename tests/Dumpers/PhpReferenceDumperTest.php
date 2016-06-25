<?php

namespace Symfony\Component\Config\Definition\Dumpers;

use Symfony\Component\Config\Definition\Dummies\DummyConfigurationDefinition;
use Symfony\Component\Config\Definition\TestCases\PhpConfigurationTestCase;

class PhpReferenceDumperTest extends PhpConfigurationTestCase
{
    public function testCanDumpPhpConfiguration()
    {
        $reference = new DummyConfigurationDefinition();

        $dumper = new PhpReferenceDumper();
        $dumped = $dumper->dump($reference);
        $matcher = $this->configuration.'/config.php';

        $this->assertEquals(file_get_contents($matcher), $dumped);
    }
}
