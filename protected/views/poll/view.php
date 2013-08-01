<script src='<?php echo Yii::app()->baseUrl; ?>/js/comment.js'></script> 

<?php
Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl.'/css/jquery.countdown.css');
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl.'/js/jquery.countdown.js');
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl.'/js/countdown.js');
$this->widget('bootstrap.widgets.TbAlert');
?>
<h1>Poll Detail </h1>
<div class="row">
    <div id="countdown"></div>
    <h4 id="note" style="text-align: center"></h4>
</div>
<script>
    var start = <?php echo strtotime($poll->start_at) ?>;
    var end = <?php echo strtotime($poll->end_at) ?>;
    countDown(start * 1000, end * 1000);

    $(function() {
        $(".invite").click(function() {
            window.open('index.php?r=poll/addinvite&poll_id=<?php echo $poll->id ?>', 'mywindow', 'width=600,height=400');
        });
        $('.hidden_info').hide();
        $('.invited').hide();
        $('.view_invited').click(function(){            
            $('.invited').toggle(500);
        });
        $('.more').click(function(){
            $('.invited').hide();
            $('.hidden_info').toggle(500);
        });
        $('.btn-primary').click(function(){
            var id = "#name_vote_"+$(this).attr('show_vote_');
            $(id).slideToggle();
        });

    });

</script>

<br/>
<div class="row">
<?php
if (Yii::app()->user->is_admin) {
    echo CHtml::button('Delete Poll', array(
        'class' => 'btn btn-danger',
        'submit' => array(
            'poll/delete',
            'id' => $poll->id),
        'confirm' => 'Do you want to delete this poll ?')
    );
}
echo '</span>&nbsp';
if (Yii::app()->user->getId() == $user->id) {
    echo CHtml::button('Edit Poll', array(
        'class' => 'btn btn-warning',
        'submit' => array(
            'poll/update',
            'id' => $poll->id)
        )
    );
}
echo '</span>&nbsp';
if (Yii::app()->user->getId() === $user->id && !$poll->hasEnded()) {
    echo CHtml::button('Edit Choice', array(
        'class' => 'btn btn-warning',
        'submit' => array(
            'choice/index',
            'poll_id' => $poll->id)
        )
    );
}


if ($poll->display_type == Poll::POLL_DISPLAY_SETTINGS_INVITED_ONLY && Yii::app()->user->getId() == $user->id) {
    echo '</span>&nbsp';
    echo CHtml::button(
        'Invite More',
         array('class' => 'btn btn-info invite')
    );
} elseif ($poll->display_type == POLL::POLL_DISPLAY_SETTINGS_RESTRICTED && Yii::app()->user->getId() == $user->id) {
    echo '</span>&nbsp';
    echo Chtml::button(
            'Invite More People',
            array('class' => 'btn btn-info invite')
    );
}
?>

<?php echo CHtml::button('Show More', array('class' => 'more btn btn-primary')); ?>
</div>

