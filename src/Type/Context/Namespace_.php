<?php
/**
 * This file is part of php-generics
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Type\Context;

/**
 * Class Namespace_
 * @package NicMart\Generics\Type\Namespace_
 */
final class Namespace_
{
    /**
     * @var string
     */
    private $namespace;

    /**
     * @param array $parts
     * @return Namespace_
     */
    public static function fromParts(array $parts)
    {
        return new self(implode("\\", $parts));
    }

    /**
     * Namespace_ constructor.
     * @param $namespace
     */
    public function __construct($namespace)
    {
        $this->namespace = ltrim((string) $namespace, "\\");
    }

    /**
     * @return string
     */
    public function name()
    {
        return $this->namespace;
    }

    /**
     * @return array
     */
    public function parts()
    {
        return explode("\\", $this->namespace);
    }

    /**
     * @param Namespace_ $namespace
     * @return Namespace_
     */
    public function commonAncestor(Namespace_ $namespace)
    {
        $parts1 = $this->parts();
        $parts2 = $namespace->parts();
        $min = max(count($parts1), count($parts2));
        $commonAncestorParts = array();

        for ($i = 0; $i < $min; $i++) {
            if ($parts1[$i] != $parts2[$i]) {
                break;
            }
            $commonAncestorParts[] = $parts1[$i];
        }

        return Namespace_::fromParts($commonAncestorParts);
    }
}