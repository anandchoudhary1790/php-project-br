<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[PatientMembersFiles]].
 *
 * @see PatientMembersFiles
 */
class PatientMembersFilesQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return PatientMembersFiles[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return PatientMembersFiles|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
