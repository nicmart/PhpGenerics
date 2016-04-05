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

use PhpParser\Node\Name;

/**
 * Class NamespaceAssignmentContext
 * @package NicMart\Generics\Type\Assignment
 */
final class NamespaceAssignmentContext
{
    /**
     * @var NamespaceAssignment[]
     */
    private $assignments = array();

    /**
     * NamespaceAssignmentContext constructor.
     * @param NamespaceAssignment[] $assignments
     */
    public function __construct(array $assignments)
    {
        foreach ($assignments as $assignment) {
            $this->addAssignment($assignment);
        }
    }

    /**
     * @param NamespaceAssignment $assignment
     * @return NamespaceAssignmentContext
     */
    public function withAssignment(NamespaceAssignment $assignment)
    {
        $new = clone $this;

        $new->addAssignment($assignment);

        return $new;
    }

    /**
     * @param Namespace_ $namespace
     * @return bool
     */
    public function hasAssignmentFrom(Namespace_ $namespace)
    {
        return isset($this->assignments[$namespace->toString()]);
    }

    /**
     * @param Namespace_ $namespace
     * @return Namespace_
     */
    public function transformNamespace(Namespace_ $namespace)
    {
        return $this->hasAssignmentFrom($namespace)
            ? $this->assignments[$namespace->toString()]->to()
            : $namespace
        ;
    }

    /**
     * @return NamespaceAssignment[]
     */
    public function getAssignments()
    {
        return $this->assignments;
    }

    /**
     * @internal
     * @param NamespaceAssignment $assignment
     */
    private function addAssignment(NamespaceAssignment $assignment)
    {
        $this->assignments[$assignment->from()->toString()] = $assignment;
    }
}