<?php
namespace backend\models;

use common\models\User;
use common\models\Groups;
use yii\base\Exception;
use yii\base\Model;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Create user form
 */
class UserForm extends Model
{
    public $username;
    public $email;
    public $password;
    public $status;
    public $roles;
    public $name;
    public $phone;
    public $type;
    private $model;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['username', 'filter', 'filter' => 'trim'],
            [['phone','name'], 'required'],
            ['username', 'unique', 'targetClass' => User::className(), 'filter' => function ($query) {
                if (!$this->getModel()->isNewRecord) {
                    $query->andWhere(['not', ['id'=>$this->getModel()->id]]);
                }
            }],
            ['username', 'string', 'min' => 2, 'max' => 255],
            ['phone','integer'],
            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'unique', 'targetClass'=> User::className(), 'filter' => function ($query) {
                if (!$this->getModel()->isNewRecord) {
                    $query->andWhere(['not', ['id'=>$this->getModel()->id]]);
                }
            }],

            ['password', 'required', 'on' => 'create'],
            ['password', 'string', 'min' => 6],

            [['status'], 'integer'],
            [['roles'], 'each',
                'rule' => ['in', 'range' => ArrayHelper::getColumn(
                    Yii::$app->authManager->getRoles(),
                    'name'
                )]
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'username' => Yii::t('common', 'Username'),
            'email' => Yii::t('common', 'Email'),
            'status' => Yii::t('common', 'Status'),
            'password' => Yii::t('common', 'Password'),
            'roles' => Yii::t('common', 'Roles')
        ];
    }

    /**
     * @param User $model
     * @return mixed 
     */
    public function setModel($model)
    {
        $this->username = $model->username;
        $this->email = $model->email;
        $this->status = $model->status;
        $this->name = $model['userProfile']['name'];
        $this->phone = $model->phone;
        $this->password = $model->password;
        $this->status = $model->status;
        $this->model = $model;
        $this->roles = ArrayHelper::getColumn(
            Yii::$app->authManager->getRolesByUser($model->getId()),
            'name'
        );
        return $this->model;
    }

    /**
     * @return User
     */
    public function getModel()
    {
        if (!$this->model) {
            $this->model = new User();
        }
        return $this->model;
    }

    /**
     * Signs user up.
     * @return User|null the saved model or null if saving fails
     * @throws Exception
     */
    public function save()
    {
        if ($this->validate()) {
            $model = $this->getModel();
            $isNewRecord = $model->getIsNewRecord();
            if($this->type!='edit' || $this->type=='')
                $model->username = $this->setUserName($this->email);
            $model->email = $this->email;
            $model->status = $this->status;
            $model->phone = $this->phone;
            $model->role = 'SubAdmin';
            $model->groupid = 1;
            if ($this->password) {
                $model->setPassword($this->password);
                $model->password=$this->password;
            }
            if (!$model->save()) {
                throw new Exception('Model not saved');
            }

            $profile=['name'=>$this->name,'groupid'=>1];
            if ($isNewRecord) {
                $model->afterSignup($profile);
            }
            $auth = Yii::$app->authManager;
            $auth->revokeAll($model->getId());

            if ($this->roles && is_array($this->roles)) {
                foreach ($this->roles as $role) {
                    $auth->assign($auth->getRole($role), $model->getId());
                }
            }

            return !$model->hasErrors();
        }
        return null;
    }

    public function setUserName($email){

        $username=explode('@',$email); 
        $username=$username[0]; 
        $count=User::find()->andWhere(['username'=>$username])->count();
        if($count>0){
            $username=$username.'_'.$count+1;
        }
        return $username;
    }
}
