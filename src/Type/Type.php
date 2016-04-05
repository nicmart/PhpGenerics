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
     * @var Path
     */
    private $path;

    /**
     * @param string $string
     * @return Type
     */
    public static function fromString($string)
    {
        return new self(Path::fromString($string));
    }

    /**
     * Type constructor.
     * @param Path $path
     */
    public function __construct(Path $path)
    {
        $this->path = $path;
    }

    /**
     * @return mixed
     */
    public function toString()
    {
        return $this->path->toString("\\");
    }

    /**
     * @return SimpleName
     */
    public function name()
    {
        return new SimpleName($this->path->name());
    }

    /**
     * @return Path
     */
    public function path()
    {
        return $this->path;
    }

    /**
     * @return Namespace_
     */
    public function namespace_()
    {
        return new Namespace_($this->path()->up());
    }

    /**
     * @param Namespace_ $namespace
     * @return RelativeType
     */
    public function toRelativeTypeForNamespace(Namespace_ $namespace)
    {
        return new RelativeType(
            $this->path->from($namespace->path())
        );
    }

    /**
     * @param NamespaceContext $context
     * @return RelativeType
     */
    public function toRelativeType(NamespaceContext $context)
    {
        $useRelativePath = $this->path;

        for (
            $prefix = $this->path;
            !$prefix->isRoot();
            $prefix = $prefix->up()
        ) {
            if ($context->hasUseByPath($prefix)) {
                $alias = $context->getUseByPath($prefix)->alias()->toPath();
                $useRelativePath = $alias->append(
                    $this->path()->from($prefix)
                );
                break;
            }
        }

        $nsRelativePath = $this->path->from($context->getNamespace()->path());

        return $nsRelativePath->length() <= $useRelativePath->length()
            ? new RelativeType($nsRelativePath)
            : new RelativeType($useRelativePath)
        ;
    }
}