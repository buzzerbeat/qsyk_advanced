<?php

namespace qsyk\models;

use Yii;

/**
 * This is the model class for table "resource_relation".
 *
 * @property integer $id
 * @property integer $resource_id
 * @property integer $rel_type
 * @property integer $rel_id
 * @property integer $dynamic
 */
class ResourceRelation extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'resource_relation';
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
            [['resource_id', 'rel_type', 'rel_id', 'dynamic'], 'required'],
            [['resource_id', 'rel_type', 'rel_id', 'dynamic'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'resource_id' => 'Resource ID',
            'rel_type' => '1为图片，2为视频',
            'rel_id' => '视频或图片id',
            'dynamic' => '如果是图片，是否为动图',
        ];
    }
}
