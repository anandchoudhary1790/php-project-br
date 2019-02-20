<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[UserScheduleGroup]].
 *
 * @see UserScheduleGroup
 */
class UserScheduleGroupQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return UserScheduleGroup[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return UserScheduleGroup|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
