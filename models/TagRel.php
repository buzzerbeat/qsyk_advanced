<?php

namespace qsyk\models;

use Yii;

/**
 * This is the model class for table "tag_rel".
 *
 * @property integer $id
 * @property integer $tag_id
 * @property integer $resource_id
 */
class TagRel extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tag_rel';
    }
    
    public static function getDb()
    {
        return Yii::$app->get('qsykDb');
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tag_id', 'resource_id'], 'required'],
            [['tag_id', 'resource_id'], 'integer'],
            [['tag_id', 'resource_id'], 'unique', 'targetAttribute' => ['tag_id', 'resource_id'], 'message' => 'The combination of Tag ID and Resource ID has already been taken.'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tag_id' => 'Tag ID',
            'resource_id' => 'Resource ID',
        ];
    }
}
