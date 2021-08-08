<?php

namespace app\models;

use Yii;

/**
 * Класс модели для таблицы "users"
 *
 * @property int $id                        ID (первичный ключ)
 * @property string $role                   роль: заказчик или исполнитель
 * @property string $dt_add                 дата/время регистрации пользователя
 * @property string $name                   ФИО пользователя
 * @property string $email                  e-mail пользователя (login)
 * @property string $password               пароль
 * @property int $city_id                   местонахождение пользователя
 * @property string $dt_last_action         время последнего действия на сайте
 * @property string|null $birthday          дата рождения
 * @property string|null $about             информация о пользователе
 * @property string|null $phone             телефон (мобильный)
 * @property string|null $skype             skype
 * @property string|null $telegram          telegram
 * @property string|null $other_messenger   другой мессенжер
 * @property int $cnt_done_tasks            количество выполненных работ
 * @property int $cnt_failed_tasks          количество проваленных работ
 * @property float $rating                  рейтинг пользователя
 * @property int $notice_message            уведомлять о новом сообщении
 * @property int $notice_actions            уведомлять о действиях по заданию
 * @property int $notice_review             уведомлять о новом отзыве
 * @property int $hide_profile              не показывать профиль
 * @property int $customer_only             показывать контакты только заказчику
 * @property int $avatar_id                 ID фотографии пользователя
 *
 * @property ExecutorSpecialties[] $executorSpecialties
 * @property Messages[] $messagesSender
 * @property Messages[] $messagesRecipient
 * @property PhotosWorks[] $photosWorks
 * @property Responses[] $responses
 * @property Reviews[] $reviews
 * @property Tasks[] $tasksCustomer
 * @property Tasks[] $tasksExecutor
 * @property Cities $city
 * @property Files $avatar
 */
class Users extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'users';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['role'], 'string'],
            [['dt_add', 'dt_last_action', 'birthday'], 'safe'],
            [['name', 'email', 'password', 'city_id', 'avatar_id'], 'required'],
            [['city_id', 'cnt_done_tasks', 'cnt_failed_tasks', 'notice_message', 'notice_actions', 'notice_review', 'hide_profile', 'customer_only', 'avatar_id'], 'integer'],
            [['rating'], 'number'],
            [['name', 'email', 'password', 'skype', 'telegram', 'other_messenger'], 'string', 'max' => 64],
            [['about'], 'string', 'max' => 512],
            [['phone'], 'string', 'max' => 32],
            [['email'], 'unique'],
            [['city_id'], 'exist', 'skipOnError' => true, 'targetClass' => Cities::class, 'targetAttribute' => ['city_id' => 'id']],
            [['avatar_id'], 'exist', 'skipOnError' => true, 'targetClass' => Files::class, 'targetAttribute' => ['avatar_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID пользователя',
            'role' => 'роль пользователя',
            'dt_add' => 'дата/время регистрации',
            'name' => 'ФИО пользователя',
            'email' => 'e-mail',
            'password' => 'пароль',
            'city_id' => 'ID города',
            'dt_last_action' => 'время последнего действия',
            'birthday' => 'дата рождения',
            'about' => 'информация о пользователе',
            'phone' => 'телефон',
            'skype' => 'Skype',
            'telegram' => 'Telegram',
            'other_messenger' => 'другой мессенжер',
            'cnt_done_tasks' => 'количество выполненных работ',
            'cnt_failed_tasks' => 'количество проваленных работ',
            'rating' => 'рейтинг пользователя',
            'notice_message' => 'уведомлять о новом сообщении',
            'notice_actions' => 'уведомлять о действиях по заданию',
            'notice_review' => 'уведомлять о новом отзыве',
            'hide_profile' => 'не показывать профиль',
            'customer_only' => 'показывать контакты только заказчику',
            'avatar_id' => 'ID фотографии пользователя',
        ];
    }

    /**
     * Gets query for [[ExecutorSpecialties]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getExecutorSpecialties()
    {
        return $this->hasMany(ExecutorSpecialties::class, ['executor_id' => 'id']);
    }

    /**
     * Gets query for [[Messages]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMessagesSender()
    {
        return $this->hasMany(Messages::class, ['sender_id' => 'id']);
    }

    /**
     * Gets query for [[Messages]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMessagesRecipient()
    {
        return $this->hasMany(Messages::class, ['recipient_id' => 'id']);
    }

    /**
     * Gets query for [[PhotosWorks]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPhotosWorks()
    {
        return $this->hasMany(PhotosWorks::class, ['executor_id' => 'id']);
    }

    /**
     * Gets query for [[Responses]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getResponses()
    {
        return $this->hasMany(Responses::class, ['executor_id' => 'id']);
    }

    /**
     * Gets query for [[Reviews]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReviews()
    {
        return $this->hasMany(Reviews::class, ['executor_id' => 'id']);
    }

    /**
     * Gets query for [[Tasks]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTasksCustomer()
    {
        return $this->hasMany(Tasks::class, ['customer_id' => 'id']);
    }

    /**
     * Gets query for [[Tasks]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTasksExecutor()
    {
        return $this->hasMany(Tasks::class, ['executor_id' => 'id']);
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
     * Gets query for [[Avatar]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAvatar()
    {
        return $this->hasOne(Files::class, ['id' => 'avatar_id']);
    }
}
