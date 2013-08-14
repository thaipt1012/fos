<?php
/**
 * author Vu Dang Tung
 */
class CommentController extends Controller
{
    public function actions()
    {
        return array(
            array(
                'allow',
                'actions' => array('create, addreply', 'loadcomment', 'getreplycomments'),
                'users' => array('@'),
            ),);
    }

	public function actionCreate()
	{
		if (!Yii::app()->request->isAjaxRequest) {
            $this->render('/site/error', array('code' => 403, 'message' => 'Forbidden'));
            Yii::app()->end();
        }
        $comment = new Comment;
        if (isset($_POST['comment_data']) ) {
            $comment->attributes = $_POST['comment_data'];
            $comment->content = CHtml::encode($comment->content);
            $comment->user_id = $this->current_user->id;
            $poll = Poll::model()->findbyPk($_POST['comment_data']['poll_id']);
            $cancomment = $this->current_user->canViewPoll($poll);
            $user = User::model()->findbyPk($this->current_user->id);
            if ($cancomment && $comment->save()) {
              echo json_encode($comment->getData());
            } else {
                echo header('HTTP/1.1 404 Method Not Allowed');
            }
        } else {
            echo header('HTTP/1.1 405 Method Not Allowed');
        }
	}

    public function actionAddreply() {
        if (!Yii::app()->request->isAjaxRequest) {
            $this->render('/site/error', array('code' => 403, 'message' => 'Forbidden'));
            Yii::app()->end();
        }
        $comment = new Comment;
        if (isset($_POST['comment_data']) ) {
            $parent_comment = Comment::model()->findByPk($_POST['comment_data']['parent_id']);
            if ($parent_comment && $parent_comment->canBeReplied()) {
                $comment->attributes = $_POST['comment_data'];
                $comment->content = CHtml::encode($comment->content);
                $comment->user_id = $this->current_user->id;
                $poll = Poll::model()->findbyPk($_POST['comment_data']['poll_id']);
                $cancomment = $this->current_user->canViewPoll($poll);
                $user = User::model()->findbyPk($this->current_user->id);
                if ($cancomment && $comment->save()) {
                  echo json_encode($comment->getData());
                } else {
                    echo header('HTTP/1.1 405 Method Not Allowed');
                }
            } else {
                echo header('HTTP/1.1 405 Method Not Allowed');
            }
        } else {
            echo header('HTTP/1.1 405 Method Not Allowed');
        }
    }


    public function actionLoadcomment() {
        if (isset($_POST['current_comment'])) {
            $criteria = new CDbCriteria();
            $poll = Poll::model()->findByPk($_POST['current_poll']);
            $comments = $poll->loadComment($_POST['current_comment']);
            $data = array();
            foreach ($comments as $comment) {
                $data[] = $comment->getData();
            }
            echo json_encode($data);
        } else {
            echo header('HTTP/1.1 405 Method Not Allowed');
        }
    }

    public function loadModel($id)
    {
        $comment = Comment::model()->findByPk($id);
        if ($comment === null)
        {
            throw new CHttpException(404,'The requested page does not exist.');
        }
        return $comment;
    }

    public function actionGetReplyComments(){
        $children_json = array();
        if (isset($_POST['comment_id'])) {
            $comment = $this->loadModel($_POST['comment_id']);
            foreach ($comment->children as $children) {
                $children_json[] = $children->getData();
            }
        }
        echo json_encode($children_json);
    }
}