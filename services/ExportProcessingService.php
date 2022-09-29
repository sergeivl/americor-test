<?php

namespace app\services;

use app\models\Export;
use app\services\exporters\ExporterInterface;
use app\services\exporters\ExporterToCsv;
use Yii;
use yii\base\Exception;
use yii\base\InvalidConfigException;
use yii\di\NotInstantiableException;

class ExportProcessingService
{
    const EXPORTERS = [
        'Csv' => ExporterToCsv::class
        // ... другие виды экспорта
    ];

    /**
     * @param Export $export
     * @return string filepath
     * @throws InvalidConfigException
     * @throws NotInstantiableException
     * @throws Exception
     */
    public function export(Export $export): string
    {
        if (!$this->checkType($export->type)) {
            throw new Exception('Incorrect type', 403);
        }

        Yii::$container->set(ExporterInterface::class, self::EXPORTERS[$export->type]);
        $exporter = Yii::$container->get(ExporterInterface::class);
        return $exporter->export($export);
    }

    public static function checkType(string $type): bool
    {
        return in_array($type, array_keys(self::EXPORTERS), true);
    }

}
