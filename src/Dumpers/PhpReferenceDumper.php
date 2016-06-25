<?php

namespace Symfony\Component\Config\Definition\Dumpers;

use Symfony\Component\Config\Definition\BaseNode;

/**
 * Dumps a Symfony reference in PHP format.
 *
 * @author Maxime Fabre <ehtnam6@gmail.com>
 */
class PhpReferenceDumper extends AbstractReferenceDumper
{
    /**
     * @param BaseNode $node
     *
     * @return string
     */
    public function dumpNode(BaseNode $node)
    {
        return '<?php'.PHP_EOL.PHP_EOL.'return ['.parent::dumpNode($node).PHP_EOL.'];'.PHP_EOL;
    }

    /**
     * @param mixed $value
     *
     * @return string
     */
    protected function serializeValue($value)
    {
        return var_export($value, true);
    }
}
