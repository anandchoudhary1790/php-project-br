<?php

namespace backend\models\search;

use common\models\Groups;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\User;

/**
 * UserSearch represents the model behind the search form about `common\models\User`.
 */
class DoctorSearch extends User
{
    public $name;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'status', 'created_at', 'updated_at', 'logged_at'], 'integer'],
            [['name', 'auth_key', 'password_hash', 'email','phone','admin_status'], 'safe'],
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
    public function search($params,$logined)
    {
        $query = User::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ]
            ],
        ]);

        $query->joinWith(['userProfile']);
        $query->Where(['user_profile.groupid' => Groups::GROUP_DOCTOR]);
        if($logined->role=='SubAdmin'){
            $query->andWhere(['user.admin_user_id' => $logined->id]);
        }
        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'logged_at' => $this->logged_at
        ]);

        if(!empty($this->admin_status) && $this->admin_status != '0'){
            $query->andWhere(['admin_status'=>$this->admin_status]);
        }

        $query->andFilterWhere(['like', 'user.phone', $this->phone])
            ->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }

    public function linkedDoctors($params,$ids=[],$groupid)
    {
        $query = User::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ]
            ],
        ]);
        if(count($ids)>0){
            $query->andWhere(['user.id'=>$ids]);
            $query->andWhere(['user.groupid'=>$groupid]);
            $query->joinWith(['userProfile']);
            $query->andWhere(['user_profile.groupid' => $groupid]);
            if (!($this->load($params) && $this->validate())) {
                return $dataProvider;
            }

            $query->andFilterWhere([
                'id' => $this->id,
                'status' => $this->status,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
                'logged_at' => $this->logged_at
            ]);

            $query->andFilterWhere(['like', 'user.username', $this->username])
                ->andFilterWhere(['like', 'auth_key', $this->auth_key])
                ->andFilterWhere(['like', 'password_hash', $this->password_hash])
                ->andFilterWhere(['like', 'user.email', $this->email]);

           
        }else{
            $query->andWhere(['user.id'=>0]);
        }

         return $dataProvider;
    }
}
