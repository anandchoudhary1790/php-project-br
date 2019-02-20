<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "countries".
 *
 * @property int $id
 * @property string $name
 * @property string $fips104
 * @property string $dialcode
 * @property string $iso2
 * @property string $iso3
 * @property string $ison
 * @property string $internet
 * @property string $capital
 * @property string $mapreference
 * @property string $nationalitysingular
 * @property string $nationalityplural
 * @property string $currency
 * @property string $currencycode
 * @property string $population
 * @property string $title
 * @property string $comment
 * @property string $status
 */
class Countries extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'countries';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status'], 'string'],
            [['name', 'fips104', 'dialcode', 'iso2', 'iso3', 'ison', 'internet', 'capital', 'mapreference', 'nationalitysingular', 'nationalityplural', 'currency', 'currencycode', 'population', 'title', 'comment'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'fips104' => 'Fips104',
            'dialcode' => 'Dialcode',
            'iso2' => 'Iso2',
            'iso3' => 'Iso3',
            'ison' => 'Ison',
            'internet' => 'Internet',
            'capital' => 'Capital',
            'mapreference' => 'Mapreference',
            'nationalitysingular' => 'Nationalitysingular',
            'nationalityplural' => 'Nationalityplural',
            'currency' => 'Currency',
            'currencycode' => 'Currencycode',
            'population' => 'Population',
            'title' => 'Title',
            'comment' => 'Comment',
            'status' => 'Status',
        ];
    }
}
