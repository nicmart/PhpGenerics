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
     * @param FullName $type
     * @return bool
     */
    public function hasAssignmentFrom(FullName $type)
    {
        return isset($this->assignments[$type->toString()]);
    }

    /**
     * @param FullName $type
     * @return FullName
     */
    public function transformType(FullName $type)
    {
        return $this->hasAssignmentFrom($type)
            ? $this->assignments[$type->toString()]->to()
            : $type
        ;
    }

    /**
     * @return TypeAssignment[]
     */
    public function getAssignments()
    {
        return $this->assignments;
    }

    /**
     * @internal
     * @param TypeAssignment $assignment
     */
    private function addAssignment(TypeAssignment $assignment)
    {
        $this->assignments[$assignment->from()->toString()] = $assignment;
    }
}