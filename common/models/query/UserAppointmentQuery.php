<?php

namespace common\models\query;

use common\models\UserAppointment;
use yii\db\ActiveQuery;

/**
 * Class UserQuery
 * @package common\models\query
 * @author Eugene Terentev <eugene@terentev.net>
 */
class UserAppointmentQuery extends ActiveQuery
{
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