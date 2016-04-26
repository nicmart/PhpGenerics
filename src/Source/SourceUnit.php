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

/**
 * Class SourceUnit
 * @package NicMart\Generics\Source
 */
final class SourceUnit
{
    /**
     * @var FullName
     */
    private $fullName;
    /**
     * @var
     */
    private $source;

    /**
     * SourceUnit constructor.
     * @param FullName $fullName
     * @param $source
     */
    public function __construct(
        FullName $fullName,
        $source
    ) {
        $this->fullName = $fullName;
        $this->source = $source;
    }

    /**
     * @return FullName
     */
    public function name()
    {
        return $this->fullName;
    }

    /**
     * @return string
     */
    public function source()
    {
        return $this->source;
    }
}