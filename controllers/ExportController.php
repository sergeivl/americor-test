<?php

namespace app\controllers;

use app\models\Export;
use app\services\ExportCheckService;
use app\services\ExportStartService;
use Yii;
use yii\base\Exception;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class ExportController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ]
        ];
    }

    /**
     * @param $exportType
     * @return array
     * @throws Exception
     */
    public function actionStart($exportType)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $params = []; // Дополнительные критерии экспорта
        $exportProcessingService = new ExportStartService();
        $queueId = $exportProcessingService->start($exportType, $params);

        return [
            'status' => 'ok',
            'queueId' => $queueId,
            'checkUrl' => Url::to(['export/check', 'queueId' => $queueId])
        ];
    }


    /**
     * @param string $queueId
     * @return array
     * @throws Exception
     */
    public function actionCheck(string $queueId)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $service = new ExportCheckService();
        if ($service->check($queueId)) {
            return $service->getResult($queueId);
        }

        return [
            'status' => 'processing'
        ];
    }

    /**
     * @param $id
     * @return \yii\console\Response|Response
     * @throws HttpException
     */
    public function actionResult($id)
    {
        $export = Export::findOne($id);
        if (!$export) {
            throw new NotFoundHttpException('File not found', 404);
        }
        return Yii::$app->response->sendFile(
            Yii::getAlias('@app' . '/' . $export->path)
        );
    }
}
