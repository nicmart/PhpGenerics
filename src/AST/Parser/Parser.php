<?php
/**
 * This file is part of PhpStorm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\AST\Parser;

/**
 * Interface Parser
 * @package NicMart\Generics\AST\Parser
 */
interface Parser
{
    /**
     * @param string $source
     * @return array The AST nodes
     */
    public function parse($source);
}