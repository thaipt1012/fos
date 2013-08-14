<?php

class ChoiceController extends Controller
{

    /**
     * @return array action filters
     */
    public function filters()
    {
        return array(
            'accessControl', // perform access control for CRUD operations
            'postOnly + delete', // we only allow deletion via POST request
        );
    }

    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules()
    {
        return array(
            array('allow', // allow all users to perform 'index' and 'view' actions
                'actions' => array('index', 'view'),
                'users' => array('*'),
            ),
            array('allow', // allow authenticated user to perform 'create' and 'update' actions
                'actions' => array('create', 'update', 'delete'),
                'users' => array('@'),
            ),
            array('allow', // allow admin user to perform 'admin' and 'delete' actions
                'actions' => array('admin'),
                'users' => array('admin'),
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
    }

    /**
     * Displays a particular model.
     * @param integer $id the ID of the model to be displayed
     */
    public function actionView($id)
    {
        $this->render('view', array(
            'model' => $this->loadModel($id),
        ));
    }

    /**
     * Creates a new model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     */
    public function actionCreate()
    {
        $poll = Poll::model()->findByPk($_POST['poll_id']);
        if ($poll && $this->current_user->id == $poll->user_id && !$poll->hasEnded()) {
            $model = new Choice;
            $criteria = new CDbCriteria();
            // Uncomment the following line if AJAX validation is needed
            // $this->performAjaxValidation($model);
            if (isset($_POST['poll_id']) && isset($_POST['content_choice'])) {
                $criteria->addCondition('poll_id=:poll_id');
                $criteria->params = array(':poll_id' => $_POST['poll_id']);
                $count = Choice::model()->count($criteria);

                $model->poll_id = $_POST['poll_id'];
                $model->content = CHtml::encode($_POST['content_choice']);
                if (!$model->save() || $count > 9) {
                    throw new CHttpException(500, 'Internal Server Error.');
                } else {
                    echo json_encode($model->id);
                }
            }
        } else {
            throw new CHttpException(500, 'Internal Server Error.');
        }
    }

    /**
     * Updates a particular model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id the ID of the model to be updated
     */
    public function actionUpdate($id)
    {
        $model = $this->loadModel($id);

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if (isset($_POST['Choice'])) {
            $model->attributes = $_POST['Choice'];
            if ($model->save())
                $this->redirect(array('view', 'id' => $model->id));
        }

        $this->render('update', array(
            'model' => $model,
        ));
    }

    /**
     * Deletes a particular model.
     * If deletion is successful, the browser will be redirected to the 'admin' page.
     * @param integer $id the ID of the model to be deleted
     */
    public function actionDelete()
    {
        // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
            if (isset($_POST['choice_id'])) {
                $choice = Choice::model()->findByPk($_POST['choice_id']);
                $poll = $choice->poll;
                if (!$poll->hasEnded()) {
                    if ($choice && $this->current_user->id == $choice->poll->user_id) {
                        $votes = $choice->votes;
                        if (empty($votes)) {
                            $choice->delete();
                            echo header('HTTP/1.1 200 OK');
                        } else {
                            CHttpException(500, 'Internal Server Error.');
                        }
                    } else {
                        CHttpException(500, 'Internal Server Error.');
                    }
                }
            } else {
                CHttpException(500, 'Internal Server Error.');
            }
    }

    /**
     * Lists all models.
     */
    public function actionIndex($poll_id)
    {
        $poll = Poll::model()->findByPk($poll_id);
        $choices = Choice::model()->findAllByAttributes(array('poll_id' => $poll_id));
        if (!$poll->hasEnded()) {
            $this->render('index', array(
                'poll' => $poll,
                'choices' => $choices,
            ));
        } else {
            throw new CHttpException(404, 'The requested page does not exist.');
        }
    }

    /**
     * Manages all models.
     */
    public function actionAdmin()
    {
        $model = new Choice('search');
        $model->unsetAttributes();  // clear any default values
        if (isset($_GET['Choice'])) {
            $model->attributes = $_GET['Choice'];
        }
        $this->render('admin', array(
            'model' => $model,
        ));
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer $id the ID of the model to be loaded
     * @return Choice the loaded model
     * @throws CHttpException
     */
    public function loadModel($id)
    {
        $model = Choice::model()->findByPk($id);
        if ($model === null) {
            throw new CHttpException(404, 'The requested page does not exist.');
        }
        return $model;
    }

    /**
     * Performs the AJAX validation.
     * @param Choice $model the model to be validated
     */
    protected function performAjaxValidation($model)
    {
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'choice-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }

}
