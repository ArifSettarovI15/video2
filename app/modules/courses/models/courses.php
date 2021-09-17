<?php


class Courses
{
    /**
     * @var MainClass
     */
    public $registry;
    /**
     * @var DatabaseClass
     */
    public $db;


    /**
     * @var Catalog
     */
    public $catalog;

    public function __construct(&$registry)
    {

        $this->registry =& $registry;
        $this->db =& $this->registry->db;


        require_once 'catalog.php';

        $this->catalog = new Catalog($this->registry);
    }

}