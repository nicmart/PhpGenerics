<?php
/**
 * This file is part of php-generics
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Source\Transformer;


/**
 * Interface SourceTransformer
 * @package NicMart\Generics\Source\Transformer
 */
interface SourceTransformer
{
    /**
     * @param string $source
     * @return string
     */
    public function transform($source);
}