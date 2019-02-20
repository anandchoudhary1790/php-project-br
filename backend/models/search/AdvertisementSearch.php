<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Advertisement;

/**
 * AdvertisementSearch represents the model behind the search form of `common\models\Advertisement`.
 */
class AdvertisementSearch extends Advertisement
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'show_for_seconds', 'sequence', 'created_at', 'updated_at'], 'integer'],
            [['title', 'link', 'start_date', 'end_date', 'image_path', 'image_base_url', 'status'], 'safe'],
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
        $query = Advertisement::find();

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
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'show_for_seconds' => $this->show_for_seconds,
            'sequence' => $this->sequence,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'link', $this->link])
            ->andFilterWhere(['like', 'image_path', $this->image_path])
            ->andFilterWhere(['like', 'image_base_url', $this->image_base_url])
            ->andFilterWhere(['like', 'status', $this->status]);

        return $dataProvider;
    }
}
