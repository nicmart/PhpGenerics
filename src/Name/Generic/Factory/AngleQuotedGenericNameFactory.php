<?php
/**
 * This file is part of php-generics
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Name\Generic\Factory;

use NicMart\Generics\Name\FullName;
use NicMart\Generics\Name\Generic\AngleQuotedGenericNameInterface;
use NicMart\Generics\Name\Generic\GenericName;
use NicMart\Generics\Name\RelativeName;
use NicMart\Generics\Name\Transformer\NameQualifier;

/**
 * Class AngleQuotedGenericNameFactory
 * @package NicMart\Generics\Name\Generic\Factory
 */
class AngleQuotedGenericNameFactory implements GenericNameFactory
{
    const CHAR_CODE = 194;

    /**
     * @param FullName $name
     * @return bool
     */
    public function isGeneric(FullName $name)
    {
        return strpos($name->toString(), "«") !== false;
    }

    /**
     * @param FullName $name
     * @param NameQualifier $qualifier
     *
     * @return GenericName
     */
    public function toGeneric(
        FullName $name,
        NameQualifier $qualifier
    ) {
        $name = $name->toString();
        $nameLength = strlen($name);

        $typeVars = array();
        $currTypeVar = "";
        $mainName = "";

        $level = 0;

        for ($i = 0; $i < $nameLength; $i++) {
            $char = $name[$i];
            if (ord($char) == self::CHAR_CODE) {
                $char .= $name[++$i];
            }

            if ($char == "»" || $char == "·") {
                --$level;
            }

            if ($level == 0) {
                if ($char == "«") {

                } elseif ($char == "·" || $char == "»") {
                    $typeVars[] = $qualifier->qualify(
                        RelativeName::fromString($currTypeVar)
                    );
                    $currTypeVar = "";
                } else {
                    $mainName .= $char;
                }
            } else {
                $currTypeVar .= $char;
            }

            if ($char == "«"  || $char == "·") {
                ++$level;
            }
        }

        return new GenericName(
            FullName::fromString($mainName),
            $typeVars
        );
    }

    /**
     * @param GenericName $genericName
     * @return FullName
     */
    public function fromGeneric(GenericName $genericName)
    {
        $paramStrings = array_map(
            function (FullName $param) { return $param->last()->toString(); },
            $genericName->parameters()
        );

        return FullName::fromString(sprintf(
            "%s«%s»",
            $genericName->main()->toString(),
            implode("·", $paramStrings)
        ));
    }
}