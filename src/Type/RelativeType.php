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

use NicMart\Generics\Type\Context\NamespaceContext;

/**
 * Class RelativeType
 * @package NicMart\Generics\Type
 */
final class RelativeType
{
    /**
     * @var string[]
     */
    private $nativeTypes = array(
        "string",
        "int",
        "callable",
        "array",
        "resource",
        "float",
        "double",
        "bool",
        "void",

        "static",
        "self",
        "parent"
    );
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
     * RelativeType constructor.
     * @param Path $path
     */
    public function __construct(Path $path)
    {
        $this->path = $path;
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
     * @param NamespaceContext $context
     * @return Type
     * @throws \UnderflowException
     */
    public function toFullType(NamespaceContext $context)
    {
        $path = $this->path;
        if ($path->isRoot()) {
            return new Type($path);
        }

        if (in_array($path->name(), $this->nativeTypes)) {
            return new Type($path);
        }

        $first = new SimpleName($path->first());

        if ($context->hasUse($first)) {
            $use = $context->getUse($first);
            return new Type($use->path()->append(
                $path->from(new Path(array($path->first())))
            ));
        }

        return new Type(
            $context->getNamespace()->path()->append($path)
        );
    }
}