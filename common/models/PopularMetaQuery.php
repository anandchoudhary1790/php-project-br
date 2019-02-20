<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[PopularMeta]].
 *
 * @see PopularMeta
 */
class PopularMetaQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return PopularMeta[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return PopularMeta|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
