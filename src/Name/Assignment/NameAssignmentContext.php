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
     * @param array $assignments
     * @return NameAssignmentContext
     */
    public static function fromStrings(array $assignments)
    {
        $parsedAssignments = array();

        foreach ($assignments as $from => $to) {
            $parsedAssignments[] = new NameAssignment(
                FullName::fromString($from),
                FullName::fromString($to)
            );
        }

        return new self($parsedAssignments);
    }

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
     * @param FullName $name
     * @return bool
     */
    public function hasAssignmentFrom(FullName $name)
    {
        return isset($this->assignments[$name->toString()]);
    }

    /**
     * @param FullName $name
     * @return FullName
     */
    public function transformName(FullName $name)
    {
        return $this->hasAssignmentFrom($name)
            ? $this->assignments[$name->toString()]->to()
            : $name
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