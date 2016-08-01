<?php

namespace qsyk\models;

use Yii;

/**
 * This is the model class for table "resource_report".
 *
 * @property integer $id
 * @property integer $resource_id
 * @property integer $type
 * @property integer $user_id
 * @property string $user_mid
 * @property integer $time
 * @property integer $check_admin
 * @property integer $check_time
 * @property integer $check_result
 */
class ResourceReport extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'resource_report';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['resource_id', 'type', 'user_id', 'time'], 'required'],
            [['resource_id', 'type', 'user_id', 'time', 'check_admin', 'check_time', 'check_result'], 'integer'],
            [['user_mid'], 'string', 'max' => 32],
        ];
    }
    
    public static function getDb()
    {
        return Yii::$app->get('qsykDb');
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'resource_id' => 'Resource ID',
            'type' => '0广告，1辱骂，2色情，3刷楼',
            'user_id' => '举报人',
            'user_mid' => '举报人标识',
            'time' => 'Time',
            'check_admin' => '复审管理员',
            'check_time' => '复审时间',
            'check_result' => '复审结果0待复审，1复审正常，2复审违规',
        ];
    }
}
