<?php
/**
 * This file is part of PhpStorm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Name\Generic\Parser;


use NicMart\Generics\Name\FullName;
use NicMart\Generics\Name\Generic\GenericNameApplication;
use NicMart\Generics\Name\RelativeName;

/**
 * Class AngleQuotedGenericTypeNameParser
 * @package NicMart\Generics\Name\Generic\Parser
 */
class AngleQuotedGenericTypeNameParser implements GenericTypeNameParser
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
     * @return GenericNameApplication
     */
    public function parse(FullName $name)
    {
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
                    $typeVars[] = RelativeName::fromString($currTypeVar);
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

        return new GenericNameApplication(
            FullName::fromString($mainName),
            $typeVars
        );
    }

    /**
     * @param GenericNameApplication $application
     * @return mixed
     */
    public function serialize(GenericNameApplication $application)
    {
        $paramStrings = array_map(
            function (FullName $param) { return $param->last()->toString(); },
            $application->arguments()
        );

        // A bit hackish
        return FullName::fromString(sprintf(
            "%s«%s»",
            $application->main()->toString(),
            implode("·", $paramStrings)
        ));
    }
}