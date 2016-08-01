<?php

namespace qsyk\models;

use common\components\Utility;
use Yii;

/**
 * This is the model class for table "resource_favorite".
 *
 * @property integer $resource_id
 * @property integer $user_id
 * @property integer $time
 */
class ResourceFavorite extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'resource_favorite';
    }
    
    public static function getDb()
    {
        return Yii::$app->get('qsykDb');
    }

    public function getResourceCount()
    {
        return $this->hasOne(ResourceCount::className(), ['resource_id' => 'resource_id']);
    }

    public function getResource()
    {
        return $this->hasOne(Resource::className(), ['id' => 'resource_id']);
    }
    public function getTimeElapsed()
    {
        return Utility::time_get_past($this->time);

    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['resource_id', 'user_id', 'time'], 'required'],
            [['resource_id', 'user_id', 'time'], 'integer'],
        ];
    }

    public function fields()
    {
        $fields = [
            'resource',
            'timeElapsed'
        ];
        return $fields;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'resource_id' => 'Resource ID',
            'user_id' => 'User ID',
            'time' => 'Time',
        ];
    }
}
