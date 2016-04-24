<?php
/**
 * This file is part of php-generics
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Name\Context;

/**
 * Interface NamespaceContextExtractor
 * @package NicMart\Generics\Name\Context
 */
interface NamespaceContextExtractor
{
    /**
     * @param string $source
     * @return NamespaceContext
     */
    public function contextOf($source);
}