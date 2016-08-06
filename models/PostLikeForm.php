<?php
/**
 * Created by PhpStorm.
 * User: cx
 * Date: 2016/5/25
 * Time: 9:52
 */

namespace qsyk\models;


use common\components\Utility;
use yii\base\Model;

class PostLikeForm extends Model
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
    
    public static function getDb()
    {
        return Yii::$app->get('qsykDb');
    }

    public function getId() {
        return Utility::id($this->sid);
    }

    public function like()
    {
        $this->userId = \Yii::$app->user->identity->id;
        $post = ResourcePost::find()->where([
            'resourceid' => $this->getId(),
            'user' => $this->userId,
        ])->one();
        if ($post) {
            if (!$post->updateCounters(['dig' => 1])) {
                $this->addErrors($post->getErrors());
                return false;
            }
        }

        return true;
    }
}
