<?php

namespace app\models;

use Yii;

/**
 * Класс модели для таблицы "cities"
 *
 * @property int $id                        ID (первичный ключ)
 * @property string $city                   название города
 * @property float $lat                     широта
 * @property float $long                    долгота
 *
 * @property Locations[] $locations
 * @property Users[] $users
 */
class Cities extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cities';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['city', 'lat', 'long'], 'required'],
            [['lat', 'long'], 'number'],
            [['city'], 'string', 'max' => 64],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID города',
            'city' => 'название города',
            'lat' => 'широта',
            'long' => 'долгота',
        ];
    }

    /**
     * Gets query for [[Locations]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLocations()
    {
        return $this->hasMany(Locations::class, ['city_id' => 'id']);
    }

    /**
     * Gets query for [[Users]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(Users::class, ['city_id' => 'id']);
    }
}
