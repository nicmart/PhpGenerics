<?php
/**
 * This file is part of PhpStorm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Infrastructure\PhpDocumentor;


use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlockFactory;
use phpDocumentor\Reflection\DocBlockFactoryInterface;
use phpDocumentor\Reflection\Location;
use phpDocumentor\Reflection\Types;

class TypeAnnotatorBlockFactory implements DocBlockFactoryInterface
{
    public static function createInstance(array $additionalTags = [])
    {
        // TODO: Implement createInstance() method.
    }

    public function create(
        $docblock,
        Types\Context $context = null,
        Location $location = null
    ) {
        // TODO: Implement create() method.
    }

}