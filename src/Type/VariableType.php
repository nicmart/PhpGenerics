<?php
/**
 * This file is part of PhpStorm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Type;

use Doctrine\Instantiator\Exception\InvalidArgumentException;
use NicMart\Generics\Name\FullName;

/**
 * Class VariableType
 * @package NicMart\Generics\Type
 */
final class VariableType implements Type
{
    /**
     * @var FullName
     */
    private $name;

    /**
     * VariableType constructor.
     * @param FullName $name
     */
    public function __construct(FullName $name)
    {
        $this->assertValidVariableName($name);
        $this->name = $name;
    }

    /**
     * @return FullName
     */
    public function name()
    {
        return $this->name;
    }

    /**
     * @param FullName $name
     * @return mixed
     */
    private function assertValidVariableName(FullName $name)
    {
        $variableInterface = '\NicMart\Generics\Variable\Variable';
        if (!is_subclass_of($name->toString(), $variableInterface)) {
            throw new InvalidArgumentException(
                "Variable types must be a subclass of $variableInterface"
            );
        }
    }
}