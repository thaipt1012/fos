<?php

/**
 * This is the model class for table "polls".
 *
 * The followings are the available columns in table 'polls':
 * @property integer $id
 * @property string $question
 * @property string $description
 * @property integer $user_id
 * @property integer $is_multichoice
 * @property integer $poll_type
 * @property integer $display_type
 * @property integer $result_display_type
 * @property integer $result_detail_type
 * @property integer $result_show_time_type
 * @property string $created_at
 * @property string $updated_at
 * @property string $start_at
 * @property string $end_at
 */
class Poll extends CActiveRecord
{

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return Poll the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'polls';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('user_id, is_multichoice, poll_type, display_type, result_display_type, result_detail_type, result_show_time_type', 'numerical', 'integerOnly' => true),
            array('question', 'length', 'max' => 255),
            array('description, created_at, updated_at, start_at, end_at', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, question, description, user_id, is_multichoice, poll_type, display_type, result_display_type, result_detail_type, result_show_time_type, created_at, updated_at, start_at, end_at', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'user' => array(self::BELONGS_TO, 'User', 'user_id'),
            'choices' => array(self::HAS_MANY, 'Choice', 'poll_id'),
            'comments' => array(self::HAS_MANY, 'Comment', 'poll_id'),
            'invitations' => array(self::HAS_MANY, 'Invitation', 'poll_id'),
            'notifications' => array(self::HAS_MANY, 'Notification', 'poll_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'question' => 'Question',
            'description' => 'Description',
            'user_id' => 'User',
            'is_multichoice' => 'Is Multichoice',
            'poll_type' => 'Poll Type',
            'display_type' => 'Display Type',
            'result_display_type' => 'Result Display Type',
            'result_detail_type' => 'Result Detail Type',
            'result_show_time_type' => 'Result Show Time Type',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'start_at' => 'Start At',
            'end_at' => 'End At',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('question', $this->question, true);
        $criteria->compare('description', $this->description, true);
        $criteria->compare('user_id', $this->user_id);
        $criteria->compare('is_multichoice', $this->is_multichoice);
        $criteria->compare('poll_type', $this->poll_type);
        $criteria->compare('display_type', $this->display_type);
        $criteria->compare('result_display_type', $this->result_display_type);
        $criteria->compare('result_detail_type', $this->result_detail_type);
        $criteria->compare('result_show_time_type', $this->result_show_time_type);
        $criteria->compare('created_at', $this->created_at, true);
        $criteria->compare('updated_at', $this->updated_at, true);
        $criteria->compare('start_at', $this->start_at, true);
        $criteria->compare('end_at', $this->end_at, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

}