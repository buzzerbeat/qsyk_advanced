<?php

namespace qsyk\models;

use Yii;

/**
 * This is the model class for table "user_tag".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $tag_id
 */
class UserTag extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_tag';
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
            [['user_id', 'tag_id'], 'required'],
            [['user_id', 'tag_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'tag_id' => 'Tag ID',
        ];
    }
}
