<?php
/**
 * This file is part of php-generics
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Type;

use NicMart\Generics\Type\Context\Namespace_;
use NicMart\Generics\Type\Context\NamespaceContext;
use NicMart\Generics\Type\Context\Use_;

/**
 * Class Type
 * @package NicMart\Generics\Type
 */
final class Type
{
    /**
     * @var string
     */
    private $name;

    /**
     * @param array $parts
     * @return Type
     */
    public static function fromParts(array $parts)
    {
        return new self(implode("\\", $parts));
    }

    /**
     * Type constructor.
     * @param $fullQualifiedName
     */
    public function __construct($fullQualifiedName)
    {
        $this->name = ltrim((string) $fullQualifiedName, "\\");
    }

    /**
     * @return mixed
     */
    public function name()
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function parts()
    {
        return explode("\\", $this->name);
    }

    /**
     * @return Namespace_
     */
    public function namespace_()
    {
        return Namespace_::fromParts(array_slice($this->parts(), 0, -1));
    }

    /**
     * @param Namespace_ $namespace
     * @return RelativeType
     */
    public function toRelativeTypeForNamespace(Namespace_ $namespace)
    {
        $parts = $this->parts();
        $nsParts = $namespace->parts();
        $numParts = min(count($parts), count($nsParts));

        for ($i = 0; $i < $numParts; $i++) {
            if ($parts[$i] != $nsParts[$i]) {
                break;
            }
            unset($parts[$i]);
        }

        return RelativeType::fromParts(array_values($parts));
    }

    /**
     * @param NamespaceContext $context
     * @return RelativeType
     */
    public function toRelativeType(NamespaceContext $context)
    {
        $parts = $this->parts();
        $useRelativeNameParts = $this->parts();

        for ($i = 0, $totParts = count($parts); $i <= $totParts; $i++) {
            $prefix = implode("\\", array_slice($parts, 0, $totParts -$i));
            if ($context->hasUseByName($prefix)) {
                $useRelativeNameParts = array_merge(
                    array($context->getUseByName($prefix)->alias()),
                    array_slice($parts, $totParts - $i)
                );
                break;
            }
        }

        $nsRelatativeParts = $this
            ->toRelativeTypeForNamespace($context->getNamespace())
            ->parts()
        ;

        return count($nsRelatativeParts) <= count($useRelativeNameParts)
            ? RelativeType::fromParts($nsRelatativeParts)
            : RelativeType::fromParts($useRelativeNameParts)
        ;

    }
}