<?php

namespace common\models\query;

use yii\db\ActiveQuery;
use common\models\UserDirectory;
/**
 * This is the ActiveQuery class for [[UserDirectory]].
 *
 * @see UserDirectory
 */
class UserDirectoryQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return UserDirectory[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return UserDirectory|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
