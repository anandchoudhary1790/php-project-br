<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[UserAboutus]].
 *
 * @see UserAboutus
 */
class UserAboutusQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return UserAboutus[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return UserAboutus|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
