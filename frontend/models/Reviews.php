<?php

namespace app\models;

use Yii;

/**
 * Класс модели для таблицы "reviews"
 *
 * @property int $id                        ID (первичный ключ)
 * @property string $dt_add                 дата/время добавления отзыва
 * @property int $eval                      оценка
 * @property string $review_text            текст отзыва
 * @property int $task_id                   ID задания, о котором пишется отзыв
 * @property int $executor_id               ID исполнителя
 *
 * @property Tasks $task
 * @property Users $executor
 */
class Reviews extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'reviews';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['dt_add'], 'safe'],
            [['eval', 'task_id', 'executor_id'], 'integer'],
            [['review_text', 'task_id', 'executor_id'], 'required'],
            [['review_text'], 'string', 'max' => 1024],
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
            'id' => 'ID отзыва',
            'dt_add' => 'дата/время добавления отзыва',
            'eval' => 'оценка',
            'review_text' => 'текст отзыва',
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
