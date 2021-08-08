<?php

namespace app\models;

use Yii;

/**
 * Класс модели для таблицы "tasks"
 *
 * @property int $id                        ID (первичный ключ)
 * @property string $dt_add                 дата/время добавления задания
 * @property string $status                 статус задания
 * @property string $job_essence            краткое описание задания
 * @property string $job_details            подробности задания
 * @property string|null $expire            срок исполнения
 * @property int $budget                    бюджет (цена)
 * @property int|null $location_id          ID локации (адрес исполнения, если задание требует присутствия)
 * @property int $category_id               ID категории
 * @property int $customer_id               ID заказчика
 * @property int|null $executor_id          ID исполнителя
 *
 * @property Messages[] $messages
 * @property Responses[] $responses
 * @property Reviews[] $reviews
 * @property Locations $location
 * @property Categories $category
 * @property Users $customer
 * @property Users $executor
 * @property TasksAttachments[] $tasksAttachments
 */
class Tasks extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tasks';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['dt_add', 'expire'], 'safe'],
            [['status'], 'string'],
            [['job_essence', 'job_details', 'category_id', 'customer_id'], 'required'],
            [['budget', 'location_id', 'category_id', 'customer_id', 'executor_id'], 'integer'],
            [['job_essence'], 'string', 'max' => 255],
            [['job_details'], 'string', 'max' => 1024],
            [['location_id'], 'exist', 'skipOnError' => true, 'targetClass' => Locations::class, 'targetAttribute' => ['location_id' => 'id']],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Categories::class, 'targetAttribute' => ['category_id' => 'id']],
            [['customer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['customer_id' => 'id']],
            [['executor_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['executor_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID задания',
            'dt_add' => 'дата/время добавления задания',
            'status' => 'статус задания',
            'job_essence' => 'краткое описание задания',
            'job_details' => 'подробности задания',
            'expire' => 'срок исполнения',
            'budget' => 'бюджет',
            'location_id' => 'ID локации',
            'category_id' => 'ID категории',
            'customer_id' => 'ID заказчика',
            'executor_id' => 'ID исполнителя',
        ];
    }

    /**
     * Gets query for [[Messages]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMessages()
    {
        return $this->hasMany(Messages::class, ['task_id' => 'id']);
    }

    /**
     * Gets query for [[Responses]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getResponses()
    {
        return $this->hasMany(Responses::class, ['task_id' => 'id']);
    }

    /**
     * Gets query for [[Reviews]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReviews()
    {
        return $this->hasMany(Reviews::class, ['task_id' => 'id']);
    }

    /**
     * Gets query for [[Location]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLocation()
    {
        return $this->hasOne(Locations::class, ['id' => 'location_id']);
    }

    /**
     * Gets query for [[Category]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Categories::class, ['id' => 'category_id']);
    }

    /**
     * Gets query for [[Customer]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne(Users::class, ['id' => 'customer_id']);
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

    /**
     * Gets query for [[TasksAttachments]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTasksAttachments()
    {
        return $this->hasMany(TasksAttachments::class, ['task_id' => 'id']);
    }
}
