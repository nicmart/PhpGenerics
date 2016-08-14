<?php
/**
 * This file is part of PhpStorm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Type\Parser;

use NicMart\Generics\Map\GenericTypeApplication;
use NicMart\Generics\Name\FullName;
use NicMart\Generics\Name\Generic\GenericNameApplication;
use NicMart\Generics\Name\Generic\Parser\GenericTypeNameParser;
use NicMart\Generics\Name\Name;
use NicMart\Generics\Name\Transformer\NameQualifier;
use NicMart\Generics\Type\GenericType;
use NicMart\Generics\Type\ParametrizedType;
use NicMart\Generics\Type\PrimitiveType;
use NicMart\Generics\Type\Serializer\TypeSerializer;
use NicMart\Generics\Type\SimpleReferenceType;
use NicMart\Generics\Type\Transformer\ParametricTypeTransformer;
use NicMart\Generics\Type\Type;
use NicMart\Generics\Type\VariableType;

/**
 * Class GenericTypeParser
 * @package NicMart\Generics\Type\Parser
 */
class GenericTypeParserAndSerializer implements TypeParser, TypeSerializer
{
    /**
     * @var GenericTypeNameParser
     */
    private $genericTypeNameParser;

    /**
     * GenericTypeParser constructor.
     * @param GenericTypeNameParser $genericTypeNameParser
     */
    public function __construct(GenericTypeNameParser $genericTypeNameParser)
    {
        $this->genericTypeNameParser = $genericTypeNameParser;
    }

    /**
     * @param Name $name
     * @param NameQualifier $nameQualifier
     * @return Type|void
     */
    public function parse(Name $name, NameQualifier $nameQualifier)
    {
        $fullName = $nameQualifier->qualify($name);

        if (PrimitiveType::isPrimitive($fullName)) {
            return new PrimitiveType($fullName);
        }

        if (VariableType::isVariable($fullName)) {
            return new VariableType($fullName);
        }


        var_dump(sprintf(
            "Parsing type %s (%s). Name Class: %s",
            $fullName->toString(),
            $name->toString(),
            get_class($name)
        ));

        if ($this->genericTypeNameParser->isGeneric($fullName)) {
            return $this->parseGenericName($fullName, $nameQualifier);
        }

        return new SimpleReferenceType($fullName);
    }

    /**
     * @param Type $type
     * @return FullName
     */
    public function serialize(Type $type)
    {
        if ($type instanceof GenericType || $type instanceof ParametrizedType) {
            return $this->serializeGenericType($type);
        }

        return $type->name();
    }


    /**
     * @param FullName $fullName
     * @param NameQualifier $qualifier
     * @return GenericType|ParametrizedType
     */
    private function parseGenericName(FullName $fullName, NameQualifier $qualifier)
    {
        $nameAplication = $this->genericTypeNameParser->parse($fullName);

        $typeArguments = array();
        $isGeneric = true;

        foreach ($nameAplication->arguments() as $name) {
            $typeArguments[] = $type = $this->parse($name, $qualifier);
            $isGeneric = $isGeneric && $type instanceof VariableType;
        }

        if ($isGeneric) {
            return new GenericType(
                $nameAplication->main(),
                $typeArguments
            );
        }

        return new ParametrizedType(
            $nameAplication->main(),
            $typeArguments
        );
    }

    /**
     * @param GenericType|ParametrizedType|Type $type
     * @return FullName
     */
    private function serializeGenericType(Type $type)
    {
        $main = $type->name();

        $typeArguments = $type instanceof GenericType
            ? $type->parameters()
            : $type->arguments()
        ;

        $nameArguments = array();

        foreach ($typeArguments as $type) {
            $nameArguments[] = $this->serialize($type);
        }

        return $this->genericTypeNameParser->serialize(
            new GenericNameApplication(
                $main,
                $nameArguments
            )
        );
    }
}