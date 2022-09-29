<?php

use app\models\search\HistorySearch;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\ListView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $dataProvider ActiveDataProvider */
/* @var $model HistorySearch */
/* @var $linkExport string */

$this->registerJsFile(
    '@web/js/export.js',
    ['position' => View::POS_END]
);
?>

<?php Pjax::begin(['id' => 'grid-pjax', 'formSelector' => false]); ?>

<div class="panel panel-primary panel-small m-b-0">
    <div class="panel-body panel-body-selected">

        <div class="pull-sm-right">
            <?php if (!empty($linkExport)): ?>
                <?= Html::a(
                    Yii::t('app', 'CSV Optimized'),
                    Url::to(['export/start', 'exportType' => 'Csv']),
                    [
                        'class' => 'btn btn-primary import-csv-button',
                        'data-pjax' => 0,
                    ]
                ); ?>
            <?php endif; ?>

            <img src="/img/loading.gif" alt="Идет загрузка" class="loading-animation hide">
            <span class="alert alert-danger export-error hide"></span>
        </div>

    </div>
</div>

<?php echo ListView::widget([
    'dataProvider' => $dataProvider,
    'itemView' => '_item',
    'options' => [
        'tag' => 'ul',
        'class' => 'list-group'
    ],
    'itemOptions' => [
        'tag' => 'li',
        'class' => 'list-group-item'
    ],
    'emptyTextOptions' => ['class' => 'empty p-20'],
    'layout' => '{items}{pager}',
]); ?>

<?php Pjax::end(); ?>
