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
        $root = $builder->root('foobar', 'array', new NodeBuilder());

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
                ->arrayNode('closures')
                    ->children()
                        ->closureNode('baz')
                            ->info('baz')
                            ->defaultValue(function ($foobar) {
                                foreach (['foo', 'bar'] as $foo) {
                                    return $foobar + $foo;
                                }
                            })
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('qux')
                    ->info('qux')
                    ->defaultValue(['qux', 'qux'])
                    ->prototype('scalar')->end()
                ->end()
                ->arrayNode('ter')
                    ->info('ter')
                    ->useAttributeAsKey('name')
                    ->prototype('array')->end()
                ->end()
                ->arrayNode('qua')
                    ->info('qua')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('foo')->defaultValue('bar')->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('quin')
                    ->info('quin')
                    ->useAttributeAsKey('name')
                    ->prototype('scalar')->end()
                ->end();

        return $builder;
    }
}
