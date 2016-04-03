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
use PhpParser\Node\Name;

final class TypeAssignmentContext
{
    /**
     * @var TypeAssignment[]
     */
    private $assignments = array();

    /**
     * TypeAssignmentContext constructor.
     * @param TypeAssignment[] $assignments
     */
    public function __construct(array $assignments)
    {
        foreach ($assignments as $assignment) {
            $this->addAssignment($assignment);
        }
    }

    /**
     * @param TypeAssignment $assignment
     * @return TypeAssignmentContext
     */
    public function withAssignment(TypeAssignment $assignment)
    {
        $new = clone $this;

        $new->addAssignment($assignment);

        return $new;
    }

    /**
     * @param Type $type
     * @return bool
     */
    public function hasAssignmentFrom(Type $type)
    {
        return isset($this->assignments[$type->name()]);
    }

    /**
     * @param Type $type
     * @return Type
     */
    public function transformType(Type $type)
    {
        return $this->hasAssignmentFrom($type)
            ? $this->assignments[$type->name()]->to()
            : $type
        ;
    }

    /**
     * @internal
     * @param TypeAssignment $assignment
     */
    private function addAssignment(TypeAssignment $assignment)
    {
        $this->assignments[$assignment->from()->name()] = $assignment;
    }
}