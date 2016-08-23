<?php
/**
 * This file is part of PhpStorm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Infrastructure\PhpDocumentor\Type;

use phpDocumentor\Reflection\Type as PhpDocType;

/**
 * Class RenderedType
 *
 * This is a workaround for PhpDocReflector limitation on serialization
 * Each type is converted with a string cast. This wrapper
 * type is hardcoding the rendered string.
 *
 * @package NicMart\Generics\Infrastructure\PhpDocumentor\Type
 */
class RenderedType implements PhpDocType
{
    /**
     * @var PhpDocType
     */
    private $type;

    /**
     * @var string
     */
    private $renderedString;

    /**
     * RenderedType constructor.
     * @param PhpDocType $type
     * @param string $renderedString
     */
    public function __construct(PhpDocType $type, $renderedString)
    {
        $this->type = $type;
        $this->renderedString = $renderedString;
    }

    public function __toString()
    {
        return (string) $this->renderedString;
    }
}