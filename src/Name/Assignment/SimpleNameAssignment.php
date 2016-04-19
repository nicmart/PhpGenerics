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

use NicMart\Generics\Name\SimpleName;

/**
 * Class SimpleNameAssignment
 * @package NicMart\Generics\Name\Assignment
 */
final class SimpleNameAssignment
{
    /**
     * @var SimpleName
     */
    private $from;

    /**
     * @var SimpleName
     */
    private $to;

    /**
     * TypeAssignment constructor.
     * @param SimpleName $from
     * @param SimpleName $to
     */
    public function __construct(SimpleName $from, SimpleName $to)
    {
        $this->from = $from;
        $this->to = $to;
    }

    /**
     * @return SimpleName
     */
    public function from()
    {
        return $this->from;
    }

    /**
     * @return SimpleName
     */
    public function to()
    {
        return $this->to;
    }
}