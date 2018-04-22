<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\User */

$this->title = 'User: #' .$model->id;
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->registerCss('
     .user-image {
        margin:10px;
        height:100%;
        max-height:150px;
     }
');
?>
<div class="user-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'username',
            'gallery.type',
            [
                'label' => 'Images',
                'value' => function ($model) {
                    $imgString = '';
                    foreach ($model->gallery->images as $row) {
                        $imgString .= '<img src="' . $row->imageLink . '" class="user-image">';
                    }
                    
                    return $imgString;
                },
                'format' => 'raw'
            ]
        ],
    ]) ?>

</div>
