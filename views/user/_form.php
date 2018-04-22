<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\User */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-form">
    <div class="row">
    	<div class="col-lg-5">
            <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
        
            <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>
        
            <?= $form->field($model, 'galleryType')->dropDownList(['Art' => 'Art', 'Fiction' => 'Fiction', 'Scary' => 'Scary', 'Natural' => 'Natural'], ['prompt' => '-- Please select --']) ?>
        
            <?= $form->field($model, 'image1')->fileInput() ?>
        
            <?= $form->field($model, 'image2')->fileInput() ?>
        
            <div class="form-group">
                <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
            </div>
            
            <?php ActiveForm::end(); ?>
    	</div>
        <div class="col-lg-7">
        </div>
    </div>
</div>
