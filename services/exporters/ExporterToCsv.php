<?php

namespace app\services\exporters;

use app\models\Export;
use app\models\History;
use app\services\exporters\builders\CsvFieldsBuilder;
use Yii;
use yii\db\ActiveQuery;

class ExporterToCsv implements ExporterInterface
{
    private CsvFieldsBuilder $csvFieldsBuilder;

    public function __construct(CsvFieldsBuilder $csvFieldsBuilder)
    {
        $this->csvFieldsBuilder = $csvFieldsBuilder;
    }

    /**
     * @param Export $export
     * @return string
     */
    public function export(Export $export): string
    {
        $query = $this->buildQuery(
            json_decode($export->params)
        );


        $filePath = $this->buildFilePath();
        $filePointer = fopen($filePath, 'w');

        fputcsv(
            $filePointer,
            $this->csvFieldsBuilder->getHeadFields(),
            ';'
        );

        foreach ($query->asArray()->batch() as $items) {
            foreach ($items as $item) {
                fputcsv(
                    $filePointer,
                    $this->csvFieldsBuilder->buildCswRowDate($item),
                    ';'
                );
            }
        }

        $export->path = $this->buildRelativeFilePath($filePath);
        $export->save();

        return $filePath;
    }

    private function buildQuery(array $params): ActiveQuery
    {
        $query = History::find()
            ->addSelect('history.*')
            ->orderBy(['ins_ts' => SORT_DESC, 'id' => SORT_DESC])
            ->with([
                    'customer',
                    'user',
                    'sms',
                    'task',
                    'call',
                    'fax',
                ]
            );

        if ($params) {
            // TODO при наличии параметров дополняем запрос критериями из $params
        }

        return $query;
    }

    private function buildFilePath(): string
    {
        $exportDir = Yii::getAlias('@app') . '/data/export';
        return $exportDir . '/' . uniqid() . '.csv';
    }

    private function buildRelativeFilePath(string $path): string
    {
        return str_replace(
            Yii::getAlias('@app') . '/',
            '',
            $path
        );
    }

}