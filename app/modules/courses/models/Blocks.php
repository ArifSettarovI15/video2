<?php

class BlocksColumns extends DbDataColumns
{
    private $id;
    private $theme;
    private $title;
    private $sort;

    public function __construct()
    {
        $this->setId();
        $this->getId()->setName('id');
        $this->getId()->setType(TYPE_UINT);

        $this->setTheme();
        $this->getTheme()->setName('theme');
        $this->getTheme()->setType(TYPE_UINT);


        $this->setTitle();
        $this->getTitle()->setName('title');
        $this->getTitle()->setType(TYPE_STR);

        $this->setSort();
        $this->getSort()->setName('sort');
        $this->getSort()->setType(TYPE_UINT);

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
    public function getTheme(){
        return $this->theme;
    }
    public function setTheme(){
        $this->theme = new DbColumn();
    }
    /**
     * @return DbColumn
     */
    public function getTitle(){
        return $this->title;
    }
    public function setTitle(){
        $this->title = new DbColumn();
    }

    /**
     * @return DbColumn
     */
    public function getSort(){
        return $this->sort;
    }
    public function setSort(){
        $this->sort = new DbColumn();
    }
}

class BlocksModel extends DbDataModel
{
    /**
     * @var BlocksColumns $columns_update
     */
    public $columns_update;

    /**
     * @var BlocksColumns $columns_where
     */
    public $columns_where;

    public function InitDop()
    {
        $this->setTableName('courses_blocks');
        $this->setTableItemPrefix('block_');
        $this->setTablePrimaryKey($this->GetTableItemNameSimple('id'));
        $this->columns_where=new BlocksColumns();
        $this->columns_update=new BlocksColumns();
    }
}

class Blocks extends DbData
{
    /**
     * @var BlocksModel $model
     */
    public $model;

    /**
     * @var $model BlocksModel
     */

    public function CreateModel()
    {
        $this->model = new BlocksModel;
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