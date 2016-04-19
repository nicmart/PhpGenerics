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
use NicMart\Generics\Name\Transformer\SimpleNameTransformer;
use PhpParser\Node\Name;

/**
 * Class SimpleNameAssignmentContext
 * @package NicMart\Generics\Name\Assignment
 */
final class SimpleNameAssignmentContext implements SimpleNameTransformer
{
    /**
     * @var SimpleNameAssignment[]
     */
    private $assignments = array();

    /**
     * @param array $assignments
     * @return SimpleNameAssignmentContext
     */
    public static function fromStrings(array $assignments)
    {
        $parsedAssignments = array();

        foreach ($assignments as $from => $to) {
            $parsedAssignments[] = new SimpleNameAssignment(
                new SimpleName($from),
                new SimpleName($to)
            );
        }

        return new self($parsedAssignments);
    }

    /**
     * TypeAssignmentContext constructor.
     * @param SimpleNameAssignment[] $assignments
     */
    public function __construct(array $assignments)
    {
        foreach ($assignments as $assignment) {
            $this->addAssignment($assignment);
        }
    }

    /**
     * @param SimpleNameAssignment $assignment
     * @return SimpleNameAssignmentContext
     */
    public function withAssignment(SimpleNameAssignment $assignment)
    {
        $new = clone $this;

        $new->addAssignment($assignment);

        return $new;
    }

    /**
     * @param SimpleName $name
     * @return bool
     */
    public function hasAssignmentFrom(SimpleName $name)
    {
        return isset($this->assignments[$name->toString()]);
    }

    /**
     * @param SimpleName $name
     * @return SimpleName
     */
    public function transform(SimpleName $name)
    {
        return $this->hasAssignmentFrom($name)
            ? $this->assignments[$name->toString()]->to()
            : $name
        ;
    }

    /**
     * @return SimpleNameAssignment[]
     */
    public function getAssignments()
    {
        return $this->assignments;
    }

    /**
     * @internal
     * @param SimpleNameAssignment $assignment
     */
    private function addAssignment(SimpleNameAssignment $assignment)
    {
        $this->assignments[$assignment->from()->toString()] = $assignment;
    }
}