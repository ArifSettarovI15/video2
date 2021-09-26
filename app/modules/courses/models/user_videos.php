<?php

class UserVideosColumns extends DbDataColumns
{
    private $id;
    private $user_id;
    private $date_to;
    private $video_id;

    public function __construct()
    {
        $this->setId();
        $this->getId()->setName('id');
        $this->getId()->setType(TYPE_UINT);

        $this->setUserId();
        $this->getUserId()->setName('user_id');
        $this->getUserId()->setType(TYPE_UINT);

        $this->setDateTo();
        $this->getDateTo()->setName('date_to');
        $this->getDateTo()->setType(TYPE_STR);

        $this->setVideoId();
        $this->getVideoId()->setName('video_id');
        $this->getVideoId()->setType(TYPE_UINT);


    }

    /**
     * @return DbColumn
     */
    public function getId(){
        return $this->id;
    }
    public function setId(){
        $this->id = new DbColumn();
    }

    /**
     * @return DbColumn
     */
    public function getUserId(){
        return $this->user_id;
    }
    public function setUserId(){
        $this->user_id = new DbColumn();
    }

    /**
     * @return DbColumn
     */
    public function getDateTo(){
        return $this->date_to;
    }
    public function setDateTo(){
        $this->date_to = new DbColumn();
    }

    /**
     * @return DbColumn
     */
    public function getVideoId(){
        return $this->video_id;
    }
    public function setVideoId(){
        $this->video_id = new DbColumn();
    }

}

class UserVideosModel extends DbDataModel
{
    /**
     * @var UserVideosColumns $columns_update
     */
    public $columns_update;

    /**
     * @var UserVideosColumns $columns_where
     */
    public $columns_where;

    public function InitDop()
    {
        $this->setTableName('courses_user_videos');
        $this->setTableItemPrefix('uv_');
        $this->setTablePrimaryKey($this->GetTableItemNameSimple('id'));
        $this->columns_where=new UserVideosColumns();
        $this->columns_update=new UserVideosColumns();
    }
}

class UserVideos extends DbData
{
    /**
     * @var UserVideosModel $model
     */
    public $model;

    /**
     * @var $model UserVideosModel
     */

    public function CreateModel()
    {
        $this->model = new UserVideosModel;
    }

    public function GetItemById($id)
    {
        $this->CreateModel();
        $this->model->setSelectField($this->model->getTableName() . '.*');
        $this->model->columns_where->getId()->setValue($id);
        return $this->GetItem();
    }

    public function PrepareData($result_item, $full = 0)
    {
        return $result_item;
    }

}