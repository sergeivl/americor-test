<?php

namespace app\jobs;

use app\models\Export;
use app\services\ExportProcessingService;
use Yii;
use yii\base\BaseObject;
use yii\base\Exception;
use yii\base\InvalidConfigException;
use yii\di\NotInstantiableException;
use yii\queue\JobInterface;
use yii\queue\Queue;

class ExportJob extends BaseObject implements JobInterface
{
    public string $exportId;

    /**
     * @return void
     * @throws InvalidConfigException
     * @throws NotInstantiableException|Exception
     */
    public function execute($queue)
    {
        $export = Export::findOne($this->exportId);

        if (!$export) {
            throw new Exception('Not found Export record', 404);
        }

        $export->status = Queue::STATUS_RESERVED;
        $export->updated_at = time();
        $export->save();

        $exporter = Yii::$container->get(ExportProcessingService::class);
        $exporter->export($export);

        $export->status = Queue::STATUS_DONE;
        $export->updated_at = time();
        $export->save();
    }
}
