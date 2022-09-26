<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\queue\Queue;

/**
 * This is the model class for table "{{%export}}".
 *
 * @property integer $id
 * @property string $type
 * @property string $queue_id
 * @property string $params
 * @property string $path
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 */
class Export extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%export}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'created_at', 'status'], 'required'],
            [[
                'status',
                'created_at',
                'updated_at',
            ], 'integer'],
            [[
                'type',
                'queue_id',
                'path',
                'params',
            ], 'string', 'max' => 255],

            ['status', 'default', 'value' => Queue::STATUS_WAITING],
            ['status', 'in', 'range' => [Queue::STATUS_WAITING, Queue::STATUS_RESERVED, Queue::STATUS_DONE]],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'queue_id' => Yii::t('app', 'Queue ID'),
            'params' => Yii::t('app', 'Params'),
            'path' => Yii::t('app', 'Path'),
            'status' => Yii::t('app', 'Status'),
            'created_at' => Yii::t('app', 'Created at'),
            'updated_at' => Yii::t('app', 'Updated at')
        ];
    }
}
