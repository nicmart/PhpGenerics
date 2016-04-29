<?php
/**
 * This file is part of php-generics
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Source\Evaluation;


use NicMart\Generics\Name\FullName;
use NicMart\Generics\Source\SourceUnit;

/**
 * Class Psr0SourceUnitEvaluation
 * @package NicMart\Generics\Source\Evaluation
 */
class Psr0SourceUnitEvaluation implements SourceUnitEvaluation
{
    /**
     * @var
     */
    private $folder;

    /**
     * Psr0SourceUnitEvaluation constructor.
     * @param string $folder
     */
    public function __construct($folder)
    {
        $this->folder = $folder;
    }

    /**
     * @param SourceUnit $sourceUnit
     * @return void
     */
    public function evaluate(SourceUnit $sourceUnit)
    {
        $name = $sourceUnit->name();
        $filePath = $this->filePath($name);
        $this->createFolders($name);
        file_put_contents(
            $filePath,
            "<?php\n\n" . $sourceUnit->source()
        );

        include $filePath;
    }

    /**
     * @param FullName $name
     * @return string
     */
    private function filePath(FullName $name)
    {
        return sprintf(
            "%s/%s.php",
            $this->folder,
            $name->toString("/")
        );
    }

    /**
     * @param FullName $name
     */
    private function createFolders(FullName $name)
    {
        $path = $this->folder;

        if (!file_exists($this->folder)) {
            mkdir($this->folder);
        }

        $parts = array_slice($name->parts(), 0, -1);

        foreach ($parts as $part) {
            $path .= "/" . $part;
            if (!file_exists($path)) {
                mkdir($path);
            }
        }
    }
}