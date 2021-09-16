<?php

class CommentsClass
{
    /**
     * @var MainClass
     */
    var $registry;

    /**
     * @var DatabaseClass
     */
    var $db;

    function __construct($registry)
    {
        $this->registry =& $registry;
        $this->db =& $this->registry->db;
    }


    function AddComment ($object_name,$object_id,$status,$user_id,$author,$text,$time,$rank,$comment_data=array()) {
        $comment_data=serialize($comment_data);

        $this->db->query_write("INSERT INTO `core_comments`
        (`comment_status`,`comment_object_name`,`comment_object_id`,`user_id`,`comment_author`,`comment_text`,`comment_time`,`comment_rank`,`comment_data`)
        VALUES (
        " . $this->db->sql_prepare($status) . ",
        " . $this->db->sql_prepare($object_name) . ",
        " . $this->db->sql_prepare($object_id) . ",
        " . $this->db->sql_prepare($user_id) . ",
        " . $this->db->sql_prepare($author) . ",
        " . $this->db->sql_prepare($text) . ",
        " . $this->db->sql_prepare($time) . ",
        " . $this->db->sql_prepare($rank) . ",
        " . $this->db->sql_prepare($comment_data) . "
        )");
        return $this->db->insert_id();
    }

    function GetComments ($options, $count=10, $start_page=0,$join='', $select='') {
        $array=array();
        $result=$this->GetCommentsFromDb($options, $count, $start_page,$join, $select);
        while ($result_item = $this->db->fetch_array($result))
        {
            $result_item['comment_data']=unserialize($result_item['comment_data']);

            $array[]=$result_item;
        }

        return $array;
    }

    function GetCommentsFromDb ($filter_options=array(), $count, $start_page,$join='', $select=''){
        $sql=$this->PrepareCommentsWhere($filter_options);
        if ($count=='all') {
            $sql_limit="";
        }
        else {
            $sql_limit=" LIMIT ".$start_page.",".$count;
        }

        $s=' * ';
        if ($select) {
        	$s.=', '.$select;
        }

        return $this->db->query_read("SELECT ".$s."
        FROM `core_comments`
        ".$join."
        ".$sql." ".$sql_limit);
    }
    function GetObjectRank ($object_name,$object_id) {
        $result=$this->db->query_first("SELECT SUM(`core_comments`.`comment_rank`) as sum, count(`core_comments`.`comment_id`) as count
        FROM `core_comments`
        WHERE `comment_object_name`=".$this->db->sql_prepare($object_name)." AND `comment_object_id`=".$this->db->sql_prepare($object_id)." and comment_status=1");
        $rank=0;
        if ($result['count']>0) {
            $rank=round($result['sum']/$result['count'],2);
        }
        return array($result['count'],$rank);
    }
    function GetCommentsTotal ($filter_options=array(),$join='') {
        $result=$this->GetCommentsTotalFromDb($filter_options,$join);
        return intval($result['count']);
    }

    function GetCommentsTotalFromDb($filter_options,$join){
        $sql=$this->PrepareCommentsWhere($filter_options);
        return $this->db->query_first("SELECT count(`core_comments`.`comment_id`) as count
        FROM `core_comments`
        ".$join."
        ".$sql);
    }
    function PrepareCommentsWhere ($filter_options){
        $sql='';

        if (intval($filter_options['comment_id'])!=0){
            if ($sql != '') {
                $sql .= ' AND ';
            } else {
                $sql .= ' WHERE ';
            }
            $sql.="`core_comments`.`comment_id`=".$this->db->sql_prepare($filter_options['comment_id']);
        }
        if ($filter_options['object_name']!=''){
            if ($sql != '') {
                $sql .= ' AND ';
            } else {
                $sql .= ' WHERE ';
            }
            $sql.="`core_comments`.`comment_object_name`=".$this->db->sql_prepare($filter_options['object_name']);
        }
        if ($filter_options['comment_author']!=''){
            if ($sql != '') {
                $sql .= ' AND ';
            } else {
                $sql .= ' WHERE ';
            }
            $sql.='`core_comments`.`comment_author` LIKE "%'.$this->db->escape_string_like($filter_options['comment_author']).'%"';
        }
        if ($filter_options['comment_text']!=''){
            if ($sql != '') {
                $sql .= ' AND ';
            } else {
                $sql .= ' WHERE ';
            }
            $sql.='(`core_comments`.`comment_text` LIKE "%'.$this->db->escape_string_like($filter_options['comment_text']).'%" OR 
            REPLACE(`shop_items`.`item_title`, " ", "") LIKE "%' . $this->db->escape_string_like($filter_options['comment_text']) . '%") ';
        }
        if (intval($filter_options['object_id'])!=0){
            if ($sql != '') {
                $sql .= ' AND ';
            } else {
                $sql .= ' WHERE ';
            }
            $sql.="`core_comments`.`comment_object_id`=".$this->db->sql_prepare($filter_options['object_id']);
        }

        if (is_string($filter_options['comment_status']) or array_key_exists ('comment_status', $filter_options)==false) {
            $filter_options['comment_status']=1;
        }
        if (is_numeric($filter_options['comment_status']) && $filter_options['comment_status']!=-1 ){

            if ($sql != '') {
                $sql .= ' AND ';
            } else {
                $sql .= ' WHERE ';
            }
            $sql.="`core_comments`.`comment_status`=".$this->db->sql_prepare($filter_options['comment_status']);
        }
        if ($filter_options['date_from']!=''){
            $date_from=strtotime($filter_options['date_from'].' 00:00:00');
            if ($sql != '') {
                $sql .= ' AND ';
            } else {
                $sql .= ' WHERE ';
            }
            $sql.="`core_comments`.`comment_time`>=".$this->db->sql_prepare($date_from);
        }
        if ($filter_options['date_to']!=''){
            $date_to=strtotime($filter_options['date_to'].' 23:59:59');
            if ($sql != '') {
                $sql .= ' AND ';
            } else {
                $sql .= ' WHERE ';
            }
            $sql.="`core_comments`.`comment_time`<=".$this->db->sql_prepare($date_to);
        }

        if ($filter_options['order_way']=='desc'){
            $filter_options['order_sort_sql']='DESC';
        }
        else {
            $filter_options['order_sort_sql']='ASC';
        }

	    if ($filter_options['order']=='rank'){

		    if ($filter_options['show_order']) {
			    $sql .= " ORDER BY `core_comments`.`comment_rank` DESC, `core_comments`.`comment_time` DESC ";
		    }
	    }
        elseif ($filter_options['order']=='time'){
            $filter_options['order_sql']=' `core_comments`.`comment_time`';
            $filter_options['order_sort_sql']='DESC';
	        if ($filter_options['show_order']) {
		        $sql .= " ORDER BY " . $filter_options['order_sql'] . " " . $filter_options['order_sort_sql'];
	        }
        }
        else {
            $filter_options['order_sql']=' `core_comments`.`comment_time`';
            $filter_options['order_sort_sql']='DESC';
	        if ($filter_options['show_order']) {
		        $sql .= " ORDER BY " . $filter_options['order_sql'] . " " . $filter_options['order_sort_sql'];
	        }
        }


        return $sql;
    }

    function UpdateCommentStatus ($comment_id,$value){
        return $this->db->query_write("UPDATE `core_comments`
         SET `comment_status`=".$this->db->sql_prepare($value)."
         WHERE `comment_id`=".$this->db->sql_prepare($comment_id));
    }
}
