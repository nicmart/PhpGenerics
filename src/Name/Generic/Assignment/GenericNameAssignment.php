<?php
/**
 * This file is part of PhpStorm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Name\Generic\Assignment;


use Doctrine\Instantiator\Exception\InvalidArgumentException;
use NicMart\Generics\Name\Assignment\NameAssignment;
use NicMart\Generics\Name\Assignment\SimpleNameAssignment;
use NicMart\Generics\Name\Context\NamespaceContext;
use NicMart\Generics\Name\FullName;
use NicMart\Generics\Name\Generic\Factory\GenericNameFactory;
use NicMart\Generics\Name\Generic\GenericName;
use NicMart\Generics\Name\Transformer\NameQualifier;

/**
 * Class GenericNameAssignment
 * @package NicMart\Generics\Name\Generic\Assignment
 */
final class GenericNameAssignment
{
    /**
     * @param FullName $genericName
     * @param array $typeArguments
     * @param NameQualifier $nameQualifier
     * @param GenericNameFactory $genericNameFactory
     * @return GenericNameAssignment
     */
    public static function fromName(
        FullName $genericName,
        array $typeArguments,
        NameQualifier $nameQualifier,
        GenericNameFactory $genericNameFactory
    ) {
        $generic = $genericNameFactory->toGeneric(
            $genericName,
            $nameQualifier
        );
        $appliedGeneric = $generic->apply($typeArguments);
        $parametrizedName = $genericNameFactory->fromGeneric($appliedGeneric);

        return new self(
            $generic,
            new NameAssignment($genericName, $parametrizedName),
            $typeArguments
        );
    }

    /**
     * @var FullName[]
     */
    private $typeArguments = array();

    /**
     * @var NameAssignment
     */
    private $nameAssignment;

    /**
     * @var GenericName
     */
    private $genericName;

    /**
     * GenericNameAssignment constructor.
     * @param GenericName $genericName
     * @param NameAssignment $nameAssignment
     * @param array $typeArguments
     */
    public function __construct(
        GenericName $genericName,
        NameAssignment $nameAssignment,
        array $typeArguments
    ) {
        $this->assertValidArguments($typeArguments);

        foreach ($typeArguments as $argument) {
            $this->addTypeArgument($argument);
        }

        $this->nameAssignment = $nameAssignment;
        $this->genericName = $genericName;
    }

    /**
     * @return mixed
     */
    public function generic()
    {
        return $this->generic();
    }

    /**
     * @return FullName[]
     */
    public function typeArguments()
    {
        return $this->typeArguments;
    }

    /**
     * @return FullName[]
     */
    public function typeParameters()
    {
        return $this->genericName->parameters();
    }

    /**
     * @return NameAssignment
     */
    public function mainAssignment()
    {
        return $this->nameAssignment;
    }

    /**
     * @return SimpleNameAssignment
     */
    public function mainSimpleNameAssignment()
    {
        return new SimpleNameAssignment(
            $this->nameAssignment->from()->last(),
            $this->nameAssignment->to()->last()
        );
    }

    /**
     * @return \NicMart\Generics\Name\Assignment\NameAssignmentContext
     */
    public function typeAssignments()
    {
        return $this->genericName->assignments($this->typeArguments);
    }

    /**
     * @param FullName $argument
     */
    private function addTypeArgument(FullName $argument)
    {
        $this->typeArguments[] = $argument;
    }

    /**
     * @param array $arguments
     */
    private function assertValidArguments(array $arguments)
    {
        if (count($arguments) == $this->genericName->arity()) {
            return;
        }

        throw new InvalidArgumentException(sprintf(
            "Invalid number of arguments provided for generic type %s: expected %d, found %d",
            $this->genericName->main()->toString(),
            $this->genericName->arity(),
            count($arguments)
        ));
    }
}