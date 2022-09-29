<?php

namespace app\services;

use app\models\Export;
use Yii;
use yii\base\Exception;
use yii\helpers\Url;
use yii\queue\Queue;

class ExportCheckService
{
    /**
     * @param string $queueId
     * @return bool
     */
    public function check(string $queueId): bool
    {
        /** @var Queue $queue */
        $queue = Yii::$app->queue;
        return $queue->isDone($queueId);
    }

    /**
     * @param string $queueId
     * @return array
     * @throws Exception
     */
    public function getResult(string $queueId): array
    {
        $export = Export::find()->where(['queue_id' => $queueId])->one();

        if (!$export) {
            throw new Exception('Export action not found', 404);
        }

        return [
            'status' => 'ok',
            'path' => Url::to(['export/result', 'id' => $export->id])
        ];
    }
}
