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


use NicMart\Generics\Name\Context\NamespaceContext;
use NicMart\Generics\Name\Name;
use UnexpectedValueException;

/**
 * Class ChainPhpParserNameTransformer
 * @package NicMart\Generics\AST\Name
 */
class ChainNameTransformer implements NameTransformer
{
    /**
     * @var NameTransformer[]
     */
    private $nameTransformers = array();

    /**
     * Accepts a factory callable that returns the NameTransformer array
     *
     * The factory will receive THIS instance as argument, making possible
     * self-recursive definitions of the transformation, maintaining
     * immutability.
     *
     * Please not that immutability is compromised during the execution
     * of the factory.
     *
     * @param callable $factory
     * @return ChainNameTransformer
     * @throws UnexpectedValueException
     */
    public static function fromNameTransformerFactory($factory) {
        if (!is_callable($factory)) {
            throw new UnexpectedValueException(
                "NameTransformer factory must be a valid php callable"
            );
        }

        $chain = new self(array());

        foreach ($factory($chain) as $transformer) {
            $chain->addNameTransformer($transformer);
        }

        return $chain;
    }

    /**
     * ChainPhpParserNameTransformer constructor.
     *
     * @param NameTransformer[] $nameTransformers
     */
    public function __construct(array $nameTransformers)
    {
        foreach ($nameTransformers as $nameTransformer) {
            $this->addNameTransformer($nameTransformer);
        }
    }

    /**
     * @param Name $name
     * @param NamespaceContext $namespaceContext
     *
     * @return Name
     */
    public function transformName(Name $name, NamespaceContext $namespaceContext)
    {
        $transformedName = $name;

        foreach ($this->nameTransformers as $nameTransformer) {
            $transformedName = $nameTransformer->transformName(
                $transformedName,
                $namespaceContext
            );
        }

        return $transformedName;
    }


    /**
     * @param NameTransformer $phpParserNameTransformer
     */
    private function addNameTransformer(
        NameTransformer $phpParserNameTransformer
    ) {
        $this->nameTransformers[] = $phpParserNameTransformer;
    }
}