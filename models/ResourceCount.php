<?php

namespace qsyk\models;

use Yii;

/**
 * This is the model class for table "resource_count".
 *
 * @property integer $resource_id
 * @property integer $resource_hits
 * @property integer $resource_hits_daily
 * @property integer $resource_hits_week
 * @property integer $resource_post
 * @property integer $resource_post_daily
 * @property integer $resource_post_week
 * @property integer $resource_pre_dig
 * @property integer $resource_dig
 * @property integer $resource_bury
 * @property integer $resource_collect
 * @property integer $resource_share
 * @property integer $resource_share_click
 */
class ResourceCount extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'resource_count';
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
            [['resource_hits', 'resource_hits_daily', 'resource_hits_week', 'resource_post', 'resource_post_daily', 'resource_post_week', 'resource_pre_dig', 'resource_dig', 'resource_bury', 'resource_collect', 'resource_share', 'resource_share_click'], 'required'],
            [['resource_hits', 'resource_hits_daily', 'resource_hits_week', 'resource_post', 'resource_post_daily', 'resource_post_week', 'resource_pre_dig', 'resource_dig', 'resource_bury', 'resource_collect', 'resource_share', 'resource_share_click'], 'integer'],
            [['resource_hits', 'resource_hits_daily', 'resource_hits_week', 'resource_post', 'resource_post_daily', 'resource_post_week', 'resource_pre_dig', 'resource_dig', 'resource_bury', 'resource_collect', 'resource_share', 'resource_share_click'], 'default', 'value' =>0],

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'resource_id' => 'Resource ID',
            'resource_hits' => 'Resource Hits',
            'resource_hits_daily' => 'Resource Hits Daily',
            'resource_hits_week' => 'Resource Hits Week',
            'resource_post' => 'Resource Post',
            'resource_post_daily' => 'Resource Post Daily',
            'resource_post_week' => 'Resource Post Week',
            'resource_pre_dig' => '系统顶赞记录，获取用户dig数据时应扣除该值',
            'resource_dig' => 'Resource Dig',
            'resource_bury' => 'Resource Bury',
            'resource_collect' => 'Resource Collect',
            'resource_share' => 'Resource Share',
            'resource_share_click' => '分享页面点击数',
        ];
    }
}
