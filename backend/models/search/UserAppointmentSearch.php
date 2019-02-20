<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\UserAppointment;

/**
 * UserAppointmentSearch represents the model behind the search form of `common\models\UserAppointment`.
 */
class UserAppointmentSearch extends UserAppointment
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'token', 'user_id', 'user_gender', 'doctor_id', 'doctor_address_id', 'start_time', 'end_time', 'schedule_id', 'slot_id', 'created_at', 'updated_at'], 'integer'],
            [['booking_type', 'type', 'user_name', 'user_age', 'user_phone', 'user_address', 'doctor_name', 'doctor_address', 'date', 'weekday', 'shift_name', 'book_for', 'payment_type', 'status'], 'safe'],
            [['doctor_fees', 'service_charge'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = UserAppointment::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'token' => $this->token,
            'user_id' => $this->user_id,
            'user_gender' => $this->user_gender,
            'doctor_id' => $this->doctor_id,
            'doctor_address_id' => $this->doctor_address_id,
            'doctor_fees' => $this->doctor_fees,
            'date' => $this->date,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'schedule_id' => $this->schedule_id,
            'slot_id' => $this->slot_id,
            'service_charge' => $this->service_charge,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'booking_type', $this->booking_type])
            ->andFilterWhere(['like', 'type', $this->type])
            ->andFilterWhere(['like', 'user_name', $this->user_name])
            ->andFilterWhere(['like', 'user_age', $this->user_age])
            ->andFilterWhere(['like', 'user_phone', $this->user_phone])
            ->andFilterWhere(['like', 'user_address', $this->user_address])
            ->andFilterWhere(['like', 'doctor_name', $this->doctor_name])
            ->andFilterWhere(['like', 'doctor_address', $this->doctor_address])
            ->andFilterWhere(['like', 'weekday', $this->weekday])
            ->andFilterWhere(['like', 'shift_name', $this->shift_name])
            ->andFilterWhere(['like', 'book_for', $this->book_for])
            ->andFilterWhere(['like', 'payment_type', $this->payment_type])
            ->andFilterWhere(['like', 'status', $this->status]);

        return $dataProvider;
    }
}
