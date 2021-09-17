<?php

class CatalogColumns extends DbDataColumns
{
    private $id;
    private $title;
    private $url;
    private $icon;
    private $status;
    private $sort;

    public function __construct()
    {
        $this->setId();
        $this->getId()->setName('id');
        $this->getId()->setType(TYPE_UINT);

        $this->setTitle();
        $this->getTitle()->setName('title');
        $this->getTitle()->setType(TYPE_STR);

        $this->setUrl();
        $this->getUrl()->setName('url');
        $this->getUrl()->setType(TYPE_STR);

        $this->setIcon();
        $this->getIcon()->setName('icon');
        $this->getIcon()->setType(TYPE_UINT);

        $this->setStatus();
        $this->getStatus()->setName('status');
        $this->getStatus()->setType(TYPE_BOOL);

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
    public function getTitle(){
        return $this->title;
    }
    public function setTitle(){
        $this->title = new DbColumn();
    }
    /**
     * @return DbColumn
     */
    public function getUrl(){
        return $this->url;
    }
    public function setUrl(){
        $this->url = new DbColumn();
    }
    /**
     * @return DbColumn
     */
    public function getIcon(){
        return $this->icon;
    }
    public function setIcon(){
        $this->icon = new DbColumn();
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
    /**
     * @return DbColumn
     */
    public function getStatus(){
        return $this->status;
    }
    public function setStatus(){
        $this->status = new DbColumn();
    }
}

class CatalogModel extends DbDataModel
{
    /**
     * @var CatalogColumns $columns_update
     */
    public $columns_update;

    /**
     * @var CatalogColumns $columns_where
     */
    public $columns_where;

    public function InitDop()
    {
        $this->setTableName('courses_catalog');
        $this->setTableItemPrefix('catalog_');
        $this->setTablePrimaryKey($this->GetTableItemNameSimple('id'));
        $this->columns_where=new CatalogColumns();
        $this->columns_update=new CatalogColumns();
    }
}

class Catalog extends DbData
{
    /**
     * @var CatalogModel $model
     */
    public $model;

    /**
     * @var $model CatalogModel
     */

    public function CreateModel()
    {
        $this->model = new CatalogModel;
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
        $result_item = $this->registry->files->FilePrepare($result_item, 'icon_', 0);
        $result_item['catalog_icon_url'] = $this->registry->files->GetImageUrl($result_item, 'medium', 0, 'icon_');
        $result_item['catalog_icon_normal_url'] = $this->registry->files->GetImageUrl($result_item, 'normal', 0, 'icon_');

        return $result_item;
    }
}