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
 * Interface NamespaceContextResolver
 * @package NicMart\Generics\Name\Context
 */
interface NamespaceContextResolver
{
    /**
     * @return NamespaceContext
     */
    public function resolve();
}