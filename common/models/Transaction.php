<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "transaction".
 *
 * @property int $id
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
 * @property int $created_at
 * @property int $updated_at
 */
class Transaction extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'transaction';
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
            [['type', 'txn_type', 'payment_type', 'paytm_response'], 'string'],
            [['user_id', 'txn_date', 'status'], 'required'],
            [['user_id', 'appointment_id','temp_appointment_id'], 'integer'],
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
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @inheritdoc
     * @return TransactionQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new TransactionQuery(get_called_class());
    }
}
