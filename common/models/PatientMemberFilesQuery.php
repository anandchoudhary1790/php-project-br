<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[PatientMemberFiles]].
 *
 * @see PatientMemberFiles
 */
class PatientMemberFilesQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return PatientMemberFiles[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return PatientMemberFiles|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
