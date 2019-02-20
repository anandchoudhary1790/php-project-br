<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[HospitalAttender]].
 *
 * @see HospitalAttender
 */
class HospitalAttenderQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return HospitalAttender[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return HospitalAttender|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
