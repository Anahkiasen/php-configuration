<?php

namespace Symfony\Component\Config\Definition\Dumpers;

use Symfony\Component\Config\Definition\BaseNode;

class JsonReferenceDumper extends PhpReferenceDumper
{
    /**
     * @param BaseNode $node
     *
     * @return string
     */
    public function dumpNode(BaseNode $node)
    {
        $reference = parent::dumpNode($node);

        // Simply convert the PHP to JSON for simicity's sake
        $reference = str_replace('<?php', null, $reference);
        $reference = eval($reference);
        $reference = json_encode($reference, JSON_PRETTY_PRINT);

        return $reference.PHP_EOL;
    }
}