<table class='detail-view table table-striped table-condensed' id='yw1'>
    <tbody>        
        <tr class='odd hidden_info'>
            <th>User</th>
            <td><?php echo $user->username ?></td>
        </tr>
        <tr class='even hidden_info'>
            <th>Setting</th>
            <td>
                <b> Multi-choice :</b>
                <?php
                if ($poll->is_multichoice == POll::IS_MULTICHOICES_NO) {
                    echo ' No ';
                } else {
                    echo ' Yes ';
                }
                ?>
            </td>
        </tr>
        <tr class='even hidden_info'>
            <th></th>
            <td>
                <b> Poll Type :</b>
                <?php
                if ($poll->poll_type == Poll::POLL_TYPE_SETTINGS_ANONYMOUS) {
                    echo ' Anonymous (Owner can\'t view and public voter name) ';
                } else {
                    echo ' Non-Anonymous (Owner can view and public voter name) ';
                }
                ?>
            </td>
        </tr>
        <tr class='even hidden_info'>
            <th></th>
            <td>
                <b>Poll Display :</b>
                <?php
                switch ($poll->display_type) {
                    case Poll::POLL_DISPLAY_SETTINGS_PUBLIC:
                        echo ' Public (All user can see and vote) ';
                        break;
                    case Poll::POLL_DISPLAY_SETTINGS_RESTRICTED:
                        echo ' Restricted (All user can see but only invited user can vote) ';
                        if ($poll->poll_type == Poll::POLL_TYPE_SETTINGS_NON_ANONYMOUS) {
                            echo CHtml::button('view invited', array(
                                'class' => 'view_invited btn btn-primary btn-mini',
                            ));
                        }
                        break;
                    default:
                        echo ' Invited Only (Only invited user can see and vote) ';
                        if ($poll->poll_type == Poll::POLL_TYPE_SETTINGS_NON_ANONYMOUS || $poll->user_id == $this->current_user->id) {
                            echo CHtml::button('view invited', array(
                                'class' => 'view_invited btn btn-primary btn-mini',
                            ));
                        }
                        break;
                }
                ?>
            </td>
        </tr>
        <tr class="invited">
            <th></th>
            <td>
            <?php
                foreach($users_invited as $user) {
                    echo $user->profile->createViewlink();
                    echo '&nbsp;&nbsp;&nbsp;';
                }
            ?>
            </td>
        </tr>
        <tr class='odd hidden_info'>
            <th></th>
            <td>
                <b>Result Display :</b>
                <?php
                switch ($poll->result_display_type) {
                    case Poll::RESULT_DISPLAY_SETTINGS_PUBLIC:
                        echo 'Pulic (All user who can access can see result) ';
                        break;
                    case Poll::RESULT_DISPLAY_SETTINGS_VOTED_ONLY:
                        echo 'Voted Only (Only voted user can see result) ';
                        break;
                    default:
                        echo 'Owner Only (Only owner can see result) ';
                        break;
                }
                ?>
            </td>
        </tr>
        <tr class='even hidden_info'>
            <th></th>
            <td>
                <b>Result Details :</b>
                <?php
                if ($poll->result_detail_type == Poll::RESULT_DETAIL_SETTINGS_ALL) {
                    echo ' All (All result include who voted) ';
                } else {
                    echo ' Only Percentage (Show only percentage of each choice) ';
                }
                ?>
            </td>
        </tr>
        <tr class='odd hidden_info'>
            <th></th>
            <td>
                <b>Result Show Time :</b>
                <?php
                if ($poll->result_show_time_type == Poll::RESULT_TIME_SETTINGS_AFTER) {
                    echo ' After vote finish (Show result only after poll expired) ';
                } else {
                    echo ' During (Show result during votting time) ';
                }
                ?>
            </td>
        </tr>
        <tr class='odd hidden_info'>
            <th></th>
            <td>
                <b>Start at:</b>
                <?php echo $poll->start_at; ?>
            </td>
        </tr>
        <tr class='odd hidden_info'>
            <th></th>
            <td>
                <b>End at :</b>
                <?php echo $poll->end_at; ?>
            </td>
        </tr>
        <tr class='even'>
            <th>Question</th>
            <td><?php echo $poll->question ?></td>
        </tr>
        <tr class='odd'>
            <th>Description</th>
            <td><?php echo $poll->description ?></td>
        </tr>
        </tbody>
    </table>
    <form method="post" action="<?php echo Yii::app()->createUrl('poll/vote', array('id' => $poll->id));?>">
        <?php
        /**
        * @author Pham Tri Thai
        * View result vote
        */
        
        $total_votes = 0;
        foreach ($choices as $choice) {
            $votes = $choice->votes;
            $total_votes += sizeof($votes);
        }

        foreach ($choices as $choice) {
            if ($poll->is_multichoice == 1) {
                echo CHtml::checkBox('choice['.$choice->id.']', false, array(
                    'id' => $choice->id,
                    'class' => 'cb',
                    'disabled' => !($can_votes && $voting)
                    )
                );
            } else {
                echo CHtml::radioButton('choice', false, array(
                    'value' => $choice->id,
                    'id' => $choice->id,
                    'class' => 'cb',
                    'disabled' => !($can_votes && $voting)
                    )
                );
            }
            
            $votes = $choice->votes;
            if ($total_votes !== 0) {
                $percent = sizeof($votes) * 100 / $total_votes;
            } else {
                $percent = 0;
            }
            echo '<div class="progress progress-striped active bar_choice">';
            if ($can_show_result) {
                echo '<div class="bar bar-warning" style="width: ' . $percent . '%;"></div>';
            }
            echo CHtml::label($choice->content, $choice->id, 
                array(
                    'class' => 'content_choice'
                ));
            echo '</div>';

            if ($can_show_result) {
                echo sizeof($votes);
                if ($can_show_voter) {
                    echo CHtml::button('Show Voter', array(
                       'class' => 'btn btn-primary',
                       'show_vote_' => $choice->id,
                       )
                    );
                    echo "<div class='voter_area' id = 'name_vote_$choice->id'>";
                    for ($k = 0; $k < sizeof($votes); $k++) {
                        $user_link = $votes[$k]->user->profile->createViewLink(null, array(
                            'class' => 'user_vote',
                        ));
                        echo $user_link;
                        echo " ";
                    }
                    echo '</div>';
                }
            }
            echo "<div class='clear2'></div>";
        }

        if (empty($all_votes)) {
            echo CHtml::button(
                'Vote', 
                array(
                    'class' => 'btn btn-primary',
                    'type' => 'submit',
                    'disabled' => ! $voting,
                )
            );
        } else {
            foreach ($all_votes as $vote) {
                echo "You voted with {$vote->choice->content}<br>";
            }
            echo CHtml::button(
                'Re-Vote', 
                array(
                    'class' => 'btn btn-primary',
                    'type' => 'submit',
                    'disabled' => ! $voting,
                )
            );
        }
    ?>
