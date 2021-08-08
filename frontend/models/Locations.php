<?php

namespace app\models;

use Yii;

/**
 * Класс модели для таблицы "locations"
 *
 * @property int $id                        ID (первичный ключ)
 * @property int $city_id                   ID города
 * @property float $lat                     широта локации, введенной пользователем
 * @property float $long                    долгота локации, введенной пользователем
 *
 * @property Cities $city
 * @property Tasks[] $tasks
 */
class Locations extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'locations';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['city_id', 'lat', 'long'], 'required'],
            [['city_id'], 'integer'],
            [['lat', 'long'], 'number'],
            [['city_id'], 'exist', 'skipOnError' => true, 'targetClass' => Cities::class, 'targetAttribute' => ['city_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID локации',
            'city_id' => 'ID города',
            'lat' => 'широта локации',
            'long' => 'долгота локации',
        ];
    }

    /**
     * Gets query for [[City]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCity()
    {
        return $this->hasOne(Cities::class, ['id' => 'city_id']);
    }

    /**
     * Gets query for [[Tasks]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTasks()
    {
        return $this->hasMany(Tasks::class, ['location_id' => 'id']);
    }
}
