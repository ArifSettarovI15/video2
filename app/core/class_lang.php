<?php

class LangClass
{
    /**
     * @var MainClass;
     */
    var $registry;
    var $data=array();
    var $data_js=array();

    function LangClass (&$registry){
        $this->registry=&$registry;
        $this->LoadLangFromDb();
    }

    function LoadLangFromDb (){
        /*$result=$this->registry->db->query_read("SELECT `name`, `ru` FROM `core_lang`");
        while ($result_item = $this->registry->db->fetch_array($result))
        {
            $this->data[$result_item['name']]=$result_item['ru'];
        }*/
        include (ROOT_DIR.'/app/modules/users/language/ru.php');
        /** @var array $LANG_ARRAY */
        $this->data=$LANG_ARRAY;
        $this->registry->template->global_vars['lang']=$LANG_ARRAY;
        /** @var array $LANG_JS_ARRAY */
        $this->data_js=$LANG_JS_ARRAY;
        $this->registry->template->global_vars['lang_js']=$LANG_JS_ARRAY;
    }
    // TODO
    // Make check for input vars in language (only <b><strong><a> tags)
    function SetVars ($string,$data=array()) {
        foreach ($data as $key=>$value) {
            $pos=$key+1;
            $string=str_ireplace('^s'.$pos.'$',$value,$string);
        }
        return $string;
    }
}