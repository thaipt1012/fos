<?php

class HomeController extends Controller 
{
    public function actionIndex()
    {
        $polls = Poll::model()->findAll();
        $this->render('index', array('polls' => $polls));
    }
    
}
?>
