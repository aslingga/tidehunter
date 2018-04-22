<?php
namespace app\models;

use yii\base\Model;

class UserForm extends Model
{
    public $id;
    public $username;
    public $galleryType;
    public $image1;
    public $image2;
    
    public function rules() {
        return [
            [['username', 'galleryType', 'image1'], 'required'],
            [['image1', 'image2'], 'file', 'skipOnEmpty' => false, 'extensions' => 'png, jpg'],
            [['id'], 'safe']
        ];
    }
}