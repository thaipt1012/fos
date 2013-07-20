<?php $this->widget('bootstrap.widgets.TbAlert'); ?>

<?php
    /**
     * @author Pham Tri Thai
     * View home page
     */
?>
<div class="home-content">
    <?php
    for ($i = 0; $i < sizeof($polls); $i++) {
        echo '<div class="cell_poll">';
        echo CHtml::link($polls[$i]->user->username, '', array(
            'class' => 'user_poll')
        );
        echo $polls[$i]->question;
        echo '<br>';

        $choices = $polls[$i]->choices;

        $total_votes = 0;
        for ($j = 0; $j < sizeof($choices); $j++) {
            $votes = $choices[$j]->votes;
            $total_votes += sizeof($votes);
        }

        for ($j = 0; $j < sizeof($choices); $j++) {
            if ($polls[$i]->is_multichoice == 1) {
                echo CHtml::checkBox($choices[$j]->id, false, array(
                    'id' => $choices[$j]->id,
                    'class' => 'cb')
                );
            } else {
                echo CHtml::radioButton($polls[$i]->id, false, array(
                    'value' => $choices[$j]->id,
                    'id' => $choices[$j]->id,
                    'class' => 'cb')
                );
            }

            $votes = $choices[$j]->votes;
            if ($total_votes !== 0) {
                $percent = sizeof($votes) * 100 / $total_votes;
            } else {
                $percent = 0;
            }

            echo '<div class="progress progress-striped active bar_choice">';
            echo '<div class="bar bar-warning" style="width: ' . $percent . '%;"></div>';
            echo CHtml::label($choices[$j]->content, $choices[$j]->id, 
                array(
                    'class' => 'content_choice'
                ));
            echo '</div>';

            echo sizeof($votes);
            for ($k = 0; $k < sizeof($votes); $k++) {
                echo CHtml::link($votes[$k]->user->username, '', array(
                    'class' => 'user_vote')
                );
            }
            echo "<div class='clear2'></div>";
        }


        if ($polls[$i]->is_multichoice == 1) {
            echo CHtml::checkBox('', false, array(
                'id' => 'max_id_choice + 1', 
                'class' => 'cb')
            );
        } else {
            echo CHtml::radioButton($polls[$i]->id, false, array(
                'value' => 'max_id_choice + 1',
                'id' => 'max_id_choice + 1',
                'class' => 'cb')
            );
        }

        echo '<div class="progress progress-striped active bar_choice">';
        echo '<div class="bar bar-warning" style="width: 0%;"></div>';
        echo CHtml::textArea('new_choice', '', array(
            'placeholder' => 'type new choice...',
            'class' => 'type_new_choice')
        );
        echo '</div>';
        echo "<div class='clear2'></div>";


        $comments = $polls[$i]->comments;
        for ($j = 0; $j < sizeof($comments); $j++) {
            if (!$comments[$j]->parent_id) {
                echo '<div class="comment">';
                echo CHtml::link($comments[$j]->user->username, '', array(
                    'class' => 'user_comment')
                );
                echo $comments[$j]->content;
                echo '<br>';
                echo CHtml::label($comments[$j]->updated_at, '',
                    array('class' => 'time_update'));
                echo CHtml::link('Comment', '',
                    array('class' => 'btn_comment'));
                echo "<div class='clear2'></div>";
                echo '</div>';
                $childrens = $comments[$j]->children;
                for ($k = 0; $k < sizeof($childrens); $k++) {
                    echo '<div class="comment_children">';
                    echo CHtml::link($childrens[$j]->user->username, '',
                        array('class' => 'user_comment'));
                    echo $childrens[$j]->content;
                    echo '<br>';
                    echo CHtml::label($childrens[$j]->updated_at, '',
                        array('class' => 'time_update'));
                    echo "<div class='clear2'></div>";
                    echo '</div>';
                }
            }
        }
        echo "<div class='clear2'></div>";
        echo CHtml::textArea('your_comment', '', array(
            'placeholder' => 'type your comment...',
            'rows' => 1,)
         );
        //echo CHtml::inputFeild('text-area', '', array('placeholder' => 'type your comment...'));
        echo '<hr>';
        echo '</div>';
    };
    ?>
</div>

<script>
    $().ready(function() {
        $('textarea').autosize();
    });
</script>