<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[UserAddressImages]].
 *
 * @see UserAddressImages
 */
class UserAddressImagesQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return UserAddressImages[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return UserAddressImages|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
