<?php
/**
 * This file is part of php-generics
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\AST\Name;

use NicMart\Generics\Name\Context\NamespaceContext;
use PhpParser\Node\Name;

/**
 * Interface PhpParserNameTransformer
 * @package NicMart\Generics\AST\Name
 */
interface PhpParserNameTransformer
{
    /**
     * @param Name $name
     * @param NamespaceContext $namespaceContext
     * @return mixed
     */
    public function transform(Name $name, NamespaceContext $namespaceContext);
}