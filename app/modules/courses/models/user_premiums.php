<?php

class UserPremiumsColumns extends DbDataColumns
{
    private $id;
    private $user_id;
    private $theme_id;

    public function __construct()
    {
        $this->setId();
        $this->getId()->setName('id');
        $this->getId()->setType(TYPE_UINT);

        $this->setUserId();
        $this->getUserId()->setName('user_id');
        $this->getUserId()->setType(TYPE_UINT);

        $this->setThemeId();
        $this->getThemeId()->setName('theme_id');
        $this->getThemeId()->setType(TYPE_UINT);


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
    public function getThemeId(){
        return $this->theme_id;
    }
    public function setThemeId(){
        $this->theme_id = new DbColumn();
    }

}

class UserPremiumsModel extends DbDataModel
{
    /**
     * @var UserPremiumsColumns $columns_update
     */
    public $columns_update;

    /**
     * @var UserPremiumsColumns $columns_where
     */
    public $columns_where;

    public function InitDop()
    {
        $this->setTableName('courses_user_premiums');
        $this->setTableItemPrefix('up_');
        $this->setTablePrimaryKey($this->GetTableItemNameSimple('id'));
        $this->columns_where=new UserPremiumsColumns();
        $this->columns_update=new UserPremiumsColumns();
    }
}

class UserPremiums extends DbData
{
    /**
     * @var UserPremiumsModel $model
     */
    public $model;

    /**
     * @var $model UserPremiumsModel
     */

    public function CreateModel()
    {
        $this->model = new UserPremiumsModel;
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