<?php
/**
 * This file is part of php-generics
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Name\Assignment;

use NicMart\Generics\Name\FullName;

/**
 * Class TypeAssignment
 * @package NicMart\Generics\Name\Assignment
 */
final class TypeAssignment
{
    /**
     * @var FullName
     */
    private $from;

    /**
     * @var FullName
     */
    private $to;

    /**
     * TypeAssignment constructor.
     * @param FullName $from
     * @param FullName $to
     */
    public function __construct(FullName $from, FullName $to)
    {
        $this->from = $from;
        $this->to = $to;
    }

    /**
     * @return FullName
     */
    public function from()
    {
        return $this->from;
    }

    /**
     * @return FullName
     */
    public function to()
    {
        return $this->to;
    }
}