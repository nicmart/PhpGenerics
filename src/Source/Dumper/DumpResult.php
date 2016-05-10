<?php
/**
 * @author Nicolò Martini - <nicolo@martini.io>
 *
 * Created on 11/05/2016, 13:36
 * Copyright (C) DXI Ltd
 */

namespace NicMart\Generics\Source\Dumper;


use NicMart\Generics\Source\SourceUnit;

/**
 * Class DumpResult
 * @package NicMart\Generics\Source\Dumper
 */
final class DumpResult
{
    /**
     * @var string
     */
    private $filename;

    /**
     * @var SourceUnit
     */
    private $sourceUnit;

    /**
     * DumpResult constructor.
     * @param $filename
     * @param SourceUnit $sourceUnit
     */
    public function __construct($filename, SourceUnit $sourceUnit)
    {
        $this->filename = $filename;
        $this->sourceUnit = $sourceUnit;
    }

    /**
     * @return SourceUnit
     */
    public function sourceUnit()
    {
        return $this->sourceUnit;
    }

    /**
     * @return string
     */
    public function filename()
    {
        return $this->filename;
    }
}