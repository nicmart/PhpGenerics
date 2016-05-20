<?php
/**
 * This file is part of php-generics
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Name\Transformer;


use Doctrine\Instantiator\Exception\UnexpectedValueException;
use NicMart\Generics\Name\Context\NamespaceContext;
use NicMart\Generics\Name\FullName;
use NicMart\Generics\Name\Generic\Factory\GenericNameFactory;
use NicMart\Generics\Name\Name;

/**
 * Class GenericNameTransformer
 * @package NicMart\Generics\Name\Transformer
 */
class GenericNameTransformer implements NameTransformer
{
    /**
     * @var FullNameTransformer
     */
    private $innerNameTransformer;

    /**
     * @var GenericNameFactory
     */
    private $genericNameFactory;

    /**
     * Accepts a factory callable that returns the NameTransformer
     *
     * The factory will receive THIS instance as argument, making possible
     * self-recursive definitions of the transformation, maintaining
     * immutability.
     *
     * @param callable $factory
     * @param GenericNameFactory $genericNameFactory
     * @return GenericNameTransformer
     */
    public static function fromNameTransformerFactory(
        $factory,
        GenericNameFactory $genericNameFactory
    ) {
        if (!is_callable($factory)) {
            throw new UnexpectedValueException(
                "NameTransformer factory must be a valid php callable"
            );
        }

        $dummyTransformer = new ChainNameTransformer(array());

        $transformer = new self($dummyTransformer, $genericNameFactory);

        $transformer->innerNameTransformer = $factory($transformer);

        return $transformer;
    }

    /**
     * GenericNameTransformer constructor.
     * @param NameTransformer $innerNameTransformer
     * @param GenericNameFactory $genericNameFactory
     */
    public function __construct(
        NameTransformer $innerNameTransformer,
        GenericNameFactory $genericNameFactory
    ) {
        $this->innerNameTransformer = $innerNameTransformer;
        $this->genericNameFactory = $genericNameFactory;
    }

    /**
     * @param Name $name
     * @param NamespaceContext $namespaceContext
     * @return FullName|Name
     */
    public function transformName(
        Name $name,
        NamespaceContext $namespaceContext
    ) {
        $fullName = $name instanceof FullName
            ? $name
            : $namespaceContext->qualify($name)
        ;

        if (!$this->genericNameFactory->isGeneric($fullName)) {
            return $name;
        }

        $genericName = $this->genericNameFactory->toGeneric(
            $fullName,
            $namespaceContext
        );

        $typeVars = $genericName->parameters();

        $typeValues = array();

        foreach ($typeVars as $typeVar) {
            $typeValues[] = $namespaceContext->qualify($this->innerNameTransformer->transformName(
                $typeVar,
                $namespaceContext
            ));
        }

        return $namespaceContext->simplify($this->genericNameFactory->fromGeneric(
            $genericName->apply($typeValues)
        ));
    }
}