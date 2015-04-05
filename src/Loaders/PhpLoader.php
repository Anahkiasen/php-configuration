<?php
namespace Symfony\Component\Config\Definition\Loaders;

use Symfony\Component\Config\Loader\FileLoader;

class PhpLoader extends FileLoader
{
    /**
     * Loads a resource.
     *
     * @param mixed       $resource The resource
     * @param string|null $type     The resource type or null if unknown
     *
     * @throws \Exception If something went wrong
     *
     * @return array
     */
    public function load($resource, $type = null)
    {
        return include $resource;
    }

    /**
     * Returns whether this class supports the given resource.
     *
     * @param mixed       $resource A resource
     * @param string|null $type     The resource type or null if unknown
     *
     * @return bool True if this class supports the given resource, false otherwise
     */
    public function supports($resource, $type = null)
    {
        return is_string($resource) && pathinfo($resource, PATHINFO_EXTENSION) === 'php';
    }
}
