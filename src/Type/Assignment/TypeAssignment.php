<?php
/**
 * This file is part of php-generics
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Type\Assignment;

use NicMart\Generics\Type\Type;

/**
 * Class TypeAssignment
 * @package NicMart\Generics\Type\Assignment
 */
final class TypeAssignment
{
    /**
     * @var Type
     */
    private $from;

    /**
     * @var Type
     */
    private $to;

    /**
     * TypeAssignment constructor.
     * @param Type $from
     * @param Type $to
     */
    public function __construct(Type $from, Type $to)
    {
        $this->from = $from;
        $this->to = $to;
    }

    /**
     * @return Type
     */
    public function from()
    {
        return $this->from;
    }

    /**
     * @return Type
     */
    public function to()
    {
        return $this->to;
    }
}