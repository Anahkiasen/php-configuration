<?php
namespace Symfony\Component\Config\Definition\Loaders;

use Symfony\Component\Config\Definition\TestCases\PhpConfigurationTestCase;
use Symfony\Component\Config\FileLocator;

class PhpLoaderTest extends PhpConfigurationTestCase
{
    /**
     * @type FileLocator
     */
    protected $locator;

    /**
     * @type PhpLoader
     */
    protected $loader;

    /**
     * Setup the tests
     */
    protected function setUp()
    {
        parent::setUp();

        $this->locator = new FileLocator($this->configuration);
        $this->loader  = new PhpLoader($this->locator);
    }

    public function testCanLoadPhpFile()
    {
        $file = $this->loader->load($this->locator->locate('foobar.php'));
        $this->assertEquals(['foo' => 'bar'], $file);
    }

    public function testCanCheckIfSupportsFile()
    {
        $supports = $this->loader->supports($this->locator->locate('foobar.json'));

        $this->assertFalse($supports);
    }
}
