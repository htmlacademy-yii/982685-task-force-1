<?php

namespace app\models;

use Yii;

/**
 * Класс модели для таблицы "messages"
 *
 * @property int $id                        ID (первичный ключ)
 * @property string $dt_add                 дата/время отправки сообщения
 * @property string $message_text           текст сообщения
 * @property int $message_read              признак, было ли прочитано сообщение
 * @property int $task_id                   ID задания
 * @property int $sender_id                 ID отправителя сообщения
 * @property int $recipient_id              ID получателя сообщения
 *
 * @property Tasks $task
 * @property Users $sender
 * @property Users $recipient
 */
class Messages extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'messages';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['dt_add'], 'safe'],
            [['message_text', 'task_id', 'sender_id', 'recipient_id'], 'required'],
            [['message_read', 'task_id', 'sender_id', 'recipient_id'], 'integer'],
            [['message_text'], 'string', 'max' => 1024],
            [['task_id'], 'exist', 'skipOnError' => true, 'targetClass' => Tasks::class, 'targetAttribute' => ['task_id' => 'id']],
            [['sender_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['sender_id' => 'id']],
            [['recipient_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['recipient_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID сообщения',
            'dt_add' => 'дата/время отправки сообщения',
            'message_text' => 'текст сообщения',
            'message_read' => 'прочитано ли сообщение',
            'task_id' => 'ID задания',
            'sender_id' => 'ID отправителя',
            'recipient_id' => 'ID получателя',
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
     * Gets query for [[Sender]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSender()
    {
        return $this->hasOne(Users::class, ['id' => 'sender_id']);
    }

    /**
     * Gets query for [[Recipient]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRecipient()
    {
        return $this->hasOne(Users::class, ['id' => 'recipient_id']);
    }
}
