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
    /**
     * @var Themes
     */
    public $themes;

    public function __construct(&$registry)
    {

        $this->registry =& $registry;
        $this->db =& $this->registry->db;


        require_once 'catalog.php';
        require_once 'themes.php';

        $this->catalog = new Catalog($this->registry);
        $this->themes = new Themes($this->registry);
    }

}