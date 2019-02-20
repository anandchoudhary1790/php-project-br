<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "transaction_log".
 *
 * @property int $id
 * @property int $txn_id
 * @property string $type
 * @property string $txn_type
 * @property int $user_id
 * @property int $appointment_id
 * @property string $payment_type
 * @property double $base_price
 * @property double $cancellation_charge
 * @property double $txn_amount
 * @property string $originate_date
 * @property string $txn_date
 * @property string $paytm_response
 * @property string $status
 * @property string $comment
 * @property int $created_at
 * @property int $updated_at
 */
class TransactionLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'transaction_log';
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['txn_id', 'user_id', 'txn_date', 'status', 'comment'], 'required'],
            [['txn_id', 'user_id', 'appointment_id','temp_appointment_id'], 'integer'],
            [['type', 'txn_type', 'payment_type', 'paytm_response', 'comment'], 'string'],
            [['base_price', 'cancellation_charge', 'txn_amount'], 'number'],
            [['originate_date', 'txn_date'], 'safe'],
            [['status'], 'string', 'max' => 45],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'txn_id' => 'Txn ID',
            'type' => 'Type',
            'txn_type' => 'Txn Type',
            'user_id' => 'User ID',
            'appointment_id' => 'Appointment ID',
            'payment_type' => 'Payment Type',
            'base_price' => 'Base Price',
            'cancellation_charge' => 'Cancellation Charge',
            'txn_amount' => 'Txn Amount',
            'originate_date' => 'Originate Date',
            'txn_date' => 'Txn Date',
            'paytm_response' => 'Paytm Response',
            'status' => 'Status',
            'comment' => 'Comment',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @inheritdoc
     * @return TransactionLogQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new TransactionLogQuery(get_called_class());
    }
}
