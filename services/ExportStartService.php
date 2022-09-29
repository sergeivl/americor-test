<?php

namespace app\services;

use app\jobs\ExportJob;
use app\models\Export;
use Yii;
use yii\base\Exception;
use yii\queue\Queue;

class ExportStartService
{
    /**
     * Create export job
     * @param string $type
     * @param array $params
     * @return string Queue id
     * @throws Exception
     */
    public function start(string $type, array $params): string
    {
        if (!ExportProcessingService::checkType($type)) {
            throw new Exception('Incorrect type', 400);
        }

        $export = $this->createExportRecord($type, $params);

        /** @var Queue $queue */
        $queue = Yii::$app->queue;
        $export->queue_id = $queue->push(new ExportJob([
            'exportId' => $export->id
        ]));

        if ($export->queue_id === null) {
            throw new Exception('Queue id is empty', 500);
        }

        $export->queue_id = (string)$export->queue_id;
        $this->saveExport($export);

        return $export->queue_id;
    }

    /**
     * @param string $type
     * @param array $params
     * @return Export
     * @throws Exception
     */
    private function createExportRecord(string $type, array $params): Export
    {
        $export = new Export();
        $export->params = json_encode($params);
        $export->type = $type;
        $export->status = Queue::STATUS_WAITING;
        $export->created_at = time();

        return $this->saveExport($export);
    }

    /**
     * @param Export $export
     * @return Export
     * @throws Exception
     */
    private function saveExport(Export $export): Export
    {
        if (!$export->save()) {
            throw new Exception('Failed to create Export record', 500);
        }

        return $export;
    }
}
