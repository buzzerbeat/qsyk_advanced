<?php
/**
 * Created by PhpStorm.
 * User: cx
 * Date: 2016/5/6
 * Time: 11:42
 */

namespace qsyk\controllers;

use qsyk\commands\DataController;
use common\components\Utility;
use qsyk\models\Resource;
use qsyk\models\ResourceFavoriteForm;
use qsyk\models\ResourceLike;
use qsyk\models\ResourceLikeForm;
use qsyk\models\ResourceReportForm;
use qsyk\models\ResourceVerifyForm;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\ContentNegotiator;
use yii\rest\Controller;
use yii\web\Response;
use qsyk\models\RandomCache;

class ResourceController extends Controller
{

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::className(),
            'only' => ['like', 'hate', 'fav', 'report', 'verify'],
        ];
        $behaviors['contentNegotiator'] = [
            'class' => ContentNegotiator::className(),
            'formats' => [
                'application/json' => Response::FORMAT_JSON,
            ],
        ];
        $behaviors[] = [
            'class' => 'yii\filters\HttpCache',
            'only' => ['index'],
            'lastModified' => function ($action, $params) {
                return RandomCache::find()->max('updated_at');
                $q = new \yii\db\Query();
                return $q->from('random_cache')->max('updated_at');
            },
        ];

        $behaviors[] = [
            'class' => 'yii\filters\HttpCache',
            'only' => ['new'],
            'lastModified' => function ($action, $params) {
                $q = new \yii\db\Query();
                return $q->from('resource')->max('pub_time');
            },
        ];

        return $behaviors;
    }

    public function actionLike()
    {
        $likeForm = new ResourceLikeForm();
        if ($likeForm->load(Yii::$app->getRequest()->post(), '') && $likeForm->like(ResourceLike::STATUS_LIKE)) {
            return ["status"=>0, "message"=>""];
        }
        return ["status"=>1, "message"=>implode(",", $likeForm->getFirstErrors())];
    }

    public function actionHate()
    {
        $likeForm = new ResourceLikeForm();
        if ($likeForm->load(Yii::$app->getRequest()->post(), '') && $likeForm->like(ResourceLike::STATUS_HATE)) {
            return ["status"=>0, "message"=>""];
        }
        return ["status"=>1, "message"=>implode(",", $likeForm->getFirstErrors())];
    }


    public function actionFav()
    {
        $favForm = new ResourceFavoriteForm();
        if ($favForm->load(Yii::$app->getRequest()->post(), '') && $favForm->fav()) {
            return ["status"=>0, "message"=>""];
        }
        return ["status"=>1, "message"=>implode(",", $favForm->getFirstErrors())];
    }

    public function actionReport()
    {
        $reportForm = new ResourceReportForm();
        if ($reportForm->load(Yii::$app->getRequest()->post(), '') && $reportForm->report()) {
            return ["status"=>0, "message"=>""];
        }
        return ["status"=>1, "message"=>implode(",", $reportForm->getFirstErrors())];
    }

    public function actionVerify()
    {
        $reportForm = new ResourceVerifyForm();
        if ($reportForm->load(Yii::$app->getRequest()->post(), '') && $reportForm->verify()) {
            return ["status"=>0, "message"=>""];
        }
        return ["status"=>1, "message"=>implode(",", $reportForm->getFirstErrors())];
    }



    public function actionNew()
    {
        return new ActiveDataProvider([
            'query' => Resource::find()
                ->where(['!=','status',Resource::STATUS_DELETE])
                ->orderBy('id desc')
        ]);
    }
    public function actionIndex()
    {

		$request = Yii::$app->request;
		$type = $request->get('type', 0);
		$sid = $request->get('sid', "");
        $where = [
            'status'=>Resource::STATUS_ACTIVE,
        ];
        $order = 'id desc';
        if (!empty($sid)) {
            $idArr = [];
            $sidArr = explode(',', $sid);
            foreach($sidArr as $oneSid) {
                $oneId = Utility::id($oneSid);
                if(!empty($oneId)) {
                    $idArr[] = $oneId;
                }
            }
            if (!empty($idArr)) {
                $where["id"] = $idArr;
                $expression = new Expression('FIELD (id, ' . implode(',', $idArr) . ')');
                $order = [$expression];
            }
        }
        if (!isset($where["id"])) {
            $where['`random_cache`.`category`'] = DataController::CATEGORY_INDEX + $type * 100;
            $order = '`random_cache`.`index` asc';
        }

		$queryBuilder = Resource::find()
            ->leftJoin('random_cache', '`random_cache`.`resource_id` = `resource`.`id`')
            ->where($where)
            ->orderBy($order);

        return new ActiveDataProvider([
            'query' => $queryBuilder 
        ]);
    }


    public function actionView($sid)
    {
        return Resource::findOne(Utility::id($sid));
    }
}
