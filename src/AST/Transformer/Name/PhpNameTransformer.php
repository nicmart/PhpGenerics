<?php
/**
 * This file is part of PhpStorm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\AST\Transformer\Name;

use PhpParser\Node\Name;

/**
 * Interface PhpNameTransformer
 * @package NicMart\Generics\AST\Transformer\Name
 */
interface PhpNameTransformer
{
    /**
     * @param Name $phpName
     * @return Name
     */
    public function __invoke(Name $phpName);
}