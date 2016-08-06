<?php
/**
 * Created by PhpStorm.
 * User: cx
 * Date: 2016/5/10
 * Time: 18:00
 */

namespace qsyk\controllers;


use qsyk\models\ResourceLike;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\ContentNegotiator;
use yii\rest\Controller;
use yii\web\Response;

class LikeController  extends Controller
{

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::className(),
            'only' => ['index', 'view'],
        ];
        $behaviors['contentNegotiator'] = [
            'class' => ContentNegotiator::className(),
            'formats' => [
                'application/json' => Response::FORMAT_JSON,
            ],
        ];

        return $behaviors;
    }

    public function actionIndex()
    {
        $user = Yii::$app->user->identity;
        return new ActiveDataProvider([
            'query' => ResourceLike::find()
                ->where(['user_id'=>$user->id,'status'=>1])
                ->andWhere(['>', 'resource_id', 0])
                ->orderBy('time desc')
        ]);
    }

    public function actionView($id)
    {
        return ResourceLike::findOne($id);
    }

}
