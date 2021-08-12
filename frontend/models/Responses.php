<?php

namespace app\models;

use Yii;

/**
 * Класс модели для таблицы "responses"
 *
 * @property int $id                        ID (первичный ключ)
 * @property string $dt_add                 дата/время добавления отклика
 * @property int $budget                    предложенная цена
 * @property string $response_text          текст отклика
 * @property int $task_id                   ID задания
 * @property int $executor_id               ID исполнителя
 *
 * @property Tasks $task
 * @property Users $executor
 */
class Responses extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'responses';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['dt_add'], 'safe'],
            [['budget', 'task_id', 'executor_id'], 'integer'],
            [['response_text', 'task_id', 'executor_id'], 'required'],
            [['response_text'], 'string', 'max' => 1024],
            [['task_id'], 'exist', 'skipOnError' => true, 'targetClass' => Tasks::class, 'targetAttribute' => ['task_id' => 'id']],
            [['executor_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['executor_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID отклика',
            'dt_add' => 'дата/время добавления отклика',
            'budget' => 'предложенная цена',
            'response_text' => 'текст отклика',
            'task_id' => 'ID задания',
            'executor_id' => 'ID исполнителя',
        ];
    }

    /**
     * Gets query for [[Task]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTask()
    {
        return $this->hasOne(Tasks::class, ['id' => 'task_id']);
    }

    /**
     * Gets query for [[Executor]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getExecutor()
    {
        return $this->hasOne(Users::class, ['id' => 'executor_id']);
    }
}
