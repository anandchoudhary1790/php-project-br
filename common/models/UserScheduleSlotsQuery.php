<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[UserScheduleSlots]].
 *
 * @see UserScheduleSlots
 */
class UserScheduleSlotsQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return UserScheduleSlots[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return UserScheduleSlots|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
