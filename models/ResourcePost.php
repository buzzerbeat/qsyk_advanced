<?php

namespace qsyk\models;

use common\components\Utility;
use common\models\User;
use Yii;

/**
 * This is the model class for table "resource_post".
 *
 * @property integer $id
 * @property integer $resourceid
 * @property integer $post_id
 * @property integer $post_userid
 * @property string $content
 * @property integer $status
 * @property integer $type
 * @property integer $time
 * @property integer $last_update_time
 * @property integer $dig
 * @property integer $bury
 * @property string $quoteids
 * @property integer $user
 * @property string $useragent
 * @property string $ip
 * @property string $referer
 */
class ResourcePost extends \yii\db\ActiveRecord
{
    const STATUS_NORMAL = 2;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'resource_post';
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
            [['resourceid', 'content', 'status',  'time',  'user'], 'required'],
            [['resourceid', 'post_id', 'post_userid', 'status', 'type', 'time', 'last_update_time', 'dig', 'bury', 'user'], 'integer'],
            [['content', 'useragent'], 'string'],
            [['quoteids', 'referer'], 'string', 'max' => 400],
            [['ip'], 'string', 'max' => 20],
            ['post_userid', 'default', 'value' => 0],
            ['post_id', 'default', 'value' => 0],
            ['dig', 'default', 'value' => 0],
            ['bury', 'default', 'value' => 0],
            ['type', 'default', 'value' => 0],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'resourceid' => 'Resourceid',
            'post_id' => 'Post ID',
            'post_userid' => 'Post Userid',
            'content' => 'Content',
            'status' => 'Status',
            'type' => '评论类型',
            'time' => 'Time',
            'last_update_time' => 'Last Update Time',
            'dig' => 'Dig',
            'bury' => 'Bury',
            'quoteids' => 'Quoteids',
            'user' => 'User',
            'useragent' => 'Useragent',
            'ip' => 'Ip',
            'referer' => 'Referer',
        ];
    }
    public function getSid() {
        return Utility::sid($this->id);
    }

    public function getUserModel() {
        return $this->hasOne(User::className(), ['id'=>'user']);
    }

    public function getUserName()
    {
        return !empty($this->userModel) ? $this->userModel->username : '';
    }

    public function getUserAvatar()
    {
        return !empty($this->userModel) ? Utility::sid($this->userModel->avatar) : '';
    }

    public function getCreateTimeElapsed() {
        return Utility::time_get_past($this->time);
    }

    public function getCreateTime() {
        return $this->time;
    }
    public function getFloor() {
        $cache = yii::$app->cache;
        $key = "post_floor_" . $this->id;
        $data = $cache->get($key);
        if ($data === false) {
            $floor = ResourcePost::find()->where([
                'resourceid'=>$this->resourceid
            ])
                ->andWhere(['<','time',$this->time])
                ->count();

            $floorName = ['沙发', '板凳', '地板'];
            if (isset($floorName[$floor])) {
                $floor = $floorName[$floor];
            } else {
                $floor = ($floor + 1) . '楼';
            }
            // store $data in cache so that it can be retrieved next time
            $cache->set($key, $floor, $floorName[0]);
        }
        return $data;
    }

    public function getReply() {
        $reply = ResourcePost::findOne($this->post_id);
        if (empty($reply)) {
            return null;
        } else {
            return [
                "reply" => $reply->sid,
                "reply_content" => $reply->content,
                "reply_username" => $reply->userName,
            ];
        }
    }
    public function fields()
    {
        $fields = [
            'sid',
            'content',
            'userName',
            'userAvatar',
            'dig',
            'createTimeElapsed',
            'createTime',
            'floor',
            'reply',
            'index',
        ];
        return $fields; // TODO: Change the autogenerated stub
    }
}
