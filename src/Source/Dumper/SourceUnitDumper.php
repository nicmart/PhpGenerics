<?php
/**
 * @author Nicolò Martini - <nicolo@martini.io>
 *
 * Created on 11/05/2016, 13:35
 * Copyright (C) DXI Ltd
 */

namespace NicMart\Generics\Source\Dumper;


use NicMart\Generics\Source\SourceUnit;

interface SourceUnitDumper
{
    /**
     * @param SourceUnit $sourceUnit
     * @return DumpResult
     */
    public function dump(SourceUnit $sourceUnit);
}