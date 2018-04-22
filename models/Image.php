<?php

namespace app\models;

use Yii;
use yii\web\UploadedFile;

/**
 * This is the model class for table "image".
 *
 * @property int $id
 * @property int $galleryId
 * @property string $fileName
 *
 * @property Gallery $gallery
 */
class Image extends \yii\db\ActiveRecord
{
    public $imageFile;
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'image';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['galleryId', 'fileName'], 'required'],
            [['galleryId'], 'integer'],
            [['fileName'], 'string', 'max' => 255],
            [['galleryId'], 'exist', 'skipOnError' => true, 'targetClass' => Gallery::className(), 'targetAttribute' => ['galleryId' => 'id']],
            [['imageFile'], 'file'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'galleryId' => 'Gallery ID',
            'fileName' => 'File Name',
        ];
    }
    
    public function upload()
    {
        $this->fileName = uniqid('image_') . '-'. date('Ydmhisa', time()) . '.' . $this->imageFile->extension;
        
        if ($this->validate()) {
            $this->imageFile->saveAs('uploads/' . $this->fileName);
            return true;
        } else {
            return false;
        }
    }
    
    public function getImageLink() {
        return 'uploads/' . $this->fileName;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGallery()
    {
        return $this->hasOne(Gallery::className(), ['id' => 'galleryId']);
    }
}
