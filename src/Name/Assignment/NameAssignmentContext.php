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

final class NameAssignmentContext
{
    /**
     * @var NameAssignment[]
     */
    private $assignments = array();

    /**
     * TypeAssignmentContext constructor.
     * @param NameAssignment[] $assignments
     */
    public function __construct(array $assignments)
    {
        foreach ($assignments as $assignment) {
            $this->addAssignment($assignment);
        }
    }

    /**
     * @param NameAssignment $assignment
     * @return NameAssignmentContext
     */
    public function withAssignment(NameAssignment $assignment)
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
     * @return NameAssignment[]
     */
    public function getAssignments()
    {
        return $this->assignments;
    }

    /**
     * @internal
     * @param NameAssignment $assignment
     */
    private function addAssignment(NameAssignment $assignment)
    {
        $this->assignments[$assignment->from()->toString()] = $assignment;
    }
}