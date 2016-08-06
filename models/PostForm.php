<?php
/**
 * Created by PhpStorm.
 * User: cx
 * Date: 2016/5/31
 * Time: 10:36
 */

namespace qsyk\models;


use common\components\Utility;
use qsyk\models\ResourceCount;
use qsyk\models\ResourcePost;
use Yii;
use yii\base\Model;

class PostForm extends Model
{
    public $content;
    public $sid;
    public $reply;
    public $retPost;

    public function rules()
    {
        return [
            // username and password are both required
            [['content', 'sid'], 'required'],
            [['sid'],  'string'],
            ['content',  'string', 'min' => 3, 'max' => 2000],
            ['content', 'validateSimilar'],
        ];
    }

    public function validateSimilar($attribute, $params)
    {
        $latestPosts = ResourcePost::find()
            ->orderBy(['time'=>SORT_DESC])
            ->limit(10)
            ->all();
        foreach($latestPosts as $post) {
            similar_text($post->content, $this->content, $percent);
            if (number_format($percent, 0) > 90){
                $this->addError($attribute, '请勿连续发表相似内容，您可以查看之前发表的内容是否已经成功');
                return;
            }
        }
    }

    public function getResourceId() {
        return Utility::id($this->sid);
    }

    public function getReplyId() {
        return Utility::id($this->reply);
    }

    private function updateCounters() {
        $rCount = ResourceCount::findOne($this->resourceId);
        if (empty($rCount)) {
            $rCount = new ResourceCount();
            $rCount->setAttributes([
                'resource_id'=>$this->resourceId,
                'resource_post'=>1,
                'resource_post_daily'=>1,
                'resource_post_week'=>1,
            ]);
            $rCount->save();
        } else {
            $rCount->updateCounters(['resource_post' => 1]);
            $rCount->updateCounters(['resource_post_daily' => 1]);
            $rCount->updateCounters(['resource_post_week' => 1]);
        }
    }

    public function send() {
        $user = \Yii::$app->user->identity;
        if ($this->validate()) {
            $headers = Yii::$app->request->headers;
            $post = new ResourcePost();
            $post->setAttributes([
                'resourceid'=>$this->resourceId,
                'content'=>$this->content,
                'status'=>ResourcePost::STATUS_NORMAL,
                'user'=>$user->id,
                'time'=>time(),
                'useragent'=>$headers->get('User-Agent', ''),
                'ip'=>Yii::$app->request->getUserIP(),
            ]);

            if (!empty($this->reply)) {
                $reply = ResourcePost::findOne($this->replyId);
                if (!empty($reply)) {
                    $post->post_userid = $reply->user;
                    $post->post_id = $this->replyId;
                }
            }


            if (!$post->save()) {
                $this->addErrors($post->getErrors());
                return false;
            }
            $this->retPost = $post;
            $this->updateCounters();
            return true;
        }
        return false;
    }


}