</form>

<?php
    
    echo '<div class="comment_area">';
        for ($j = 0; $j < sizeof($comments); $j++) {
            if (!$comments[$j]->parent_id) {
                echo "<div class='comment'>";
                echo "<div class='user_comment'>{$comments[$j]->user->profile->createViewlink()}</div>";
                echo $comments[$j]->content;
                echo '<br>';
                echo CHtml::label($comments[$j]->updated_at, '',
                    array('class' => 'time_update'));
                echo CHtml::link('Reply', '',
                    array('class' => 'reply_comment', 'comment_id' => $comments[$j]->id) );
                echo "<div class='clear2'></div>";
                echo '</div>';
                echo "<div class= 'children_{$comments[$j]->id}'>";
                $childrens = $comments[$j]->children;
                for ($k = 0; $k < sizeof($childrens); $k++) {
                    echo '<div class="comment_children">';
                    echo "<div class='user_comment'>{$childrens[$k]->user->profile->createViewlink()}</div>";
                    echo $childrens[$k]->content;
                    echo '<br>';
                    echo CHtml::label($childrens[$k]->updated_at, '',
                        array('class' => 'time_update'));
                    echo "<div class='clear2'></div>";
                    echo '</div>';
                }
                echo '</div>';
                echo "<div class='row'>";
                echo "<textarea class='span8 offset1 children_comment_textarea id_{$comments[$j]->id}'
                      placeholder='' rows='1' data-poll-id= '{$poll->id}'
                      parent_comment= '{$comments[$j]->id}'  wrap='off' style='overflow:hidden '>
                      </textarea>";
                echo "</div>";
            }
        }
        echo "<div class='clear2'></div>";        
        echo '</div>';
    echo '</div>';
?>
<div class="row">
    <div class="a-comment span7">
        <textarea class ="span12 comment-textarea" 
            placeholder="Write a comment..." rows="2" data-poll-id= "<?php echo $poll->id; ?>"
            id="comment-all" wrap="off" style="overflow:hidden "></textarea>
    </div>
</div>
