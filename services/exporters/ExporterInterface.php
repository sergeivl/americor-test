<?php

namespace app\services\exporters;

use app\models\Export;

interface ExporterInterface
{
    public function export(Export $export);
}
