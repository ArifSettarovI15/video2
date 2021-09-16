<?php
require_once 'class_db.php';
require_once 'class_cleaner.php';
require_once 'class_lang.php';
require_once 'class_route.php';
require_once 'functions.php';
require_once 'smsc_api.php';
require_once 'class_template.php' ;
require_once 'class_user.php';
require_once 'class_errors.php';
require_once 'class_paging.php';
require_once 'class_files.php';
require_once 'class_text.php';
require_once 'class_comments.php';
require_once 'class_data.php';
require_once 'stem.php';
require_once 'class_sprav.php';
require_once 'Speed.php';

class MainClass
{

    var $input;
    var $db;
    var $config;
    var $GPC = array();
    var $GPC_exists = array();
    var $debug = false;
    var $route;
    var $mailer;
    var $system_mailer;
    var $template;
    var $lang;
	var $speed;

	/**
	 * @var SettingsClass
	 */
    var $settings;
    var $settings_values;
    var $error;
    var $options;
	var $sprav;
	var $data;
    /**
     * @var FilesClass
     */
    var $files;

    /**
     * @var TextClass
     */
    var $text;

    /**
     * @var CommentsClass
     */
    var $comments;

    var $global_data=array();
    /**
     * @var UserClass
     */
    var $user;
    var $user_info;
    var $user_profile;

	/**
	 * @var SeoClass
	 */
	var $seo;

	/**
	 * @var Taxi
	 */
	var $taxi;


    function __construct()
    {

        $this->fetch_config();


        $transport = (new Swift_SmtpTransport(
            $this->config['email']['smtp']['server'],
	        $this->config['email']['smtp']['port'],
	        $this->config['email']['smtp']['ssl']
        ))
	        ->setUsername($this->config['email']['smtp']['login'])
	        ->setPassword($this->config['email']['smtp']['password'])
        ;

	    $this->mailer = new Swift_Mailer($transport);
        $this->system_mailer = new Swift_Mailer($transport);

        $this->input = new Input_Cleaner($this);

        if ($this->debug) {
            require_once(ROOT_DIR . '/app/core/class_database_explain.php');
            $this->db = new DatabaseClass_Explain($this);
        } else {
            $this->db = new DatabaseClass($this);
        }

        $this->db->connect(
            $this->config['Database']['dbname'],
            $this->config['MasterServer']['servername'],
            $this->config['MasterServer']['port'],
            $this->config['MasterServer']['username'],
            $this->config['MasterServer']['password'],
            $this->config['Mysqli']['ini_file'],
            $this->config['Mysqli']['charset']
        );

        $this->input->clean_array_gpc('c', array(
            'sid' => TYPE_STR,
            'uid' => TYPE_STR
        ));
        $this->input->clean_array_gpc('r', array(
            'ajax' => TYPE_BOOL
        ));

        $this->route= new RouteClass($this);
        $this->template=new TemplateClass($this);
        $this->lang=new LangClass($this);
        $this->files= new FilesClass($this);
        $this->text= new TextClass($this);
        $this->comments= new CommentsClass($this);
        $this->user =new UserClass($this);
	    $this->sprav= new SpravClass($this);

        $this->user_info = $this->user->AuthUser($this->GPC["sid"],$this->GPC["uid"]);

        $this->error=new ErrorClass($this);
	    $this->speed=new Speed($this);

        $this->input->clean_array_gpc('r', array(
            'route' => TYPE_STR,
            'do'=> TYPE_STR,
            'action'=> TYPE_STR
        ));


        if (!empty($this->db->explain)) {
            $this->db->timer_stop(false);
        }

        if ($this->debug) {
            restore_error_handler();
        }

        $this->GetSiteOptions();
        $this->CleanSite();
    }

    function fetch_config()
    {
        $config = array();
        include(ROOT_DIR . '/app/core/config.php');

        if (sizeof($config) == 0) {
            die('<br /><br /><strong>Configuration file error</strong>');
        }

        $this->config =& $config;

        if (isset($this->config["$_SERVER[HTTP_HOST]"])) {
            $this->config['MasterServer'] = $this->config["$_SERVER[HTTP_HOST]"];
        }

        $this->debug = !empty($this->config['system']['debug']);

        $this->SetLocalSettings();
    }

    function GetSiteOptions()
    {
        $array=array();
        $result=$this->db->query_read("SELECT * FROM core_options");
        while ($result_item = $this->db->fetch_array($result))
        {
            $array[$result_item['option_name']]=$result_item['option_value'];
        }
        $this->options=$array;
    }

    function SetSiteOption ($name,$value) {
        $this->db->query_write("UPDATE core_options
        SET option_value=".$this->db->sql_prepare($value)."
        WHERE option_name=".$this->db->sql_prepare($name));
    }


    function CleanSite()
    {
        if ($this->options['clean_session']<time()) {
            $t=time()-(24*3600);
            $this->user->DeleteOldSession($t);
            $this->SetSiteOption('clean_session',time()+(24*3600));
        }
    }


    function SetLocalSettings(){
        /*$originalLocales = explode(";", setlocale(LC_ALL, 0));
        foreach ($originalLocales as $localeSetting) {
            if (strpos($localeSetting, "=") !== false) {
                list ($category, $locale) = explode("=", $localeSetting);
            }
            else {
                $category = LC_ALL;
                $locale   = $localeSetting;
            }
            echo $category.'-'.$locale.'<br/>';
            setlocale($category, $locale);
        }*/
        if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN') {
            setlocale(LC_ALL, array('rus'));
            setlocale(LC_NUMERIC, 'en');
        }
        else {
            setlocale(LC_ALL, array('ru_RU.UTF-8'));
            setlocale(LC_NUMERIC, 'en_US');
        }
        $test=round(1/10,2);
        if (strpos($test,',')){
            echo 'comma error';
            exit;
        }
        date_default_timezone_set($this->config['system']['default_timezone']);

    }
}
