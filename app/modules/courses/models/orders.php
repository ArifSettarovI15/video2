<?php

class OrdersColumns extends DbDataColumns
{
    private $id;
    private $user_id;
    private $type;
    private $amount;
    private $payed;
    private $data;

    public function __construct()
    {
        $this->setId();
        $this->getId()->setName('id');
        $this->getId()->setType(TYPE_UINT);

        $this->setUserId();
        $this->getUserId()->setName('user_id');
        $this->getUserId()->setType(TYPE_UINT);

        $this->setType();
        $this->getType()->setName('type');
        $this->getType()->setType(TYPE_STR);

        $this->setAmount();
        $this->getAmount()->setName('amount');
        $this->getAmount()->setType(TYPE_UINT);

        $this->setPayed();
        $this->getPayed()->setName('payed');
        $this->getPayed()->setType(TYPE_BOOL);

        $this->setData();
        $this->getData()->setName('data');
        $this->getData()->setType(TYPE_STR);


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
    public function getType(){
        return $this->type;
    }
    public function setType(){
        $this->type = new DbColumn();
    }

    /**
     * @return DbColumn
     */
    public function getAmount(){
        return $this->amount;
    }
    public function setAmount(){
        $this->amount = new DbColumn();
    }

    /**
     * @return DbColumn
     */
    public function getPayed(){
        return $this->payed;
    }
    public function setPayed(){
        $this->payed = new DbColumn();
    }

    /**
     * @return DbColumn
     */
    public function getData(){
        return $this->data;
    }
    public function setData(){
        $this->data = new DbColumn();
    }
}

class OrdersModel extends DbDataModel
{
    /**
     * @var OrdersColumns $columns_update
     */
    public $columns_update;

    /**
     * @var OrdersColumns $columns_where
     */
    public $columns_where;

    public function InitDop()
    {
        $this->setTableName('courses_orders');
        $this->setTableItemPrefix('order_');
        $this->setTablePrimaryKey($this->GetTableItemNameSimple('id'));
        $this->columns_where=new OrdersColumns();
        $this->columns_update=new OrdersColumns();
    }
}

class Orders extends DbData
{
    /**
     * @var OrdersModel $model
     */
    public $model;

    /**
     * @var $model OrdersModel
     */

    public function CreateModel()
    {
        $this->model = new OrdersModel;
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

    public function CheckoutUrl($price, $order_id, $action ='order_payed'){
        \Cloudipsp\Configuration::setMerchantId(1397120);
        \Cloudipsp\Configuration::setSecretKey('test');
        $data = [
            'order_desc' => 'Оплата покупки на сайте SNG-training.com',
            'currency' => 'RUB',
            'amount' => $price.'00',
            'response_url' => BASE_URL.'/?action='.$action,
            'server_callback_url' => BASE_URL.'/',
            'sender_email' => $this->registry->user_info['user_email'],
            'lang' => 'ru',
            'product_id' =>$order_id,
            'lifetime' => 36000,
            'merchant_data' => array(
                'order_id' => $order_id,
            )
        ];
        $url = \Cloudipsp\Checkout::url($data);
        $data = $url->getData();
        return $data;
    }

    public function SetOrderPayed($order_id){
        $this->CreateModel();
        $this->model->columns_where->getId()->setValue($order_id);
        $this->model->columns_update->getPayed()->setValue(1);
        $this->Update();

        $this->CreateModel();
        $this->model->columns_where->getId()->setValue($order_id);
        return $this->GetItem();
    }
}