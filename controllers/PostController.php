<?php
/**
 * Created by PhpStorm.
 * User: cx
 * Date: 2016/5/25
 * Time: 9:47
 */

namespace qsyk\controllers;

use qsyk\models\PostLikeForm;
use qsyk\models\Resource;
use qsyk\models\PostForm;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\ContentNegotiator;
use yii\rest\Controller;
use yii\web\Response;
use common\components\Utility;

class PostController extends Controller
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::className(),
            'only' => ['like', 'send'],
        ];
        $behaviors['contentNegotiator'] = [
            'class' => ContentNegotiator::className(),
            'formats' => [
                'application/json' => Response::FORMAT_JSON,
            ],
        ];

        return $behaviors;
    }


    public function actionLike()
    {
        $likeForm = new PostLikeForm();
        if ($likeForm->load(Yii::$app->getRequest()->post(), '') && $likeForm->like()) {
            return ["status"=>0, "message"=>""];
        }
        return ["status"=>1, "message"=>implode(",", $likeForm->getFirstErrors())];
    }

    public function actionSend()
    {
        $sendForm = new PostForm();
        if ($sendForm->load(Yii::$app->getRequest()->post(), '') && $sendForm->send()) {
            return ["status"=>0, "message"=>"", "data"=>$sendForm->retPost];
        }
        return ["status"=>1, "message"=>implode(",", $sendForm->getFirstErrors())];
    }
}