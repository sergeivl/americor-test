<?php

use yii\db\Migration;

/**
 * Class m220924_135058_Export
 */
class m220924_135058_Export extends Migration
{
    /**
     * @return void
     * @throws \yii\base\Exception
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%export}}', [
            'id' => $this->primaryKey(),
            'type' => $this->string()->null(),
            'params' => $this->json(),
            'queue_id' => $this->string()->null(),
            'path' => $this->string()->null(),
            'status' => $this->smallInteger()->notNull()->defaultValue(1),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->null(),
        ], $tableOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%export}}');
    }
}
