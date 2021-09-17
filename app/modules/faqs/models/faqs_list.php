<?php

class TigerFaqsListColumns extends DbDataColumns {

    private $id;
    private $status;
    private $ask;
    private $sort;
    private $answer;
    private $faq;

    public function __construct()
    {
        $this->setId();
        $this->getId()->setName('id');
        $this->getId()->setType(TYPE_UINT);


        $this->setStatus();
        $this->getStatus()->setName('status');
        $this->getStatus()->setType(TYPE_BOOL);

        $this->setAsk();
        $this->getAsk()->setName('ask');
        $this->getAsk()->setType(TYPE_STR);

        $this->setAnswer();
        $this->getAnswer()->setName('answer');
        $this->getAnswer()->setType(TYPE_STR);

        $this->setSort();
        $this->getSort()->setName('sort');
        $this->getSort()->setType(TYPE_UINT);

        $this->setFaq();
        $this->getFaq()->setName('faq');
        $this->getFaq()->setType(TYPE_UINT);
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
    public function getStatus() {
        return $this->status;
    }

    private function setStatus() {
        $this->status = new DbColumn();
    }

    /**
     * @return DbColumn
     */
    public function getAsk() {
        return $this->ask;
    }


    private function setAsk() {
        $this->ask = new DbColumn();
    }

    /**
     * @return DbColumn
     */
    public function getSort() {
        return $this->sort;
    }

    private function setSort() {
        $this->sort = new DbColumn();
    }

    /**
     * @return DbColumn
     */
    public function getAnswer() {
        return $this->answer;
    }

    private function setAnswer() {
        $this->answer = new DbColumn();
    }

    /**
     * @return DbColumn
     */
    public function getFaq() {
        return $this->faq;
    }

    private function setFaq() {
        $this->faq = new DbColumn();
    }
}


class TigerFaqsListModel extends DbDataModel {

    /**
     * @var  TigerFaqsListColumns $columns_where
     */
    public $columns_where;
    /**
     * @var  TigerFaqsListColumns $columns_update
     */
    public $columns_update;


    public function InitDop () {
        $this->setTableName('`core_faqs_items`');
        $this->setTableItemPrefix('fitem_');
        $this->setTablePrimaryKey($this->GetTableItemNameSimple('id'));
        $this->columns_where=new TigerFaqsListColumns();
        $this->columns_update=new TigerFaqsListColumns();
    }
}

class TigerFaqsList extends  DbData
{

    /**
     * @var  TigerFaqsListModel $model
     */
    public $model;

    /**
     * @var $model TigerFaqsListModel
     */
    public function CreateModel () {
        $this->model=new TigerFaqsListModel();
    }


    public function GetItemById ($id,$full=0){
        $this->CreateModel();
        $this->model->columns_where->getId()->setValue($id);
        return $this->GetItem($full);
    }
    public function GetActiveFaqs (){
        $this->CreateModel();
        $this->model->columns_where->getStatus()->setValue(1);
        $this->model->setOrderBy('fitem_sort');
        return $this->GetList();
    }

    public function PrepareData ($result_item,$full=0) {
        return $result_item;
    }
}
