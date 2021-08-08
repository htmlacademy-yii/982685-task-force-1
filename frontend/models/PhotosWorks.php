<?php

namespace app\models;

use Yii;

/**
 * Класс модели для таблицы "photos_works"
 *
 * @property int $id                        ID (первичный ключ)
 * @property int $photo_id                  ID фотографии работы
 * @property int $executor_id               ID исполнителя
 *
 * @property Files $photo
 * @property Users $executor
 */
class PhotosWorks extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'photos_works';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['photo_id', 'executor_id'], 'required'],
            [['photo_id', 'executor_id'], 'integer'],
            [['photo_id'], 'exist', 'skipOnError' => true, 'targetClass' => Files::class, 'targetAttribute' => ['photo_id' => 'id']],
            [['executor_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['executor_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'photo_id' => 'ID фотографии работы',
            'executor_id' => 'ID исполнителя',
        ];
    }

    /**
     * Gets query for [[Photo]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPhoto()
    {
        return $this->hasOne(Files::class, ['id' => 'photo_id']);
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
