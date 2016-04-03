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

use NicMart\Generics\Type\Context\Namespace_;

/**
 * Class TypeAssignment
 * @package NicMart\Generics\Type\Assignment
 */
final class NamespaceAssignment
{
    /**
     * @var Namespace_
     */
    private $from;

    /**
     * @var Namespace_
     */
    private $to;

    /**
     * TypeAssignment constructor.
     * @param Namespace_ $from
     * @param Namespace_ $to
     */
    public function __construct(Namespace_ $from, Namespace_ $to)
    {
        $this->from = $from;
        $this->to = $to;
    }

    /**
     * @return Namespace_
     */
    public function from()
    {
        return $this->from;
    }

    /**
     * @return Namespace_
     */
    public function to()
    {
        return $this->to;
    }
}