<?php

namespace common\models\query;

use common\models\UserAttender;
use yii\db\ActiveQuery;
/**
 * This is the ActiveQuery class for [[UserAttender]].
 *
 * @see UserAttender
 */
class UserAttenderQuery extends ActiveQuery
{
    public function active()
    {
        $this->andWhere(['status' => 2]);
        return $this;
    }

    /**
     * @inheritdoc
     * @return UserAttender[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return UserAttender|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
