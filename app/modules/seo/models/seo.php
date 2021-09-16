<?php
class SeoClass
{
    /**
     * @var MainClass;
     */
    var $registry;
    /**
     * @var DatabaseClass
     */
    var $db;

    function __construct(&$registry){
        $this->registry=&$registry;
        $this->db=&$this->registry->db;

    }

    function Init () {
	    $url=$_SERVER['REQUEST_URI'];
	    $parts=explode('page/', $url);
	    $bb=explode('?', $parts[0]);

	    $seo_info=$this->GetSeoByUrl($bb[0],1);
	    if ($seo_info) {
		    if ($seo_info['seo_title']) {
			    $this->registry->template->global_vars['header']['title']=$seo_info['seo_title'];
		    }
		    if ($seo_info['seo_keywords']) {
			    $this->registry->template->global_vars['header']['keywords']=$seo_info['seo_keywords'];
		    }
		    if ($seo_info['seo_desc']) {
			    $this->registry->template->global_vars['header']['desc']=$seo_info['seo_desc'];
		    }
		    if ($seo_info['seo_text']) {
			    $this->registry->template->global_vars['seo_text']=$seo_info['seo_text'];
		    }
		    if ($seo_info['seo_skip_last']) {
			    $this->registry->template->global_vars['header']['skip_last_title']=1;
		    }
		    if ($seo_info['seo_page_title']) {
			    $this->registry->template->global_vars['page']['title']=$seo_info['seo_page_title'];
		    }
		    if ($seo_info['seo_icon_text']) {
			    $this->registry->template->global_vars['page']['icon_text']=$seo_info['seo_icon_text'];
		    }


	    }

    }

    function GetSeoList ($filter_options, $count=10, $start_page=0) {
        $array=array();
        $result=$this->GetSeoListFromDb($filter_options, $count, $start_page);
        while ($result_item = $this->db->fetch_array($result))
        {
            $array[]=$result_item;
        }

        return $array;
    }
    function GetSeoListFromDb ($filter_options, $count, $start_page) {
        $sql=$this->PrepareSeoListWhere($filter_options);
        if ($count=='all') {
            $sql_limit="";
        }
        else {
            $sql_limit="LIMIT ".$start_page.",".$count;
        }

        return $this->db->query_read("SELECT *
        FROM `core_seo`
        ".$sql." ".$sql_limit);
    }

    function PrepareSeoListWhere ($filter_options=array()) {
        $sql='';

        if (intval($filter_options['seo_id'])>0){
            if ($sql != '') {
                $sql .= ' AND ';
            } else {
                $sql .= ' WHERE ';
            }
            $sql.=" `core_seo`.`seo_id`=".$this->db->sql_prepare($filter_options['seo_id']);
        }
        if (intval($filter_options['seo_url'])>0){
            if ($sql != '') {
                $sql .= ' AND ';
            } else {
                $sql .= ' WHERE ';
            }
            $sql.=" `core_seo`.`seo_url`=".$this->db->sql_prepare($filter_options['seo_url']);
        }
	    if (intval($filter_options['seo_title'])>0){
		    if ($sql != '') {
			    $sql .= ' AND ';
		    } else {
			    $sql .= ' WHERE ';
		    }
		    $sql.=" `core_seo`.`seo_title`=".$this->db->sql_prepare($filter_options['seo_title']);
	    }
	    $sql .= " ORDER BY `core_seo`.`seo_url` ";


        return $sql;
    }


    function GetSeoListTotal ($filters) {
        $data=$this->GetSeoListTotalDB($filters);
        return $data;
    }

    function GetSeoListTotalDB ($filters) {
        $sql=$this->PrepareSeoListWhere($filters);
        $data=$this->db->query_read("SELECT `seo_id`
        FROM `core_seo`
        ".$sql);
        return $this->db->num_rows($data);
    }
    function GetSeoByUrl ($url,$full=0) {
        $info=$this->GetSeoByUrlFromDb($url);
        if ($info) {
            $info = $this->PrepareSeoData($info, $full);
        }
        return $info;
    }


    function GetSeoByUrlFromDb ($url) {
        return $this->db->query_first("SELECT *
        FROM `core_seo`
        WHERE `seo_url`=".$this->db->sql_prepare($url));
    }

    function AddSeo ($url,$title,$page_title,$keywords,$desc,$skip_last,$icon_text, $text_id){
        $this->db->query_write("INSERT INTO `core_seo`
                (`seo_url`,`seo_title`,`seo_keywords`,`seo_desc`,`seo_skip_last`,`seo_text_id`,`seo_page_title`,`seo_icon_text`)
                VALUES (
                ".$this->db->sql_prepare($url).",
                ".$this->db->sql_prepare($title).",
                ".$this->db->sql_prepare($keywords).",
                ".$this->db->sql_prepare($desc).",
                 ".$this->db->sql_prepare($skip_last).",
                ".$this->db->sql_prepare($text_id).",
                ".$this->db->sql_prepare($page_title).",
                ".$this->db->sql_prepare($icon_text)."
                )
          ");
        return $this->db->insert_id();
    }


    function UpdateSeo ($seo_id,$url,$title,$page_title,$keywords,$desc,$skip_last,$icon_text,$text_id){
        return $this->db->query_write("UPDATE `core_seo`
        SET
        `seo_url`=".$this->db->sql_prepare($url).",
        `seo_title`=".$this->db->sql_prepare($title).",
        `seo_keywords`=".$this->db->sql_prepare($keywords).",
        `seo_desc`=".$this->db->sql_prepare($desc).",
         `seo_skip_last`=".$this->db->sql_prepare($skip_last).",
        `seo_text_id`=".$this->db->sql_prepare($text_id).",
        `seo_page_title`=".$this->db->sql_prepare($page_title).",
        `seo_icon_text`=".$this->db->sql_prepare($icon_text)."
        WHERE `seo_id`=".$this->db->sql_prepare($seo_id));
    }

    function DeleteSeo ($id) {
        return $this->db->query_write("DELETE FROM `core_seo` WHERE `seo_id`=".$this->db->sql_prepare($id));
    }


    function GetSeoById ($id,$full=0) {
        $info=$this->GetSeoByIdFromDb($id,$full);
        return $info;
    }

    function GetSeoByIdFromDb ($id,$full=0) {
        $data=$this->db->query_first("SELECT *
        FROM `core_seo`
        WHERE `seo_id`=".$this->db->sql_prepare($id));
        if ($data) {
            $data = $this->PrepareSeoData($data, $full);
        }
        return $data;
    }

    function PrepareSeoData ($result_item,$full=0) {
        if ($full==1) {
            $result_item['seo_text']=$this->registry->text->GetText($result_item['seo_text_id']);
        }
        return $result_item;
    }

}
