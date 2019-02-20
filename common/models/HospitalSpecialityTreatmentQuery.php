<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[HospitalSpecialityTreatment]].
 *
 * @see HospitalSpecialityTreatment
 */
class HospitalSpecialityTreatmentQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return HospitalSpecialityTreatment[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return HospitalSpecialityTreatment|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
