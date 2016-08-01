<?php
/**
 * Created by PhpStorm.
 * User: cx
 * Date: 2016/6/13
 * Time: 15:23
 */

namespace qsyk\models;


use common\components\Utility;
use yii\base\Model;

class TagFollowForm extends Model
{
    public $tag;
    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['tag'], 'required'],
            ['tag', 'validateTag'],
        ];
    }

    public function validateTag($attribute, $params)
    {
        $tagId = $this->getTagId();
        if ($tagId == 0) {
            $this->addError('tag', 'tag参数不合法');
        }
        $tag = Tag::findOne($tagId);
        if (empty($tag))  {
            $this->addError('tag', 'tag不存在');
        }
    }

    public function getTagId() {
        return Utility::id($this->tag);
    }

    public function follow() {
        $user = \Yii::$app->user->identity;
        if ($this->validate()) {
            $follow = UserTag::find()->where([
                'tag_id'=>$this->getTagId(),
                'user_id'=>$user->id,
            ])->one();
            if (empty($follow)) {
                $follow = new UserTag();
                $follow->tag_id = $this->getTagId();
                $follow->user_id = $user->id;
                if (!$follow->save())  {
                    $this->addErrors($follow->getErrors());
                    return false;
                }

            }
            return true;
        }
        return false;
    }

    public function unFollow() {
        $user = \Yii::$app->user->identity;
        if ($this->validate()) {
            $follow = UserTag::find()->where([
                'tag_id'=>$this->getTagId(),
                'user_id'=>$user->id,
            ])->one();
            if (!empty($follow)) {
                if (!$follow->delete())  {
                    $this->addErrors($follow->getErrors());
                    return false;
                }
            }
            return true;
        }
        return false;
    }
}