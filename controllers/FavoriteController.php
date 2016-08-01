<?php
/**
 * Created by PhpStorm.
 * User: cx
 * Date: 2016/5/10
 * Time: 16:57
 */

namespace qsyk\controllers;

use qsyk\models\Resource;
use qsyk\models\ResourceFavorite;
use qsyk\models\v2\FavImportForm;
use qsyk\models\v2\UserImportForm;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\ContentNegotiator;
use yii\rest\Controller;
use yii\web\Response;

class FavoriteController extends Controller
{

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::className(),
            'only' => ['index', 'view', 'import-like', 'import-fav'],
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
            'query' => ResourceFavorite::find()
                ->where(['user_id'=>$user->id])
            ->andWhere(['>', 'resource_id', 0])
            ->orderBy('time desc')
        ]);
    }

    public function actionView($id)
    {
        return ResourceFavorite::findOne($id);
    }

    public function actionImportFav()
    {
        $model = new UserImportForm();
        if ($model->load(Yii::$app->getRequest()->get(), '') && $model->importFav()) {
            return ["status"=>0, "message"=>""];
        } else {
            return ["status"=>1, "message"=>implode(",", $model->getFirstErrors())];
        }
    }

    public function actionImportLike()
    {
        $model = new UserImportForm();
        if ($model->load(Yii::$app->getRequest()->get(), '') && $model->importLike()) {
            return ["status"=>0, "message"=>""];
        } else {
            return ["status"=>1, "message"=>implode(",", $model->getFirstErrors())];
        }
    }



}