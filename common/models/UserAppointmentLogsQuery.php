<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[UserAppointmentLogs]].
 *
 * @see UserAppointmentLogs
 */
class UserAppointmentLogsQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return UserAppointmentLogs[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return UserAppointmentLogs|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
