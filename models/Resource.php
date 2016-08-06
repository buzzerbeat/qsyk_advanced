<?php

namespace qsyk\models;

use qsyk\commands\DataController;
use kartik\helpers\Enum;
//use common\models\User;
use qsyk\models\User;
use common\components\Utility;
use Yii;

/**
 * This is the model class for table "resource".
 *
 * @property integer $id
 * @property integer $type
 * @property string $desc
 * @property integer $status
 * @property integer $rank
 * @property integer $add_time
 * @property integer $pub_time
 * @property integer $del_time
 * @property integer $last_update_time
 * @property integer $pre_pub_set
 * @property integer $pre_pub_time
 * @property integer $pub_way
 * @property integer $userid
 * @property integer $adminid
 */
class Resource extends \yii\db\ActiveRecord
{

    const TYPE_ALL = 0;
    const TYPE_TEXT = 1;
    const TYPE_IMAGE = 2;
    const TYPE_VIDEO = 3;
    const TYPE_VOICE = 4;
    const TYPE_DYNAMIC = 5;

    const STATUS_WAIT = 0;
    const STATUS_ACTIVE = 2;
    const STATUS_REPORT = 3;
    const STATUS_PRE_PUB = 4;
    const STATUS_DELETE = 99;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'resource';
    }
    
    public static function getDb()
    {
        return Yii::$app->get('qsykDb');
    }

    public function getUser() {
        return $this->hasOne(User::className(), ['id'=>'userid']);
    }

    public function getPosts() {
        return $this->hasMany(ResourcePost::className(), ['resourceid'=>'id'])
            ->andOnCondition(['in','status', [1,2]])
            ->orderBy(['time' => SORT_DESC]);
    }

    public function getHotPosts() {
        return $this->hasMany(ResourcePost::className(), ['resourceid'=>'id'])
            ->andOnCondition(['in','status', [1,2]])
            ->andOnCondition(['>','dig', 10])
            ->orderBy(['dig' => SORT_DESC])->limit(10);
    }


    public function getIndexTag()
    {
        $tagRel = TagRel::find()->where(['resource_id'=>$this->id])->andWhere(['>', 'rank', 0])->orderBy(['rank'=>SORT_DESC])->one();
        if (!empty($tagRel)) {
            return Tag::findOne($tagRel->tag_id);
        } else {
            return null;
        }
    }
    public function getTagRels()
    {
        return $this->hasMany(TagRel::className(), ['resource_id'=>'id']);
    }
    public function getTags() {
        return $this->hasMany(Tag::className(), ['id' => 'tag_id'])
            ->andOnCondition(['app_res_hide'=>0])
            ->via('tagRels');
    }

    public function getGodPosts() {
        return $this->hasMany(ResourcePost::className(), ['resourceid'=>'id'])
            ->andOnCondition(['in','status', [1,2]])
            ->andOnCondition(['>','index', 0])
            ->orderBy(['dig' => SORT_DESC])->limit(3);
    }

    public function getResourceCount() {
        return $this->hasOne(ResourceCount::className(), ['resource_id'=>'id']);
    }

    public function getResourceRel() {
        return $this->hasOne(ResourceRelation::className(), ['resource_id'=>'id']);
    }

    public function getRelImage() {
        if (!empty($this->resourceRel)) {
            if ($this->resourceRel->rel_type == 1) {
                $relImg = ResourceImage::findOne($this->resourceRel->rel_id);
                if (!empty($relImg)) {
                    return $relImg;
                }
            }
        }
        return null;
    }

    public function getRelVideo() {
        if (!empty($this->resourceRel)) {
            if ($this->resourceRel->rel_type == 2) {
                $relVideo = ResourceVideo::findOne($this->resourceRel->rel_id);
                if (!empty($relVideo)) {
                    return $relVideo;
                }
            }
        }
        return null;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'desc', 'status', 'rank', 'add_time', 'pub_time', 'del_time', 'last_update_time', 'pre_pub_set', 'pre_pub_time', 'pub_way', 'userid', 'adminid'], 'required'],
            [['type', 'status', 'rank', 'add_time', 'pub_time', 'del_time', 'last_update_time', 'pre_pub_set', 'pre_pub_time', 'pub_way', 'userid', 'adminid'], 'integer'],
            [['desc'], 'string', 'max' => 2000],
        ];
    }

    public function getSid()
    {
        return Utility::sid($this->id);
    }

    public function getUserName()
    {
        return !empty($this->user) ? $this->user->nick_name : '';
    }

    public function getRandomCache() {
        return $this->hasOne(RandomCache::className(), [ 'resource_id' => 'id']);
    }

    public function getUserAvatar()
    {
        return !empty($this->user) ? Utility::sid($this->user->avatar_img) : '';
    }
    public function getDig() {
        return !empty($this->resourceCount) ? $this->resourceCount->resource_dig : 0;
    }

    public function getBury() {
        return !empty($this->resourceCount) ? $this->resourceCount->resource_bury : 0;
    }

    public function getShare() {
        return !empty($this->resourceCount) ? $this->resourceCount->resource_share : 0;
    }

    public function getPost() {
        return !empty($this->resourceCount) ? $this->resourceCount->resource_post : 0;
    }

    public function getFavorite() {
        return !empty($this->resourceCount) ? $this->resourceCount->resource_collect : 0;
    }
    public function getPubTimeElapsed() {
//        return $this->pub_time;
        return Utility::time_get_past($this->pub_time);
    }

    public function extraFields()
    {
        $extraFields = ['hotPosts', 'posts', 'godPosts', 'randomCache',  'tags'];
        return $extraFields;
    }

    public function fields()
    {
//        $fields = parent::fields();
        $fields = [
            'id',
            'sid',
            'type',
            'desc',
            'userName',
            'userAvatar',
            'pubTimeElapsed',
            'dig',
            'bury',
            'share',
            'post',
            'favorite',
            'relImage',
            'relVideo',
            'indexTag',

        ];
        // remove fields that contain sensitive information
        return $fields;
    }
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => 'Type',
            'desc' => 'Desc',
            'status' => 'Status',
            'rank' => 'Rank',
            'add_time' => 'Add Time',
            'pub_time' => 'Pub Time',
            'del_time' => 'Del Time',
            'last_update_time' => 'Last Update Time',
            'pre_pub_set' => '是否是指定时间预发布',
            'pre_pub_time' => 'Pre Pub Time',
            'pub_way' => '发布方式：1来自推荐，2编辑操作，3随机捞取',
            'userid' => 'Userid',
            'adminid' => 'Adminid',
        ];
    }
}
