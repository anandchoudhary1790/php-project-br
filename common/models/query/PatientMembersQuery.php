<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[PatientMembers]].
 *
 * @see PatientMembers
 */
class PatientMembersQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return PatientMembers[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return PatientMembers|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
