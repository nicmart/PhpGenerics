<?php
/**
 * This file is part of php-generics
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Name\Transformer;


use NicMart\Generics\Name\FullName;

/**
 * Interface NameTransformer
 * @package NicMart\Generics\Name\Transformer
 */
interface NameTransformer
{
    /**
     * @param FullName $name
     * @return FullName
     */
    public function transform(FullName $name);
}