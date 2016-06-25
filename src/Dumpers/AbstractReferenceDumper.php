<?php

namespace Symfony\Component\Config\Definition\Dumpers;

use SuperClosure\Analyzer\TokenAnalyzer;
use Symfony\Component\Config\Definition\ArrayNode;
use Symfony\Component\Config\Definition\BaseNode;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\EnumNode;
use Symfony\Component\Config\Definition\PrototypedArrayNode;
use Symfony\Component\Config\Definition\ScalarNode;
use Symfony\Component\Config\Definition\TreeBuilder\ClosureNode;

abstract class AbstractReferenceDumper
{
    /**
     * @var array
     */
    protected $comments = [];

    /**
     * @var string
     */
    protected $default;

    /**
     * @var array
     */
    protected $defaultArray;

    /**
     * @var string|string[]
     */
    protected $example;

    /**
     * @var bool
     */
    protected $isCoreNode;

    /**
     * @var string
     */
    protected $reference;

    /**
     * @param ConfigurationInterface $configuration
     *
     * @return string
     */
    public function dump(ConfigurationInterface $configuration)
    {
        return $this->dumpNode($configuration->getConfigTreeBuilder()->buildTree());
    }

    /**
     * @param BaseNode $node
     *
     * @return string
     */
    public function dumpNode(BaseNode $node)
    {
        $this->reference = '';
        $this->writeNode($node, 1);

        $reference = $this->reference;
        $this->reference = null;

        return $reference;
    }

    //////////////////////////////////////////////////////////////////////
    ////////////////////////////// OUTPUT ////////////////////////////////
    //////////////////////////////////////////////////////////////////////

    /**
     * @param BaseNode $node
     * @param int      $depth
     * @param bool     $asComment
     */
    private function writeNode(BaseNode $node, $depth = 0, $asComment = false)
    {
        // Reinitialize fields
        $this->comments = [];
        $this->default = '';
        $this->defaultArray = null;
        $children = null;
        $this->example = $node->getExample();
        $this->isCoreNode = $node->getParent() && $node->getParent()->getName() === 'rocketeer';

        if ($node instanceof ArrayNode) {
            $children = $node->getChildren();

            if ($node instanceof PrototypedArrayNode) {
                $prototype = $node->getPrototype();

                if ($prototype instanceof ArrayNode) {
                    $children = $prototype->getChildren();
                }

                // Check for attribute as key
                if ($key = $node->getKeyAttribute()) {
                    /* @var ArrayNode|ScalarNode $keyNode */
                    $keyNodeClass = 'Symfony\Component\Config\Definition\\'.($prototype instanceof ArrayNode ? 'ArrayNode' : 'ScalarNode');
                    $keyNode = new $keyNodeClass($key, $node);
                    $keyNode->setInfo('Prototype');

                    // Add children
                    foreach ($children as $childNode) {
                        $keyNode->addChild($childNode);
                    }

                    $children = [$key => $keyNode];
                }

                if (!$children) {
                    if ($node->hasDefaultValue() && count($this->defaultArray = $node->getDefaultValue())) {
                        $this->default = '';
                    } elseif (!is_array($this->example)) {
                        $this->default = [];
                    }
                }
            }
        } elseif ($node instanceof EnumNode) {
            $this->comments[] = 'One of '.implode(', ', array_map('json_encode', $node->getValues()));
            $this->default = $node->getDefaultValue();
        } elseif ($node instanceof ClosureNode) {
            $this->default = $node->getDefaultValue();
            $analyzer = new TokenAnalyzer();
            $this->default = $analyzer->analyze($this->default)['code'];
            $this->default = preg_replace_callback('/(\n +)(.+)/', function ($line) use ($depth) {
                return substr($line[1], 0, $depth * -4).$line[2];
            }, $this->default);
        } else {
            $this->default = null;

            if ($node->hasDefaultValue()) {
                $this->default = $node->getDefaultValue();

                if (is_array($this->default)) {
                    if (count($this->defaultArray = $node->getDefaultValue())) {
                        $this->default = '';
                    } elseif (!is_array($this->example)) {
                        $this->default = [];
                    }
                }
            }
        }

        // required?
        if ($node->isRequired()) {
            $this->comments[] = 'Required';
        }

        // example
        if ($this->example && !is_array($this->example)) {
            $this->comments[] = 'Example: '.$this->example;
        }

        // Format comments and values
        $this->comments = count($this->comments) ? '// '.implode(', ', $this->comments) : '';
        $name = $this->serializeValue($node->getName()).' => ';
        $format = $asComment ? '// %s%s %s' : '%s%s %s';

        if ($node instanceof ArrayNode) {
            $name .= '[';
            $this->default = (!$this->example && !$children && !$this->defaultArray) ? '],' : null;
        } elseif ($node instanceof ClosureNode) {
            $this->default .= ',';
        } else {
            $this->default = $this->default === "\n" ? '"\n"' : $this->serializeValue($this->default);
            $this->default .= ',';
        }

        // Output informations
        $this->outputInformations($node, $depth);
        $text = rtrim(sprintf($format, $name, $this->default, $this->comments), ' ');

        // Output main value
        $this->writeLine($text, $depth * 4);

        $this->outputDefaults($depth);
        $this->outputExamples($depth);

        if ($node instanceof ArrayNode) {
            $this->outputChildren($children, $depth);
        }
    }

