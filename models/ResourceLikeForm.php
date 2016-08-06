<?php
/**
 * Created by PhpStorm.
 * User: cx
 * Date: 2016/5/6
 * Time: 12:29
 */

namespace qsyk\models;


use common\components\Utility;
use yii\base\Model;

class ResourceLikeForm extends Model
{

    public $sid;
    private $userId;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['sid'], 'required'],
        ];
    }

    public function getId() {
        return Utility::id($this->sid);
    }

    public function like($status) {
        $this->userId = \Yii::$app->user->identity->id;
        $resourceLike = ResourceLike::find()->where([
            'resource_id'=>$this->getId(),
            'user_id'=>$this->userId,
        ])->one();
        if (empty($resourceLike)) {
            $resourceLike = new ResourceLike();
            $resourceLike->setAttributes([
                'status'=>$status,
                'resource_id'=>$this->getId(),
                'user_id'=>$this->userId,
                'time'=>time(),
            ]);
            $resourceCount = $resourceLike->resourceCount;
            if (empty($resourceCount)) {
                $resourceCount = new ResourceCount();
                $resourceCount->setAttributes([
                        'resource_id'=>$this->getId(),
                        'resource_hits'=>0,
                        'resource_hits_daily'=>0,
                        'resource_hits_week'=>0,
                        'resource_post'=>0,
                        'resource_post_daily'=>0,
                        'resource_post_week'=>0,
                        'resource_pre_dig'=>0,
                        'resource_dig'=>0,
                        'resource_bury'=>0,
                        'resource_collect'=>0,
                        'resource_share'=>0,
                        'resource_share_click'=>0,
                ], false);
                if (!$resourceCount->save()){
                    $this->addErrors($resourceLike->getErrors());
                    return false;
                }
            }
            if ($resourceCount) {
                if ($status == ResourceLike::STATUS_LIKE) {
                    $resourceCount->updateCounters(['resource_dig' => 1]);
                } else if ($status == ResourceLike::STATUS_LIKE) {
                    $resourceCount->updateCounters(['resource_bury' => 1]);
                }
            }
            if (!$resourceLike->save()) {
                $this->addErrors($resourceLike->getErrors());
                return false;
            }
        } else {
            $resourceLike->status = $status;
            if (!$resourceLike->save()) {
                $this->addErrors($resourceLike->getErrors());
                return false;
            }
        }

        return true;
    }

}