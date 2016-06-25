<?php

namespace Symfony\Component\Config\Definition\TestCases;

use PHPUnit_Framework_TestCase;

abstract class PhpConfigurationTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $configuration;

    /**
     * Set up the tests.
     */
    protected function setUp()
    {
        parent::setUp();

        $this->configuration = __DIR__.'/../_configuration';
    }
}
