<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\UserSchedule;

/**
 * UserSearch represents the model behind the search form about `common\models\User`.
 */
class UserScheduleSearch extends UserSchedule
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'patient_limit', 'status'], 'required'],
            [['user_id', 'patient_limit', 'address_id','start_time', 'end_time','shift'], 'integer'],
            [['weekday', ], 'string'],
            [['status'], 'integer', 'max' => 4],
            [['consultation_fees', 'emergency_fees'], 'number'],
            [['consultation_days','emergency_days','consultation_show','emergency_show'], 'integer'],

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
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        if(isset($params['id']) && isset($params['day'])){
            $query = UserSchedule::find()->where(['user_id'=>$params['id']])->andWhere(['weekday'=>$params['day']]);
        }else
        $query = UserSchedule::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'consultation_fees', $this->consultation_fees])
            ->andFilterWhere(['like', 'consultation_days', $this->consultation_days])
            ->andFilterWhere(['like', 'weekday', $this->weekday])
            ->andFilterWhere(['like', 'emergency_show', $this->emergency_show]);

        return $dataProvider;
    }
}
