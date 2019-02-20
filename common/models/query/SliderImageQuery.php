<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[SliderImage]].
 *
 * @see SliderImage
 */
class SliderImageQuery extends \yii\db\ActiveQuery
{
     public function active()
    {
        $this->andWhere(['status' => 1]);
        return $this;
    }

    /**
     * {@inheritdoc}
     * @return SliderImage[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return SliderImage|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
