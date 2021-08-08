<?php

namespace app\models;

use Yii;

/**
 * Класс модели для таблицы "files"
 *
 * @property int $id                        ID (первичный ключ)
 * @property string $path                   путь к файлу на диске
 *
 * @property Categories[] $categories
 * @property PhotosWorks[] $photosWorks
 * @property TasksAttachments[] $tasksAttachments
 * @property Users[] $users
 */
class Files extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'files';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['path'], 'required'],
            [['path'], 'string', 'max' => 255],
            [['path'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID файла',
            'path' => 'путь к файлу',
        ];
    }

    /**
     * Gets query for [[Categories]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCategories()
    {
        return $this->hasMany(Categories::class, ['icon_id' => 'id']);
    }

    /**
     * Gets query for [[PhotosWorks]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPhotosWorks()
    {
        return $this->hasMany(PhotosWorks::class, ['photo_id' => 'id']);
    }

    /**
     * Gets query for [[TasksAttachments]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTasksAttachments()
    {
        return $this->hasMany(TasksAttachments::class, ['attachment_id' => 'id']);
    }

    /**
     * Gets query for [[Users]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(Users::class, ['avatar_id' => 'id']);
    }
}
