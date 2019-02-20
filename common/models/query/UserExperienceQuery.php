<?php

namespace common\models\query;

use common\models\UserExperience;
use yii\db\ActiveQuery;

/**
 * Class UserQuery
 * @package common\models\query
 * @author Eugene Terentev <eugene@terentev.net>
 */
class UserExperienceQuery extends ActiveQuery
{
    /**
     * @return $this
     */
    public function notDeleted()
    {
        $this->andWhere(['!=', 'status', UserExperience::STATUS_DELETED]);
        return $this;
    }

    /**
     * @return $this
     */
    public function active()
    {
        $this->andWhere(['status' => UserExperience::STATUS_ACTIVE]);
        return $this;
    }

     /**
     * {@inheritdoc}
     * @return UserPlan[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return UserPlan|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}