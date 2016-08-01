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

class ResourceFavoriteForm extends Model
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

    public function fav() {
        $this->userId = \Yii::$app->user->identity->id;
        $resourceFav = ResourceFavorite::find()->where([
            'resource_id'=>$this->getId(),
            'user_id'=>$this->userId,
        ])->one();
        if (empty($resourceFav)) {
            $resourceFav = new ResourceFavorite();
            $resourceFav->setAttributes([
                'resource_id'=>$this->getId(),
                'user_id'=>$this->userId,
                'time'=>time(),
            ]);
            if (!$resourceFav->save()) {
                $this->addErrors($resourceFav->getErrors());
                return false;
            }
            $resourceCount = $resourceFav->resourceCount;
            if ($resourceCount) {
                $resourceCount->updateCounters(['resource_collect' => 1]);
            }
        }

        return true;
    }

}