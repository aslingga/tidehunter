<?php

namespace app\controllers;

use Yii;
use app\models\User;
use app\models\UserForm;
use app\models\UserSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\Gallery;
use app\models\Image;
use yii\web\UploadedFile;
use Codeception\Lib\Connector\Yii2;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single User model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new UserForm();

        if ($model->load(Yii::$app->request->post())) {
            $model->image1 = UploadedFile::getInstance($model, 'image1');
            $model->image2 = UploadedFile::getInstance($model, 'image2');
            
            if ($model->validate()) {
                $connection = \Yii::$app->db;
                $transaction = $connection->beginTransaction(); 
                
                try {
                    // create a new user
                    $user = new User();
                    $user->username = $model->username;
                    $user->save();
                    
                    // create a new gallery
                    $gallery = new Gallery();
                    $gallery->userId = $user->id;
                    $gallery->type = $model->galleryType;
                    $gallery->save();
                    
                    // create the 1st image
                    $image1 = new Image();
                    $image1->galleryId = $gallery->id;
                    $image1->imageFile = UploadedFile::getInstance($model, 'image1');
                    $image1->upload();
                    $image1->save();
                    
                    // create the 2nd image
                    $image2 = new Image();
                    $image2->galleryId = $gallery->id;
                    $image2->imageFile = UploadedFile::getInstance($model, 'image2');
                    $image2->upload();
                    $image2->save();
                    
                    $transaction->commit();
                } 
                catch (\Exception $e) {
                    $transaction->rollBack();
                    throw $e;
                } 
                catch (\Throwable $e) {
                    $transaction->rollBack();
                    throw $e;
                }
                
                return $this->redirect(['view', 'id' => $user->id]);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $user = $this->findModel($id);
        $model = new UserForm();
        $model->id = $user->id;
        $model->username = $user->username;
        $model->galleryType = $user->gallery->type;

        if ($model->load(Yii::$app->request->post())) {
            $model->image1 = UploadedFile::getInstance($model, 'image1');
            $model->image2 = UploadedFile::getInstance($model, 'image2');
            
            if ($model->validate()) {
                $connection = \Yii::$app->db;
                $transaction = $connection->beginTransaction();
                
                try {
                    // create a new user
                    $user->username = $model->username;
                    $user->update();
                    
                    // create a new gallery
                    $gallery = $user->gallery;
                    $gallery->type = $model->galleryType;
                    $gallery->update();
                    
                    $i = 1;
                    foreach ($user->gallery->images as $row) {
                        $oldFileName = $row->fileName;
                        
                        $row->imageFile = UploadedFile::getInstance($model, 'image' . $i);
                        $row->upload();
                        $row->update();
                        
                        unlink(getcwd() . DIRECTORY_SEPARATOR . 'uploads' .DIRECTORY_SEPARATOR . $oldFileName);
                        
                        $i++;
                    }
                    
                    $transaction->commit();
                }
                catch (\Exception $e) {
                    $transaction->rollBack();
                    throw $e;
                }
                catch (\Throwable $e) {
                    $transaction->rollBack();
                    throw $e;
                }
                
                return $this->redirect(['view', 'id' => $user->id]);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $connection = \Yii::$app->db;
        $transaction = $connection->beginTransaction();
        
        try {
            Yii::setAlias('@root', realpath(dirname(__FILE__).'/../../'));
            
            $model = $this->findModel($id);
            foreach ($model->gallery->images as $row) {
                unlink(getcwd() . DIRECTORY_SEPARATOR . 'uploads' .DIRECTORY_SEPARATOR . $row->fileName);
            }
            
            $model->delete();
            
            $transaction->commit();
        }
        catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
        catch (\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }

        return $this->redirect(['index']);
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
