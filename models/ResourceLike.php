<?php

namespace qsyk\models;

use common\components\Utility;
use Yii;

/**
 * This is the model class for table "resource_like".
 *
 * @property integer $resource_id
 * @property integer $user_id
 * @property string $user_mid
 * @property integer $status
 * @property integer $resource_user
 * @property integer $time
 */
class ResourceLike extends \yii\db\ActiveRecord
{
    const STATUS_CANCEL = 0;
    const STATUS_LIKE = 1;
    const STATUS_HATE = 2;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'resource_like';
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
            [['resource_id', 'user_id', 'status', 'time'], 'required'],
            [['resource_id', 'user_id', 'status',  'time'], 'integer'],
            [['user_mid'], 'string', 'max' => 32],
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
            'user_mid' => '用户mid识别',
            'status' => '0为取消，1为赞，2为踩',
            'resource_user' => '发布资源的用户id',
            'time' => 'Time',
        ];
    }
}
