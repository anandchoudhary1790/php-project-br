<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[TransactionLog]].
 *
 * @see TransactionLog
 */
class TransactionLogQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return TransactionLog[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return TransactionLog|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
