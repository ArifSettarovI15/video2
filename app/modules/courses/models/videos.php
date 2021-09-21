<?php

class VideosColumns extends DbDataColumns
{
    private $id;
    private $title;
    private $desc;
    private $embed_link;
    private $price;
    private $use_sale;
    private $main;
    private $theme;
    private $icon;
    private $block;
    private $sort;

    public function __construct()
    {
        $this->setId();
        $this->getId()->setName('id');
        $this->getId()->setType(TYPE_UINT);

        $this->setTitle();
        $this->getTitle()->setName('title');
        $this->getTitle()->setType(TYPE_STR);

        $this->setEmbedLink();
        $this->getEmbedLink()->setName('embed_link');
        $this->getEmbedLink()->setType(TYPE_STR);

        $this->setTheme();
        $this->getTheme()->setName('theme');
        $this->getTheme()->setType(TYPE_UINT);

        $this->setDesc();
        $this->getDesc()->setName('desc');
        $this->getDesc()->setType(TYPE_STR);

        $this->setIcon();
        $this->getIcon()->setName('icon');
        $this->getIcon()->setType(TYPE_UINT);

        $this->setPrice();
        $this->getPrice()->setName('price');
        $this->getPrice()->setType(TYPE_UINT);

        $this->setUseSail();
        $this->getUseSail()->setName('use_sale');
        $this->getUseSail()->setType(TYPE_BOOL);

        $this->setMain();
        $this->getMain()->setName('main');
        $this->getMain()->setType(TYPE_BOOL);

        $this->setBlock();
        $this->getBlock()->setName('block');
        $this->getBlock()->setType(TYPE_UINT);

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
    public function getEmbedLink(){
        return $this->embed_link;
    }
    public function setEmbedLink(){
        $this->embed_link = new DbColumn();
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
    public function getBlock(){
        return $this->block;
    }
    public function setBlock(){
        $this->block = new DbColumn();
    }
    /**
     * @return DbColumn
     */
    public function getUseSail(){
        return $this->use_sale;
    }
    public function setUseSail(){
        $this->use_sale = new DbColumn();
    }
    /**
     * @return DbColumn
     */
    public function getMain(){
        return $this->main;
    }
    public function setMain(){
        $this->main = new DbColumn();
    }
    /**
     * @return DbColumn
     */
    public function getPrice(){
        return $this->price;
    }
    public function setPrice(){
        $this->price = new DbColumn();
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

class VideosModel extends DbDataModel
{
    /**
     * @var VideosColumns $columns_update
     */
    public $columns_update;

    /**
     * @var VideosColumns $columns_where
     */
    public $columns_where;

    public function InitDop()
    {
        $this->setTableName('courses_videos');
        $this->setTableItemPrefix('video_');
        $this->setTablePrimaryKey($this->GetTableItemNameSimple('id'));
        $this->columns_where=new VideosColumns();
        $this->columns_update=new VideosColumns();
    }
}

class Videos extends DbData
{
    /**
     * @var VideosModel $model
     */
    public $model;

    /**
     * @var $model VideosModel
     */

    public function CreateModel()
    {
        $this->model = new VideosModel;
    }

    public function GetItemById($id)
    {
        $this->CreateModel();
        $this->model->setSelectField($this->model->getTableName() . '.*');
        $this->model->SetJoinImage('icon',$this->model->GetTableItemName('icon'));
        $this->model->columns_where->getId()->setValue($id);
        return $this->GetItem();
    }


    public function PrepareData($result_item, $full = 0)
    {
        $result_item = $this->registry->files->FilePrepare($result_item, 'icon_', 0);
        $result_item['video_icon_url'] = $this->registry->files->GetImageUrl($result_item, 'medium', 0, 'icon_');

        return $result_item;
    }
}