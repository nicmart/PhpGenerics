<?php
/**
 * This file is part of PhpStorm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Infrastructure\PhpParser\Serializer;

use NicMart\Generics\AST\Serializer\Serializer;
use PhpParser\PrettyPrinterAbstract;

/**
 * Class PhpParserSerializer
 * @package NicMart\Generics\Infrastructure\PhpParser
 */
class PhpParserSerializer implements Serializer
{
    /**
     * @var PrettyPrinterAbstract
     */
    private $phpParserSerializer;

    /**
     * PrettyPrinterAbstract constructor.
     * @param PrettyPrinterAbstract $phpParserSerializer
     */
    public function __construct(PrettyPrinterAbstract $phpParserSerializer)
    {
        $this->phpParserSerializer = $phpParserSerializer;
    }

    /**
     * @param array $nodes
     * @return mixed|string
     */
    public function serialize(array $nodes)
    {
        return $this->phpParserSerializer->prettyPrint($nodes);
    }
}