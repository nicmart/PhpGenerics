<?php
/**
 * This file is part of php-generics
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Source\Generic;

use NicMart\Generics\Name\FullName;
use NicMart\Generics\Name\Generic\Assignment\GenericNameAssignment;
use NicMart\Generics\Name\Generic\GenericName;
use NicMart\Generics\Source\Transformer\SourceTransformer;

/**
 * Interface GenericTransformerProvider
 * @package NicMart\Generics\Source\Generic
 */
interface GenericTransformerProvider
{
    /**
     * @param GenericNameAssignment $genericNameAssignment
     * @return SourceTransformer
     */
    public function transformer(
        GenericNameAssignment $genericNameAssignment
    );
}