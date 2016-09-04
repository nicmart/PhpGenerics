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
use NicMart\Generics\Type\Type;

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
     * @var string
     */
    private $source;

    /**
     * @var Type
     */
    private $type;

    /**
     * SourceUnit constructor.
     * @param Type $type
     * @param FullName $fullName
     * @param $source
     */
    public function __construct(
        Type $type,
        FullName $fullName,
        $source
    ) {
        $this->fullName = $fullName;
        $this->source = $source;
        $this->type = $type;
    }

    /**
     * @return Type
     */
    public function type()
    {
        return $this->type;
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