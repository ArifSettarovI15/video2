<?php
class TextClass
{
    /**
     * @var MainClass
     */
    var $registry;

    /**
     * @var DatabaseClass
     */
    var $db;


    function TextClass ($registry) {
        $this->registry=&$registry;
        $this->db=&$this->registry->db;
    }

    function SaveText ($id,$text) {
        $text_data=$this->GetText($id);
        if ($text_data) {
            $this->UpdateText($id,$text);
            return $id;
        }
        else {
            return $this->AddText ($text);
        }
    }


    function AddText ($text) {
        if (trim($text)!='') {
            $this->db->query_write("INSERT INTO `core_text`
        (`text`)
        VALUES (
        " . $this->db->sql_prepare($text) . "
        )");
            return $this->db->insert_id();
        }
        else {
            return 0;
        }
    }

    function UpdateText($id,$text) {
        return $this->db->query_write("UPDATE `core_text`
        SET `text`=".$this->db->sql_prepare($text)."
        WHERE `text_id`=".$this->db->sql_prepare($id));
    }

    function GetText ($id){
        $text='';
        if (intval($id)>0) {
            $data=$this->GetTextFromDb($id);
            $text=$data['text'];
	        preg_match_all("#<a(.*)</a>#Uis", $text, $res);
	        foreach ($res[0] as $k) {
	        	if (strpos($k, 'mfp=') or strpos($k, 'route=') or strpos($k, 'filter=') or strpos($k, 'sort=')) {
			        $text=str_replace($k,strip_tags($k),$text);
		        }
	        }


        }
        return $text;
    }


    function GetTextFromDb ($id){
        return $this->db->query_first("SELECT *
        FROM `core_text`
        WHERE `text_id`=".$this->db->sql_prepare($id));
    }

    function DeleteText ($id){
        return $this->db->query_first("DELETE FROM `core_text`
        WHERE `text_id`=".$this->db->sql_prepare($id));
    }
}
