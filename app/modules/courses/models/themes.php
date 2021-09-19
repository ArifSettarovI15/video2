<?php

class ThemesColumns extends DbDataColumns
{
    private $id;
    private $title;
    private $url;
    private $catalog_id;
    private $desc;
    private $icon;
    private $icon_bg;
    private $video_price;
    private $all_price;
    private $days;
    private $sort;
    private $status;

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

        $this->setCatalogId();
        $this->getCatalogId()->setName('catalog_id');
        $this->getCatalogId()->setType(TYPE_UINT);

        $this->setDesc();
        $this->getDesc()->setName('desc');
        $this->getDesc()->setType(TYPE_STR);

        $this->setIcon();
        $this->getIcon()->setName('icon');
        $this->getIcon()->setType(TYPE_UINT);

        $this->setIconBg();
        $this->getIconBg()->setName('icon_bg');
        $this->getIconBg()->setType(TYPE_UINT);

        $this->setVideoPrice();
        $this->getVideoPrice()->setName('video_price');
        $this->getVideoPrice()->setType(TYPE_UINT);

        $this->setAllPrice();
        $this->getAllPrice()->setName('all_price');
        $this->getAllPrice()->setType(TYPE_UINT);

        $this->setDays();
        $this->getDays()->setName('days');
        $this->getDays()->setType(TYPE_UINT);

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
    public function getCatalogId(){
        return $this->catalog_id;
    }
    public function setCatalogId(){
        $this->catalog_id = new DbColumn();
    }

    /**
     * @return DbColumn
     */
    public function getDesc(){
        return $this->desc;
    }
    public function setDesc(){
        $this->desc = new DbColumn();
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
    public function getIconBg(){
        return $this->icon_bg;
    }
    public function setIconBg(){
        $this->icon_bg = new DbColumn();
    }
    /**
     * @return DbColumn
     */
    public function getVideoPrice(){
        return $this->video_price;
    }
    public function setVideoPrice(){
        $this->video_price = new DbColumn();
    }
    /**
     * @return DbColumn
     */
    public function getAllPrice(){
        return $this->all_price;
    }
    public function setAllPrice(){
        $this->all_price = new DbColumn();
    }
    /**
     * @return DbColumn
     */
    public function getDays(){
        return $this->days;
    }
    public function setDays(){
        $this->days = new DbColumn();
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

class ThemesModel extends DbDataModel
{
    /**
     * @var ThemesColumns $columns_update
     */
    public $columns_update;

    /**
     * @var ThemesColumns $columns_where
     */
    public $columns_where;

    public function InitDop()
    {
        $this->setTableName('courses_themes');
        $this->setTableItemPrefix('theme_');
        $this->setTablePrimaryKey($this->GetTableItemNameSimple('id'));
        $this->columns_where=new ThemesColumns();
        $this->columns_update=new ThemesColumns();
    }
}

class Themes extends DbData
{
    /**
     * @var ThemesModel $model
     */
    public $model;

    /**
     * @var $model ThemesModel
     */

    public function CreateModel()
    {
        $this->model = new ThemesModel;
    }

    public function GetItemById($id)
    {
        $this->CreateModel();
        $this->model->setSelectField($this->model->getTableName() . '.*');
        $this->model->SetJoinImage('icon',$this->model->GetTableItemName('icon'));
        $this->model->SetJoinImage('icon_bg',$this->model->GetTableItemName('icon_bg'));
        $this->model->columns_where->getId()->setValue($id);
        return $this->GetItem();
    }

    public function GetItemByUrl($url)
    {
        $this->CreateModel();
        $this->model->setSelectField($this->model->getTableName() . '.*');
        $this->model->SetJoinImage('icon',$this->model->GetTableItemName('icon'));
        $this->model->SetJoinImage('icon_bg',$this->model->GetTableItemName('icon_bg'));
        $this->model->columns_where->getUrl()->setValue($url);

        return $this->GetItem();
    }

    public function PrepareData($result_item, $full = 0)
    {
        $result_item = $this->registry->files->FilePrepare($result_item, 'icon_', 0);
        $result_item2 = $this->registry->files->FilePrepare($result_item, 'icon_bg_', 0);
        $result_item['theme_icon_url'] = $this->registry->files->GetImageUrl($result_item, 'medium', 0, 'icon_');
        $result_item['theme_icon_normal_url'] = $this->registry->files->GetImageUrl($result_item, 'normal', 0, 'icon_');
        $result_item['theme_icon_bg_url'] = $this->registry->files->GetImageUrl($result_item2, 'original', 0, 'icon_bg_');

        return $result_item;
    }
}