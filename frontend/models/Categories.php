<?php

namespace app\models;

use Yii;

/**
 * Класс модели для таблицы "categories"
 *
 * @property int $id                        ID (первичный ключ)
 * @property string $category_name          название категории
 * @property int $icon_id                   ID файла с иконкой
 *
 * @property Files $icon
 * @property ExecutorSpecialties[] $executorSpecialties
 * @property Tasks[] $tasks
 */
class Categories extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'categories';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['category_name', 'icon_id'], 'required'],
            [['icon_id'], 'integer'],
            [['category_name'], 'string', 'max' => 128],
            [['category_name'], 'unique'],
            [['icon_id'], 'exist', 'skipOnError' => true, 'targetClass' => Files::class, 'targetAttribute' => ['icon_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID категории',
            'category_name' => 'название категории',
            'icon_id' => 'ID файла с иконкой',
        ];
    }

    /**
     * Gets query for [[Icon]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getIcon()
    {
        return $this->hasOne(Files::class, ['id' => 'icon_id']);
    }

    /**
     * Gets query for [[ExecutorSpecialties]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getExecutorSpecialties()
    {
        return $this->hasMany(ExecutorSpecialties::class, ['category_id' => 'id']);
    }

    /**
     * Gets query for [[Tasks]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTasks()
    {
        return $this->hasMany(Tasks::class, ['category_id' => 'id']);
    }
}
