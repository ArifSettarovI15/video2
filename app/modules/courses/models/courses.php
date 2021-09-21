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
    /**
     * @var Blocks
     */
    public $blocks;
    /**
     * @var Videos
     */
    public $videos;

    public function __construct(&$registry)
    {

        $this->registry =& $registry;
        $this->db =& $this->registry->db;

        require_once 'catalog.php';
        require_once 'themes.php';
        require_once 'Blocks.php';
        require_once 'videos.php';

        $this->catalog = new Catalog($this->registry);
        $this->themes = new Themes($this->registry);
        $this->blocks = new Blocks($this->registry);
        $this->videos = new Videos($this->registry);
    }

}