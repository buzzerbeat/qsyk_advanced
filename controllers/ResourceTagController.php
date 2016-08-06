<?php
/**
 * Created by PhpStorm.
 * User: cx
 * Date: 2016/5/24
 * Time: 15:13
 */

namespace qsyk\controllers;

use common\components\Utility;
use qsyk\models\Resource;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\auth\HttpBearerAuth;
use yii\rest\Controller;

class ResourceTagController extends Controller
{

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors[] = [
            'class' => 'yii\filters\HttpCache',
            'only' => ['index'],
            'lastModified' => function ($action, $params) {
                return Resource::find()->max('pub_time');
            },
        ];

        return $behaviors;
    }
    public function actionIndex()
    {
        $request = Yii::$app->request;
        $tag = $request->get('tag', "");
        $tagId = empty($tag) ? 0 : Utility::id($tag);
        $queryBuilder = Resource::find()
            ->leftJoin('tag_rel', '`resource`.`id` = `tag_rel`.`resource_id`')
            ->where([
                'status'=>Resource::STATUS_ACTIVE,
                'tag_rel.tag_id'=>$tagId,
            ])
            ->orderBy('`pub_time` desc');

        return new ActiveDataProvider([
            'query' => $queryBuilder
        ]);
    }

    public function actionView($sid)
    {
        return Resource::findOne(Utility::id($sid));
    }

}