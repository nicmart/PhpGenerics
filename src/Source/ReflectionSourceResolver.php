<?php
/**
 * This file is part of php-generics
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Source;

use NicMart\Generics\Name\FullName;
use ReflectionClass;

/**
 * Class ReflectionSourceResolver
 * @package NicMart\Generics\Source
 */
class ReflectionSourceResolver implements SourceResolver
{
    /**
     * @param FullName $fullName
     * @return string
     */
    public function sourceOf(FullName $fullName)
    {
        $reflection = new ReflectionClass($fullName->toCanonicalString());

        return file_get_contents($reflection->getFileName());
    }
}