    /**
     * Output an array.
     *
     * @param array $array
     * @param int   $depth
     * @param bool  $comments
     */
    private function writeArray(array $array, $depth, $comments = false)
    {
        // Else dump each value on its own line
        $isIndexed = array_values($array) === $array;
        $method = $comments ? 'writeComment' : 'writeLine';

        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $val = '';
            } else {
                $val = $value;
            }

            $key = $this->serializeValue($key);
            $val = $this->serializeValue($val);
            if ($isIndexed) {
                $this->$method($val.',', $depth);
            } else {
                $this->$method(sprintf('%s => %s,', $key, $val), $depth);
            }

            if (is_array($value)) {
                $this->writeArray($value, $depth + 1);
            }
        }
    }

    //////////////////////////////////////////////////////////////////////
    ///////////////////////////// OUTPUTTERS /////////////////////////////
    //////////////////////////////////////////////////////////////////////

    /**
     * Output the node's examples.
     *
     * @param $depth
     */
    private function outputExamples($depth)
    {
        if (is_array($this->example)) {
            $message = count($this->example) > 1 ? 'Examples' : 'Example';

            $this->writeComment($message.':', $depth * 4 + 4);
            $this->writeArray($this->example, $depth * 4 + 4, true);
        }
    }

    /**
     * Output the node's default value.
     *
     * @param int $depth
     */
    private function outputDefaults($depth)
    {
        if ($this->defaultArray) {
            $message = count($this->defaultArray) > 1 ? 'Defaults' : 'Default';
            $childDepth = $depth * 4 + 4;

            $this->writeComment($message.':', $childDepth);
            $this->writeArray($this->defaultArray, $childDepth);
        }
    }

    /**
     * Output the children of the node.
     *
     * @param BaseNode[] $children
     * @param int        $depth
     */
    private function outputChildren($children, $depth)
    {
        if ($children) {
            foreach ($children as $childNode) {
                if ($childNode->getInfo() === 'Prototype') {
                    $childNode->setInfo('');
                    $this->writeNode($childNode, $depth + 1, true);
                } else {
                    $this->writeNode($childNode, $depth + 1);
                }
            }
        }

        if ($children || $this->example || $this->defaultArray) {
            $this->writeLine('],', $depth * 4);
        }
    }

    /**
     * @param BaseNode $node
     * @param int      $depth
     */
    private function outputInformations(BaseNode $node, $depth)
    {
        if ($info = $node->getInfo()) {
            $this->writeLine('');

            $info = str_replace("\n", sprintf("\n%".($depth * 4).'s// ', ' '), $info);
            $this->writeComment($info, $depth * 4);

            if ($this->isCoreNode) {
                $this->writeLine(str_repeat('/', 70), $depth * 4);
                $this->writeLine('');
            }
        }
    }

    //////////////////////////////////////////////////////////////////////
    ////////////////////////////// HELPERS ///////////////////////////////
    //////////////////////////////////////////////////////////////////////

    /**
     * @param mixed $value
     *
     * @return string
     */
    protected function serializeValue($value)
    {
        return $value;
    }

    /**
     * Outputs a single config reference line.
     *
     * @param string $text
     * @param int    $indent
     */
    protected function writeLine($text, $indent = 0)
    {
        $indent = strlen($text) + $indent;
        $format = '%'.$indent.'s';

        $this->reference .= sprintf($format, $text)."\n";
    }

    /**
     * @param string $comment
     * @param int    $depth
     */
    protected function writeComment($comment, $depth)
    {
        $this->writeLine('// '.$comment, $depth);
    }
}
