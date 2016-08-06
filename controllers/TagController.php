<?php
/**
 * Created by PhpStorm.
 * User: cx
 * Date: 2016/6/13
 * Time: 13:06
 */

namespace qsyk\controllers;

use qsyk\commands\DataController;
use qsyk\models\Resource;
use qsyk\models\ResourceFavoriteForm;
use qsyk\models\ResourceLike;
use qsyk\models\ResourceLikeForm;
use qsyk\models\ResourceReportForm;
use qsyk\models\Tag;
use qsyk\models\TagFollowForm;
use qsyk\models\TagRel;
use qsyk\models\UserTag;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\ContentNegotiator;
use yii\helpers\ArrayHelper;
use yii\rest\Controller;
use yii\web\Response;
use common\components\Utility;

class TagController extends Controller
{

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::className(),
            'only' => ['follow', 'unfollow', 'user-tags'],
        ];

        return $behaviors;
    }

    public function actionIndex()
    {
        return new ActiveDataProvider([
            'query' => Tag::find()->orderBy('`id` desc'),
        ]);
    }

    public function actionView($sid)
    {
        return Tag::findOne(Utility::id($sid));
    }

    public function actionGroup()
    {
        return [
            'top'=>Tag::tagToArray(Tag::find()->where(['app_top_show'=>1])->all()),
            'focus'=>Tag::tagToArray(Tag::find()->where(['id' => Tag::getUserTagIds()])->all())
        ];
    }

    public function actionUserTags()
    {
        return new ActiveDataProvider([
            'query' => Tag::find()->where(['id'=>Tag::getUserTagIds()]),
        ]);
    }

    public function actionFollow() {
        $likeForm = new TagFollowForm();
        if ($likeForm->load(Yii::$app->getRequest()->post(), '') && $likeForm->follow()) {
            return ["status"=>0, "message"=>""];
        }
        return ["status"=>1, "message"=>implode(",", $likeForm->getFirstErrors())];
    }

    public function actionUnfollow() {
        $likeForm = new TagFollowForm();
        if ($likeForm->load(Yii::$app->getRequest()->post(), '') && $likeForm->unFollow()) {
            return ["status"=>0, "message"=>""];
        }
        return ["status"=>1, "message"=>implode(",", $likeForm->getFirstErrors())];
    }
}