<?php
/**
 * This file is part of PhpStorm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Name\Transformer;

use NicMart\Generics\Name\Name;

/**
 * Interface NameTransformer
 *
 * A Name => Name function
 *
 * @package NicMart\Generics\Name\Transformer
 */
interface NameTransformer
{
    /**
     * @param Name $name
     * @return Name
     */
    public function __invoke(Name $name);
}