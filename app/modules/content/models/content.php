<?php
class ContentClass
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

    function GetContentList ($filter_options, $count=10, $start_page=0, $pages=0) {
        $array=array();
        $result=$this->GetContentListFromDb($filter_options, $count, $start_page, $pages);

        while ($result_item = $this->db->fetch_array($result))
        {
            $result_item = $this->PrepareContentData($result_item,0);
            $array[]=$result_item;
        }

        return $array;
    }
    function GetContentListFromDb ($filter_options, $count, $start_page, $pages=0) {
        $sql=$this->PrepareContentListWhere($filter_options, $pages);
        if ($count=='all') {
            $sql_limit="";
        }
        else {
            $sql_limit="LIMIT ".$start_page.",".$count;
        }

	    $inner='';
        if (isset($filter_options['inner_products'])) {
	        $inner=' INNER JOIN `content_items` ON `core_content`.`content_id`=content_items.`content_id` ';
        }

        return $this->db->query_read("SELECT `core_content`.*,thumb.*,news_cats.title as cat_title,
thumb3.file_sizes as thumb3_file_sizes, thumb3.file_folder as thumb3_file_folder, thumb3.file_name as thumb3_file_name
        FROM `core_content`
        LEFT JOIN `core_files` thumb ON `core_content`.`content_thumb_id`=thumb.`file_id`
        LEFT JOIN `core_files` thumb3 ON `core_content`.`content_thumb_id3`=thumb3.`file_id`
        ".$inner."
         LEFT JOIN `news_cats` ON `core_content`.`content_cat`=news_cats.`id`
        ".$sql." ".$sql_limit);
    }

    function PrepareContentListWhere ($filter_options=array(), $pages=0) {
        if ($pages){$sql='';}else{
        $sql='WHERE content_type!="pages" ';
        }
        if (is_string($filter_options['content_status']) or array_key_exists ('content_status', $filter_options)==false) {
            $filter_options['content_status']=1;
        }

        if ($filter_options['content_status']!=-1 ){

            if ($sql != '') {
                $sql .= ' AND ';
            } else {
                $sql .= ' WHERE ';
            }
            $sql.="`core_content`.`content_status`=".$this->db->sql_prepare($filter_options['content_status']);
        }
        if ($filter_options['year']!=''){
            if ($sql != '') {
                $sql .= ' AND ';
            } else {
                $sql .= ' WHERE ';
            }
            $s_like=$filter_options['year'];
            if ($filter_options['month']!='') {
                $s_like.=$filter_options['month'];
            }

            $sql .= "`core_content`.`content_date` LIKE '" . $this->db->escape_string_like($s_like) . "%' ";
        }
	    if (intval($filter_options['item_id'])>0){
		    if ($sql != '') {
			    $sql .= ' AND ';
		    } else {
			    $sql .= ' WHERE ';
		    }
		    $sql.=" `content_items`.`item_id`=".$this->db->sql_prepare($filter_options['item_id']);
	    }
        if (intval($filter_options['content_id'])>0){
            if ($sql != '') {
                $sql .= ' AND ';
            } else {
                $sql .= ' WHERE ';
            }
            $sql.=" `core_content`.`content_id`=".$this->db->sql_prepare($filter_options['content_id']);
        }
	    if ($filter_options['akcii_active']){
		    if ($sql != '') {
			    $sql .= ' AND ';
		    } else {
			    $sql .= ' WHERE ';
		    }
		    $sql.=" (`core_content`.`content_time_end`=0 OR `core_content`.`content_time_end`>=".$this->db->sql_prepare(TIMENOW).') ';
	    }

        if (isset($filter_options['vendors']) and is_array($filter_options['vendors'])) {
        	if (count($filter_options['vendors'])>0) {
        		$pp=array();
        		foreach ($filter_options['vendors'] as $a) {
			        $pp[]=intval($a);
		        }
		        if (count($pp)>0) {
			        if ( $sql != '' ) {
				        $sql .= ' AND ';
			        } else {
				        $sql .= ' WHERE ';
			        }
			        $sql .= " `core_content`.`content_vendor_id` IN (" . implode( ',', $pp ) . ")";
		        }
	        }
        }
	    if (isset($filter_options['cats']) and is_array($filter_options['cats'])) {
		    if (count($filter_options['cats'])>0) {
			    $pp=array();
			    foreach ($filter_options['cats'] as $a) {
				    $pp[]=intval($a);
			    }
			    if (count($pp)>0) {
				    if ( $sql != '' ) {
					    $sql .= ' AND ';
				    } else {
					    $sql .= ' WHERE ';
				    }
				    $sql .= " `core_content`.`content_cat` IN (" . implode( ',', $pp ) . ")";
			    }
		    }
	    }
	    if (isset($filter_options['content_thumb'])) {
		    if ($filter_options['content_thumb']){
			    if ($sql != '') {
				    $sql .= ' AND ';
			    } else {
				    $sql .= ' WHERE ';
			    }
			    $sql.=" `core_content`.`content_thumb_id`!=0 ";
		    }
		    else {
				    if ($sql != '') {
					    $sql .= ' AND ';
				    } else {
					    $sql .= ' WHERE ';
				    }
				    $sql.=" `core_content`.`content_thumb_id`=0 ";

		    }
	    }
	    if (isset($filter_options['content_thumb3'])) {
		    if ($filter_options['content_thumb3']){
			    if ($sql != '') {
				    $sql .= ' AND ';
			    } else {
				    $sql .= ' WHERE ';
			    }
			    $sql.=" `core_content`.`content_thumb_id3`!=0 ";
		    }
		    else {
				    if ($sql != '') {
					    $sql .= ' AND ';
				    } else {
					    $sql .= ' WHERE ';
				    }
				    $sql.=" `core_content`.`content_thumb_id3`=0 ";

		    }
	    }
        if (intval($filter_options['content_cat'])>0){
            if ($sql != '') {
                $sql .= ' AND ';
            } else {
                $sql .= ' WHERE ';
            }
            $sql.=" `core_content`.`content_cat`=".$this->db->sql_prepare($filter_options['content_cat']);
        }
        if ($filter_options['content_title']!=''){
            if ($sql != '') {
                $sql .= ' AND ';
            } else {
                $sql .= ' WHERE ';
            }
            $sql.=" `core_content`.`content_title` LIKE '%".$this->db->escape_string_like($filter_options['content_title'])."%'";
        }
        if ($filter_options['content_type']!='') {
            if ($sql != '') {
                $sql .= ' AND ';
            } else {
                $sql .= ' WHERE ';
            }
            $sql .= "`core_content`.`content_type`=" . $this->db->sql_prepare($filter_options['content_type']);
        }
        $gr='';

        if ($filter_options['skip_date']!=true) {
            if ($sql != '') {
                $sql .= ' AND ';
            } else {
                $sql .= ' WHERE ';
            }
            $sql .= "`core_content`.`content_time`<=" . strtotime(date('d.m.Y H:i', TIMENOW));
        }

        if ($filter_options['order_way']=='desc'){
            $filter_options['order_sort_sql']='DESC';
        }
        else {
            $filter_options['order_sort_sql']='ASC';
        }

        if ($filter_options['order']=='name'){
            $filter_options['order_sql']=' `core_content`.`content_title`';
        }
        elseif ($filter_options['order']=='date'){
            $filter_options['order_sql']=' `core_content`.`content_time`';
            $filter_options['order_sort_sql']='DESC';
        }
        elseif ($filter_options['order']=='sort'){
	        $filter_options['order_sql']=' `core_content`.`content_sort`';
	        $filter_options['order_sort_sql']='ASC';
        }
        elseif ($filter_options['order']=='views'){
	        $filter_options['order_sql']=' `core_content`.`content_views`';
	        $filter_options['order_sort_sql']='DESC';
        }
        else {
            $filter_options['order_sql']=' `core_content`.`content_time`';
            $filter_options['order_sort_sql']='DESC';
        }
        $sql.=' GROUP BY core_content.content_id ';
        if ($filter_options['show_order']) {
            $sql .= " ORDER BY " . $filter_options['order_sql'] . " " . $filter_options['order_sort_sql'];
        }

        return $sql;
    }


    function GetContentListTotal ($filters) {
        $data=$this->GetContentListTotalDB($filters);
        return $data;
    }
    function GetPublicationsTotal($type='all'){
        if ($type=='all'){
            $data=$this->db->query_read("SELECT COUNT(*) as count
        FROM `core_content` WHERE content_type!='pages' ");
        }
        else{
            $data=$this->db->query_read("SELECT COUNT(*) as count
            FROM `core_content` WHERE content_type!='pages' AND content_type=".$this->db->sql_prepare($type));
        }
        $data = mysqli_fetch_array($data);
        return (int)$data['count'];
    }
    function GetContentListTotalDB ($filters) {
        $sql=$this->PrepareContentListWhere($filters);
        $data=$this->db->query_read("SELECT `content_id`
        FROM `core_content`  
        ".$sql." ");
        return $this->db->num_rows($data);
    }
    function GetContentByUrl ($url,$full=0,$skip_img=0) {
        $info=$this->GetContentByUrlFromDb($url);
        if ($info) {
            $info = $this->PrepareContentData($info, $full,$skip_img);
        }
        return $info;
    }


    function GetContentViews ($id){
    	$data=$this->db->query_first("SELECT `count` FROM core_content_views WHERE id=".$this->db->sql_prepare($id));
    	return intval($data['count']);
    }

	function UpdateContentItemViews ($id,$value) {
		$this->db->query_write("UPDATE core_content
				SET `content_views`=".$this->db->sql_prepare($value)."
						WHERE 	content_id =".$this->db->sql_prepare($id));
	}
	function UpdateContentViews ($id) {
		$this->db->query_write("UPDATE core_content_views
				SET `count`=`count`+1
						WHERE 	id =".$this->db->sql_prepare($id));
		if (intval($this->db->affected_rows())==0) {
			$this->db->query_write("INSERT INTO core_content_views (id, `count`) VALUES (".$this->db->sql_prepare($id).", 1)");
		}
	}


    function GetContentByUrlFromDb ($url) {
        return $this->db->query_first("SELECT `core_content`.*,thumb.*,
thumb3.file_sizes as thumb3_file_sizes, thumb3.file_folder as thumb3_file_folder, thumb3.file_name as thumb3_file_name
        FROM `core_content`
        LEFT JOIN `core_files` thumb ON `core_content`.`content_thumb_id`=thumb.`file_id`
        LEFT JOIN `core_files` thumb3 ON `core_content`.`content_thumb_id3`=thumb3.`file_id`
        WHERE `content_url`=".$this->db->sql_prepare($url));
    }

    function AddContent ($content_status,$content_title,$content_url,$content_type,$content_short,
	    $meta_title,$meta_keywords,$meta_desc,$text_id,$photo_id,$image_header,$time,$date,$content_cat,$template_name,$vendor_id,$dop,$time_end,$source_url,$content_city_id, $content_city_id2){
        $this->db->query_write("INSERT INTO `core_content`
                (`content_status`,`content_title`,`content_url`,`content_type`,`content_short`,`content_text_id`,`content_time`,`content_thumb_id`,
                `content_thumb_id3`,`head_title`,`head_desc`,`head_keywords`,`content_date`,`content_cat`,`content_template`,`content_vendor_id`,`content_dop`,`content_time_end`,`source_url`,`content_city_id`,`content_city_id2`)
                VALUES (
                ".$this->db->sql_prepare($content_status).",
                ".$this->db->sql_prepare($content_title).",
                ".$this->db->sql_prepare($content_url).",
                ".$this->db->sql_prepare($content_type).",
                ".$this->db->sql_prepare($content_short).",
                ".$this->db->sql_prepare($text_id).",
                ".$this->db->sql_prepare($time).",
                ".$this->db->sql_prepare($photo_id).",
                ".$this->db->sql_prepare($image_header).",
                ".$this->db->sql_prepare($meta_title).",
                ".$this->db->sql_prepare($meta_desc).",
                ".$this->db->sql_prepare($meta_keywords).",
                ".$this->db->sql_prepare($date).",
                ".$this->db->sql_prepare($content_cat).",
                ".$this->db->sql_prepare($template_name).",
                ".$this->db->sql_prepare($vendor_id).",
                ".$this->db->sql_prepare($dop).",
                ".$this->db->sql_prepare($time_end).",
                ".$this->db->sql_prepare($source_url).",
                ".$this->db->sql_prepare($content_city_id).",
                ".$this->db->sql_prepare($content_city_id2)."
                )
          ");
        return $this->db->insert_id();
    }


    function UpdateContent ($content_id,$content_status,$content_title,$content_url,$content_type,$content_short,$meta_title,$meta_keywords,$meta_desc,$text_id,$photo_id,$image_header,$content_cat,$template_name,$time,$vendor_id,$dop,$time_end,$source_url,$content_city_id,$content_city_id2){
        return $this->db->query_write("UPDATE `core_content`
        SET
        `content_status`=".$this->db->sql_prepare($content_status).",
        `content_title`=".$this->db->sql_prepare($content_title).",
        `content_url`=".$this->db->sql_prepare($content_url).",
        `content_type`=".$this->db->sql_prepare($content_type).",
        `content_short`=".$this->db->sql_prepare($content_short).",
        `content_text_id`=".$this->db->sql_prepare($text_id).",
        `content_thumb_id`=".$this->db->sql_prepare($photo_id).",
        `content_thumb_id3`=".$this->db->sql_prepare($image_header).",
        `head_title`=".$this->db->sql_prepare($meta_title).",
        `head_desc`=".$this->db->sql_prepare($meta_desc).",
        `head_keywords`=".$this->db->sql_prepare($meta_keywords).",
        `content_cat`=".$this->db->sql_prepare($content_cat).",
        `content_template`=".$this->db->sql_prepare($template_name).",
        `content_time`=".$this->db->sql_prepare($time).",
        `content_vendor_id`=".$this->db->sql_prepare($vendor_id).",
        `content_dop`=".$this->db->sql_prepare($dop).",
        `content_time_end`=".$this->db->sql_prepare($time_end).",
        `source_url`=".$this->db->sql_prepare($source_url).",
        `content_city_id`=".$this->db->sql_prepare($content_city_id).",
        `content_city_id2`=".$this->db->sql_prepare($content_city_id2)."
        WHERE `content_id`=".$this->db->sql_prepare($content_id));
    }

    function DeleteContent ($id) {
        return $this->db->query_write("DELETE FROM `core_content` WHERE `content_id`=".$this->db->sql_prepare($id));
    }


    function GetContentById ($id,$full=0) {
        $info=$this->GetContentByIdFromDb($id,$full);
        return $info;
    }
    function GetContentByIdFromDb ($id,$full=0) {
        $data=$this->db->query_first("SELECT `core_content`.*,thumb.*,
thumb3.file_sizes as thumb3_file_sizes, thumb3.file_folder as thumb3_file_folder, thumb3.file_name as thumb3_file_name
        FROM `core_content`
        LEFT JOIN `core_files` thumb ON `core_content`.`content_thumb_id`=thumb.`file_id`
        LEFT JOIN `core_files` thumb3 ON `core_content`.`content_thumb_id3`=thumb3.`file_id`
        WHERE `content_id`=".$this->db->sql_prepare($id));
        if ($data) {
            $data = $this->PrepareContentData($data, $full);
        }
        return $data;
    }

    function PrepareContentData ($result_item,$full=0,$skip_img=0) {
        $result_item=$this->registry->files->FilePrepare($result_item,'',$skip_img);
	    $result_item['img_url'] = $this->registry->files->GetImageUrl($result_item, 'medium');


        if ($result_item['content_thumb_id3']) {
	        $result_item=$this->registry->files->FilePrepare($result_item,'thumb3_');
	        $result_item['thumb_url'] = $this->registry->files->GetImageUrl($result_item, 'medium',0,'thumb3_');

	        $result_item['thumb'] = $result_item['thumb_url'];
        }

        $add_path='';
        if ($result_item['content_type']=='news') {
            $add_path='news/';
        }
        elseif ($result_item['content_type']=='akcii') {
	        $add_path='akcii/';
        }
        elseif ($result_item['content_type']=='articles') {
	        $add_path='blog/';
        }

        $result_item['content_full_url']=BASE_URL.'/'.$add_path.$result_item['content_url'].'.html';
        $result_item['full_date']=date("d ",$result_item['content_time']).RuMonth(date("m",$result_item['content_time']),0).date(" Y, H:i",$result_item['content_time']);
        $result_item['short_date']=date("d ",$result_item['content_time']).RuMonth(date("m",$result_item['content_time']),0).date(" Y",$result_item['content_time']);

        $url_parts=explode('/',$result_item['content_url']);
        $result_item['last_url_part']=$url_parts[count($url_parts)-1];
        if ($full==1) {
            $result_item['content_text']=$this->registry->text->GetText($result_item['content_text_id']);
        }

        if ($result_item['content_short']) {
            $result_item['content_cut']=CutHeadText($result_item['content_short']);
        }
        elseif ($result_item['content_text']) {
            $result_item['content_cut']=CutHeadText($result_item['content_text']);
        }
	    $result_item['dop']=array();
	    if ($result_item['content_dop']) {
		    $result_item['dop']=explode('|', $result_item['content_dop']);
	    }
	    $result_item['datetime']=gmDate("Y-m-d\TH:i:s\Z",$result_item['content_time']);

        return $result_item;
    }


    function GetNewsCats ($type='news') {
        $array=array();
        $result=$this->GetNewsCatsFromDb($type);
        while ($result_item = $this->db->fetch_array($result))
        {
            $array[$result_item['id']]=$result_item;
        }

        return $array;
    }

    function GetNewsCatsFromDb ($type='news') {
        return $this->db->query_read("SELECT *
        FROM `news_cats`
        WHERE `type`=".$this->db->sql_prepare($type)."
        ORDER BY `title`");
    }

    function GetNewsPeriods () {
        $month=array();
        for ($i=1;$i<=12;$i++) {
            $k=$i;
            if ($i<=9){
                $k='0'.$k;
            }
            $month[$k]=RuMonth($i);
        }
        $years=array();
        for ($i=2016;$i<=date("Y");$i++) {
            $years[$i]=$i;
        }
        return array($month,$years);
    }
    function GetNewsCat ($url) {
        $info=$this->GetNewsCatFromDb($url);
        return $info;
    }
    function GetNewsCatFromDb ($id) {
        return $this->db->query_first("SELECT *
        FROM `news_cats`
        WHERE `id`=".$this->db->sql_prepare($id));
    }
    function GetNewsCatByUrl ($url) {
        $info=$this->GetNewsCatByUrlFromDb($url);
        return $info;
    }
    function GetNewsCatByUrlFromDb ($url) {
        return $this->db->query_first("SELECT *
        FROM `news_cats`
        WHERE `url`=".$this->db->sql_prepare($url));
    }
    function GetTemplatesList () {
        $where=ROOT_DIR.'/app/views/content/custom_pages';
        $array=GetDirListRecursive($where,$where);
        foreach ($array as $key=>$a) {
            $array[$key]=$a;
        }
        return $array;
    }



    function CheckContentItem ($content_id,$item_id) {
    	return $this->db->query_first("SELECT *
    	FROM content_items
    	WHERE content_id=".$this->db->sql_prepare($content_id).' AND item_id='.$this->db->sql_prepare($item_id));
    }
	function DeleteContentItem ($content_id,$item_id) {
		return $this->db->query_write("DELETE FROM content_items
    	WHERE content_id=".$this->db->sql_prepare($content_id).' AND item_id='.$this->db->sql_prepare($item_id));
	}
	function AddContentItem ($content_id,$item_id) {
		$this->db->query_write( "INSERT INTO `content_items`
                (`content_id`,`item_id`)
                VALUES (
                " . $this->db->sql_prepare( $content_id ) . ",
                " . $this->db->sql_prepare( $item_id ) . "
                )
          " );
	}

	function UpdateContentItemsSort ($content_id,$files) {
		$pos=0;
		foreach ($files as $id) {
			$id=intval($id);
			$this->UpdateContentItemSort($content_id,$id,$pos);
			$pos++;
		}
	}
	function UpdateContentItemSort ($content_id,$item_id,$pos) {
		return $this->db->query_write("UPDATE `content_items`
         SET `sort`=".$this->db->sql_prepare($pos)."
         WHERE  `content_id`=".$this->db->sql_prepare($content_id)." AND `item_id`=".$this->db->sql_prepare($item_id));
	}

	function getLastNews($count=8) {
		$filter_options=array();
		$filter_options['content_type']='news';
		$filter_options['show_order']=true;
		return $this->GetContentList($filter_options,$count);
	}

	function getLastArticles($count=8) {
		$filter_options=array();
		$filter_options['content_type']='articles';
		$filter_options['show_order']=true;
		return $this->GetContentList($filter_options,$count);
	}

	function getTaxiLastArticles($limit=12){

		$result = $this->db->query_read("
                SELECT core_content.*, taxi_articles_categories.*,icon_data.file_sizes as icon_file_sizes, icon_data.file_folder as icon_file_folder, icon_data.file_name as icon_file_name FROM core_content
                LEFT JOIN taxi_articles_categories ON core_content.content_type=taxi_articles_categories.art_cat_alias
                LEFT JOIN `core_files` icon_data ON core_content.`content_thumb_id`=icon_data.`file_id`
                WHERE content_type != 'pages'  and content_status=1 and content_thumb_id!=0
                ORDER BY core_content.content_time DESC 
                LIMIT 0, ".$this->db->sql_prepare($limit));

		return $result;
	}

	function getArticles($content_type='all', $start=0, $limit=10){
        if ($content_type == 'all'){
        $result = $this->db->query_read("
                    SELECT *, core_content.*, taxi_articles_categories.*,icon_data.file_sizes as icon_file_sizes, icon_data.file_folder as icon_file_folder, icon_data.file_name as icon_file_name FROM core_content 
                    LEFT JOIN taxi_articles_categories ON core_content.content_type=taxi_articles_categories.art_cat_alias
                    LEFT JOIN core_content_views ON core_content.content_id = core_content_views.id
                    LEFT JOIN `core_files` icon_data ON core_content.`content_thumb_id`=icon_data.`file_id`                    
                    WHERE content_type != 'pages'  and content_status=1 and content_thumb_id!=0
                    ORDER BY core_content.content_time DESC 
                    LIMIT ".$this->db->sql_prepare($start).", ".$this->db->sql_prepare($limit));
        }
        else{
            $result = $this->db->query_read("
            SELECT *, taxi_articles_categories.* FROM core_content 
            LEFT JOIN taxi_articles_categories ON core_content.content_type=taxi_articles_categories.art_cat_alias
            LEFT JOIN core_content_views ON core_content.content_id = core_content_views.id 
            WHERE content_type=".$this->db->sql_prepare($content_type)." and content_status=1
            ORDER BY core_content.content_time DESC
             LIMIT ".$start.", ".$limit." ");
        }
        return $result;
    }

}
