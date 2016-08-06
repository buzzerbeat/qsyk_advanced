<?php
/**
 * Created by PhpStorm.
 * User: cx
 * Date: 2016/5/6
 * Time: 15:30
 */

namespace qsyk\models;


use common\components\Utility;
use yii\base\Model;

class ResourceReportForm extends Model
{

    public $sid;
    private $userId;
    public $type;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['sid', 'type'], 'required'],
            [['type'], 'integer'],
        ];
    }

    public function getId() {
        return Utility::id($this->sid);
    }

    public function report() {
        $this->userId = \Yii::$app->user->identity->id;
        $resourceReport = ResourceReport::find()->where([
            'resource_id'=>$this->getId(),
            'user_id'=>$this->userId,
        ])->one();
        if (empty($resourceReport)) {
            $resourceReport = new ResourceReport();
            $resourceReport->setAttributes([
                'resource_id'=>$this->getId(),
                'user_id'=>$this->userId,
                'type'=>$this->type,
                'time'=>time(),
            ]);
            if (!$resourceReport->save()) {
                $this->addErrors($resourceReport->getErrors());
                return false;
            }
        }

        return true;
    }

}
