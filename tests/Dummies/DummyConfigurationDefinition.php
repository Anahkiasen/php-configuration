<?php
namespace Symfony\Component\Config\Definition\Dummies;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\TreeBuilder\NodeBuilder;

class DummyConfigurationDefinition implements ConfigurationInterface
{
    /**
     * Generates the configuration tree builder.
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        $builder = new TreeBuilder();
        $root    = $builder->root('foobar', 'array', new NodeBuilder());

        $root
            ->info('The main configuration of your application')
            ->children()
                ->scalarNode('foo')
                ->info('foo')
                ->defaultValue('foo')
                ->isRequired()
                ->cannotBeEmpty()
            ->end()
            ->arrayNode('bar')
                ->info("bar\nbar")
                ->example(['bar', 'bar'])
                ->prototype('scalar')->end()
            ->end()
            ->closureNode('baz')
                ->info('baz')
                ->defaultValue(function ($foobar) {
                    return $foobar + 3;
                })
            ->end()
            ->arrayNode('qux')
                ->info('qux')
                ->defaultValue(['qux', 'qux'])
                ->prototype('scalar')->end()
            ->end();

        return $builder;
    }
}