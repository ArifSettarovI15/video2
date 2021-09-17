<?php

class TigerFaqsColumns extends DbDataColumns {

    private $id;
    private $status;
    private $title;

    public function __construct()
    {
        $this->setId();
        $this->getId()->setName('id');
        $this->getId()->setType(TYPE_UINT);

        $this->setTitle();
        $this->getTitle()->setName('title');
        $this->getTitle()->setType(TYPE_STR);

        $this->setStatus();
        $this->getStatus()->setName('status');
        $this->getStatus()->setType(TYPE_BOOL);

    }
    /**
     * @return DbColumn
     */
    public function getId() {
        return $this->id;
    }

    private function setId() {
        $this->id=new DbColumn();
    }

    /**
     * @return DbColumn
     */
    public function getTitle() {
        return $this->title;
    }


    private function setTitle() {
        $this->title = new DbColumn();
    }

    /**
     * @return DbColumn
     */
    public function getStatus() {
        return $this->status;
    }

    private function setStatus() {
        $this->status = new DbColumn();
    }
}


class TigerFaqsModel extends DbDataModel {

    /**
     * @var  TigerFaqsColumns $columns_where
     */
    public $columns_where;
    /**
     * @var  TigerFaqsColumns $columns_update
     */
    public $columns_update;


    public function InitDop () {
        $this->setTableName('`core_faqs`');
        $this->setTableItemPrefix('faqs_');
        $this->setTablePrimaryKey($this->GetTableItemNameSimple('id'));
        $this->columns_where=new TigerFaqsColumns();
        $this->columns_update=new TigerFaqsColumns();
    }
}

class TigerFaqs extends  DbData
{

    /**
     * @var  TigerFaqsModel $model
     */
    public $model;

    /**
     * @var $model TigerFaqsModel
     */
    public function CreateModel () {
        $this->model=new TigerFaqsModel();
    }


    public function GetItemById ($id,$full=0){
        $this->CreateModel();
        $this->model->columns_where->getId()->setValue($id);
        return $this->GetItem($full);
    }

    public function PrepareData ($result_item,$full=0) {
        return $result_item;
    }
}
