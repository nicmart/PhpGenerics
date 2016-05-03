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

use NicMart\Generics\Name\Context\NamespaceContext;
use NicMart\Generics\Name\Name;

/**
 * Class AutoloadNameTransformer
 *
 * Just have a side-effect of autoloading the class
 *
 * @package NicMart\Generics\Name\Transformer
 */
class AutoloadNameTransformer implements NameTransformer
{
    public function transformName(
        Name $name,
        NamespaceContext $namespaceContext
    ) {
        class_exists($namespaceContext->qualify($name)->toString());

        return $name;
    }
}