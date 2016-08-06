<?php

namespace qsyk\models;

use common\components\Utility;
use Yii;

/**
 * This is the model class for table "resource_video".
 *
 * @property integer $id
 * @property integer $status
 * @property integer $cover_img
 * @property integer $length
 * @property integer $width
 * @property integer $height
 * @property integer $add_time
 * @property integer $pub_time
 * @property integer $last_update_time
 * @property integer $watermark
 * @property string $download
 * @property string $path
 */
class ResourceVideo extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'resource_video';
    }
    
    public static function getDb()
    {
        return Yii::$app->get('qsykDb');
    }

    public function getUrl()
    {
        return $this->download;
    }
    public function getSid()
    {
        return Utility::sid($this->id);
    }
    public function getThumb()
    {
        return Utility::sid($this->cover_img);
    }
    public function fields()
    {
//        $fields = parent::fields();
        $fields = [
            'sid',
            'thumb',
            'width',
            'height',
            'url',
            'length',
        ];
        // remove fields that contain sensitive information
        return $fields;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status', 'cover_img', 'length', 'width', 'height', 'add_time', 'pub_time', 'last_update_time', 'watermark', 'download', 'path'], 'required'],
            [['status', 'cover_img', 'length', 'width', 'height', 'add_time', 'pub_time', 'last_update_time', 'watermark'], 'integer'],
            [['download', 'path'], 'string', 'max' => 2000],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'status' => 'Status',
            'cover_img' => 'Cover Img',
            'length' => 'Length',
            'width' => 'Width',
            'height' => 'Height',
            'add_time' => 'Add Time',
            'pub_time' => 'Pub Time',
            'last_update_time' => 'Last Update Time',
            'watermark' => 'Watermark',
            'download' => 'Download',
            'path' => 'Path',
        ];
    }
}
