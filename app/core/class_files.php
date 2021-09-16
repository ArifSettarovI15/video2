<?php
use Intervention\Image\ImageManagerStatic as Image;
class FilesClass
{
	/**
	 * @var MainClass
	 */
	var $registry;

	/**
	 * @var DatabaseClass
	 */
	var $db;

	var $upload_dir;
	var $upload_dir_images;
	var $upload_dir_videos;
	var $upload_dir_docs;
	var $upload_images_url;
	var $upload_videos_url;
	var $current_module;
	var $upload_folder;
	var $sizes;

	function __construct ($registry) {
		$this->registry=&$registry;
		$this->db=&$this->registry->db;
		$this->upload_dir=ROOT_DIR.'/uploads';
		$this->upload_dir_images= '/images';
		$this->upload_dir_videos= '/videos';
		$this->upload_dir_docs= '/docs';
		$this->upload_images_url=BASE_URL.'/uploads/images/';
		$this->upload_videos_url=BASE_URL.'/uploads/videos/';
		$this->upload_docs_url=BASE_URL.'/uploads/docs/';
		$this->sizes= array(
			array(
				'name'=>'small',
				'fit'=>1
			),
			array(
				'name'=>'medium'
			),
			array(
				'name'=>'normal'
			),
			array(
				'name'=>'large'
			),
			array(
				'name'=>'original'
			)
		);

	}
	function CheckIsDoc ($type) {
		$formats=array(
			"application/pdf",
			"application/zip",
			"application/gzip",
			"application/msword",
			"text/plain",
			"application/vnd.openxmlformats-officedocument.wordprocessingml.document",
			"application/vnd.oasis.opendocument.text",
			"application/vnd.oasis.opendocument.spreadsheet",
			"application/vnd.oasis.opendocument.presentation",
			"application/vnd.oasis.opendocument.graphics",
			"application/vnd.ms-excel",
			"application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
			"application/vnd.ms-powerpoint",
			"application/vnd.openxmlformats-officedocument.presentationml.presentation",
			"application/msword",
			"application/vnd.openxmlformats-officedocument.wordprocessingml.document",
			"application/x-rar-compressed"
		);
		if (in_array($type, $formats)) {
			return true;
		}
		else {
			return false;
		}
	}
	function CheckIsVideo ($type) {
		$formats=array(
			"video/mpeg",
			"video/mp4",
			"video/ogg",
			"video/quicktime",
			"video/webm",
			"video/x-ms-wmv",
			"video/x-flv",
			"video/3gpp",
			"video/3gpp2",
			"image/webp"
		);
		if (in_array($type, $formats)) {
			return true;
		}
		else {
			return false;
		}
	}
	function CheckIsImage ($type) {
		$image_formats=array(
			"image/gif",
			"image/jpeg",
			"image/pjpeg",
			"image/png",
			"image/svg+xml",
			"image/tiff",
			"image/bmp"
		);
		if (in_array($type, $image_formats)) {
			return true;
		}
		else {
			return false;
		}
	}


	function GetImageSizes () {
		$images_sizes=array(
			'small' => array(
				'width' => 200,
				'height' => 200
			),
			'medium' => array(
				'width' => 360,
				'height' => 360
			),
			'normal' => array(
				'width' => 800,
				'height' => 800
			),
			'large'=>array(
				'width' => 1200,
				'height' => 900
			),
			'original'=>array(
				'width' => 1920,
				'height' => 1920
			)
		);
		return $images_sizes;
	}

	function GenerateFileName ($file_name,$new_name='',$file_ext='') {
		if ($new_name=='') {
			$new_name=GenerateName(8);
		}
		if ($file_ext=='') {
			$file_ext= pathinfo($file_name, PATHINFO_EXTENSION);
		}
		return $new_name . '.' .$file_ext;
	}

	function CheckUploadedFolders ($format='image') {
		if ($format=='image') {
			$this->CheckDir($this->upload_dir_images);
			foreach ($this->sizes as $size) {
				$size_dir = $this->upload_dir_images . '/' . $size['name'];
				$this->CheckDir($size_dir);
				$folder_start = '';
				$f_split = explode('/', $this->upload_folder);
				foreach ($f_split as $f) {
					$folder_start .= '/' . $f;
					$this->CheckDir($size_dir . $folder_start);
				}
			}
		}
		if ($format=='video') {
			$this->CheckDir($this->upload_dir_videos);
			$folder_start = '';
			$dir = $this->upload_dir_videos ;
			$f_split = explode('/', $this->upload_folder);
			foreach ($f_split as $f) {
				$folder_start .= '/' . $f;
				$this->CheckDir($dir . $folder_start);
			}
		}
		if ($format=='docs') {
			$this->CheckDir($this->upload_dir_docs);
			$folder_start = '';
			$dir = $this->upload_dir_docs ;
			$f_split = explode('/', $this->upload_folder);
			foreach ($f_split as $f) {
				$folder_start .= '/' . $f;
				$this->CheckDir($dir . $folder_start);
			}
		}
	}


	function MakePdfPhoto ($from,$to) {
		if (file_exists($from)) {
			$imagick = new Imagick();
			$imagick->readImage($from);
			$imagick->setIteratorIndex(0);
			$imagick->setImageFormat('jpg');

			$upload_folder = 'tmp';
			$this->CheckDir($upload_folder);
			$tmp_file = $this->upload_dir . '/' . $upload_folder . '/' . GenerateName() . '.jpg';
			file_put_contents($tmp_file, $imagick);
			$this->ResizeImage(
				$tmp_file,
				$to,
				150,
				150);
			@unlink($tmp_file);
			return true;
		}
		else {
			return false;
		}
	}

	function ResizeImage ($image_from,$image_to,$width,$height,$type='',$options=array()) {
		if (file_exists($image_from) && filesize($image_from)>0) {

			Image::configure(array('driver' => 'imagick'));



			try{

				$uuu = new Imagick($image_from);
				if ($uuu->getImageFormat() == 'GIF') {
					copy($image_from,$image_to);
					return true;
				}
				elseif ($uuu->valid()) {
					unset($uuu);
					$img = Image::make($image_from);

					list($real_width, $real_height) = getimagesize($image_from);

					if ($type == 'fit') {
						$img->fit($width, $height);
					} else {
						if (($img->width() / $width) > ($img->height() / $height)
						) {
							if ($real_width < $width) {
								$width = $real_width;
							}
							$img->widen($width);
						} else {
							if ($real_height < $height) {
								$height = $real_height;
							}
							$img->heighten($height);
						}
					}
					$status= $img->save($image_to);
					$path_info = pathinfo($image_to);
					$webp_to=str_replace('.'.$path_info['extension'],'.webp',$image_to);
					exec('cwebp -m 4 -q 80 '.$image_to.' -o '.$webp_to);
					if ($options['invert']) {
						$img->invert();
						$ext = pathinfo($image_to, PATHINFO_EXTENSION);
						$image_to=str_replace('.'.$ext,'_black.'.$ext,$image_to);
						$img->save($image_to);
					}


					return $status;
				}
				else {
					return false;
				}
			}
			catch (Exception $ex) {
				return false;
			}

		}
		else {
			return false;
		}
	}

	function MakeDir ($dir) {
		mkdir($this->upload_dir.'/'.$dir);
	}

	function CreateDirDb ($dir) {
		$this->AddFileInDb(
			$dir,
			$this->current_module,
			$this->registry->user_info['user_id'],
			'folder',
			$this->upload_folder,
			serialize(array()),
			time());
	}

	function CheckDir ($dir) {
		if (file_exists($this->upload_dir.'/'.$dir)==false) {
			$this->MakeDir($dir);
		}
	}

	function GetExternalFilename ($external_url){
		$link  = basename($external_url);
		$parts = explode('/', $link);
		$external_filename = end($parts);
		$d=explode('?',$external_filename);
		$external_filename=$d[0];
		return $external_filename;
	}

	function UploadExternalImage ($external_url,$file_name='',$file_ext='jpg',$sizes=array()) {
		if ($external_url!='') {
			$image_data = file_get_contents($external_url);
			if ($image_data != '') {
				$external_filename = $this->GetExternalFilename($external_url);

				$file_name = $this->GenerateFileName($external_filename, $file_name, $file_ext);
				$upload_folder = 'tmp';
				$this->CheckDir($upload_folder);
				$tmp_file = $this->upload_dir . '/' . $upload_folder . '/' . $file_name;
				file_put_contents($tmp_file, $image_data);
				$array = $this->ProcessUploadImage($tmp_file, $file_name, $sizes);
				@unlink($tmp_file);
				return $array;
			} else {
				return array(
					'status' => false,
					'error' => 'Пустой файл'
				);
			}
		}else {
			return array(
				'status' => false,
				'error' => 'URL не указан'
			);
		}
	}

	function UploadFile ($file_data,$file_name='',$file_ext='jpg',$options=array()) {
		$array = array();
		$array['status']=false;

		$to_name=pathinfo($file_data['name'], PATHINFO_FILENAME);

		if ($this->registry->user_info['user_id']==0) {
			$file_name='';
			$to_name='';
		}

		$file_type=mime_content_type($file_data['tmp_name']);

		$format='';

		if ($this->CheckIsImage($file_type)) {
			$format='image';
			if ($file_type=='image/png') {
				$file_ext='png';
			}
			elseif ($file_type=='image/gif') {
				$file_ext='gif';
			}
		}
		elseif ($this->CheckIsVideo($file_type)) {
			$format='video';
		}
		elseif ($this->CheckIsDoc($file_type)) {
			$format='doc';
		}
		else {
			$array['error']='Ошибка формата файла';
		}
		if ($format!='') {
			if (is_array($file_data) && count($file_data) > 0 && $file_data['error'] === UPLOAD_ERR_OK) {
				if ($format=='image') {
					$file_name = $this->GenerateFileName($file_data['name'], $file_name,$file_ext);

					$array = $this->ProcessUploadImage($file_data['tmp_name'], $file_name,array(),$options);
				}
				elseif ($format=='video') {
					$file_name = $this->GenerateFileName($file_data['name'], $file_name);

					$array = $this->ProcessUploadVideo($file_data['tmp_name'], $file_name);
				}
				elseif ($format=='doc') {
					$file_name = $this->GenerateFileName($file_data['name'],$to_name );

					$array = $this->ProcessUploadDocs($file_data['tmp_name'], $file_name);
				}
			} else {
				$error_values = array(
					UPLOAD_ERR_INI_SIZE => 'Размер файла больше разрешенного директивой upload_max_filesize в php.ini',
					UPLOAD_ERR_FORM_SIZE => 'Размер файла превышает указанное значение в MAX_FILE_SIZE',
					UPLOAD_ERR_PARTIAL => 'Файл был загружен только частично',
					UPLOAD_ERR_NO_FILE => 'Не был выбран файл для загрузки',
					UPLOAD_ERR_NO_TMP_DIR => 'Не найдена папка для временных файлов',
					UPLOAD_ERR_CANT_WRITE => 'Ошибка записи файла на диск'
				);

				$error_code = $file_data['error'];

				if (!empty($error_values[$error_code]))
					$array['error'] = $error_values[$error_code];
				else
					$array['error'] = 'Случилось что-то непонятное';
			}
		}
		return $array;
	}
	function ProcessUploadVideo ($file_path,$file_name) {
		$array=array();
		$array['status']=false;
		$up_folder=$this->upload_dir.'/'.$this->upload_dir_videos . '/'  . $this->upload_folder;
		$to=$up_folder . '/' . $file_name;
		if ($this->upload_folder=='') {
			$array['error']='Не указана папка для загрузки файла';
		}
		else {
			$this->CheckUploadedFolders('video');
			$sizes = array(

			);
			if (file_exists($file_path) && move_uploaded_file($file_path, $to)) {
				$array['file_id'] = $this->AddFileInDb(
					$file_name,
					$this->current_module,
					$this->registry->user_info['user_id'],
					'video',
					$this->upload_folder,
					serialize($sizes),
					TIMENOW);
				$array['status'] = true;
			}


		}
		return $array;
	}
	function ProcessUploadDocs ($file_path,$file_name,$pdf_thumb_name='',$uploaded=true) {
		$array=array();
		$array['status']=false;
		$up_folder=$this->upload_dir.$this->upload_dir_docs . '/'  . $this->upload_folder;
		$to=$up_folder . '/' . $file_name;
		$thumb_to=$up_folder . '/' . $pdf_thumb_name;
		if ($this->upload_folder=='') {
			$array['error']='Не указана папка для загрузки файла';
		}
		else {
			$this->CheckUploadedFolders('docs');
			$sizes = array(
				'thumb'=>$pdf_thumb_name
			);


			if ($uploaded) {
				$status=move_uploaded_file($file_path, $to);
			}
			else {
				$status=copy($file_path, $to);
				@unlink($file_path);
			}

			if (file_exists($to) && $status) {
				$finfo = finfo_open(FILEINFO_MIME_TYPE);
				$file_type= finfo_file($finfo, $to);
				finfo_close($finfo);

				if ($this->CheckIsDoc($file_type)) {
					if ($pdf_thumb_name) {
						$this->MakePdfPhoto($to, $thumb_to);
					}

					$array['file_id'] = $this->AddFileInDb(
						$file_name,
						$this->current_module,
						intval($this->registry->user_info['user_id']),
						'docs',
						$this->upload_folder,
						serialize($sizes),
						TIMENOW);
					$array['status'] = true;
				}
			}


		}
		return $array;
	}
	function ProcessUploadImage ($file_path,$file_name,$resize_to=array(),$options=array()) {
		$array=array();
		if (count($resize_to)==0) {
			$resize_to=$this->sizes;
		}
		$array['status']=false;
		$image_format_data = getimagesize($file_path);

		if ($this->upload_folder=='') {
			$array['error']='Не указана папка для загрузки файла';
		}
		elseif (filesize($file_path)==0) {
			$array['error']='Файл пустой';
		}
		elseif ($this->CheckIsImage(intval($image_format_data['width']))==0) {
			$array['error']='Ошибка формата файла';
		}
		else {
			$this->CheckUploadedFolders();
			$image_sizes = $this->GetImageSizes();

			foreach ($resize_to as $size_data) {
				$process_type = '';
				if ($size_data['fit'] == 1) {
					$process_type = 'fit';
				}
				$image_params = $image_sizes[$size_data['name']];

				if ($image_params) {
					if ($this->ResizeImage(
						$file_path,
						$this->upload_dir.'/'.$this->upload_dir_images . '/' . $size_data['name'] . '/' . $this->upload_folder . '/' . $file_name,
						$image_params['width'],
						$image_params['height'],
						$process_type,
						$options)
					) {
						$sizes[] = $size_data['name'];
					}
				}
			}
			if (count($sizes) > 0) {
				$webp=0;
				$path_info = pathinfo($file_name);

				if (file_exists($this->upload_dir.'/'.$this->upload_dir_images . '/original/' . $this->upload_folder . '/' . str_replace('.'.$path_info['extension'],'.webp',$file_name))) {
					$webp=1;
				}
				$array['file_id'] = $this->AddFileInDb(
					$file_name,
					$this->current_module,
					$this->registry->user_info['user_id'],
					'image',
					$this->upload_folder,
					serialize($sizes),
					TIMENOW,
					$webp);
				$array['status'] = true;
			} else {
				$array['error'] = 'Ошибка обработки изображений';
			}
		}
		return $array;
	}

	function AddFileInDb ($file_name,$file_module,$file_user_id,$file_type,$file_folder,$file_sizes,$file_time,$webp=0) {
		$this->db->query_write("INSERT INTO `core_files`
        (`file_name`,`file_module`,`file_user_id`,`file_type`,`file_folder`,`file_sizes`,`file_time`,file_webp)
        VALUES (
        ".$this->db->sql_prepare($file_name).",
          ".$this->db->sql_prepare($file_module).",
          ".intval($file_user_id).",
          ".$this->db->sql_prepare($file_type).",
          ".$this->db->sql_prepare($file_folder).",
          ".$this->db->sql_prepare($file_sizes).",
          ".$this->db->sql_prepare($file_time).",
          ".$this->db->sql_prepare($webp)."
        )");
		return $this->db->insert_id();
	}

	function GetFileInfo ($id){
		$data=$this->GetFileInfoFromDb($id);
		if ($data) {
			$data = $this->FilePrepare($data);
		}
		return $data;
	}


	function FilePrepare ($data,$prefix='',$skip=0) {
		$hh=explode('.',$data[$prefix.'file_name']);
		$data[$prefix.'ext']=strtolower($hh[count($hh)-1]);
		if ($data[$prefix.'ext']=='jpg' or $data[$prefix.'ext']=='jpeg') {
			$data[$prefix.'type']="image/jpeg";
		}
		elseif ($data[$prefix.'ext']=='gif') {
			$data[$prefix.'type']="image/gif";
		}
		elseif ($data[$prefix.'ext']=='png') {
			$data[$prefix.'type']="image/png";
		}

		$all_sizes=$this->GetImageSizes();

		if ($data[$prefix.'file_sizes']!='') {
			$data[$prefix.'sizes'] = unserialize($data[$prefix.'file_sizes']);
			foreach ($data[$prefix . 'sizes'] as $size) {
				$data[$prefix . 'sizes_url'][$size] = $this->GetImageUrl($data, $size,$skip,$prefix);
				if ($data[$prefix.'file_webp']) {
					$data[$prefix . 'sizes_url'][$size."_webp"] = str_replace('.'.$data[$prefix.'ext'],'.webp',$data[$prefix . 'sizes_url'][$size]);
				}

			}
			foreach ($data[$prefix . 'sizes'] as $size) {
				if ( $all_sizes[$size]['width']<1920) {
					if ($data[$prefix.'file_webp']) {
						$data[$prefix . 'sizes_data'][$size]['webp']['url'] = str_replace('.'.$data[$prefix.'ext'],'.webp',$data[$prefix . 'sizes_url'][$size]);
						$data[$prefix . 'sizes_data'][$size]['webp']['type'] = 'image/webp';
						$data[$prefix . 'sizes_data'][$size]['webp']['max'] = $all_sizes[$size]['width'];
					}
					$data[$prefix . 'sizes_data'][$size]['img']['url'] = $this->GetImageUrl($data, $size,$skip,$prefix);
					$data[$prefix . 'sizes_data'][$size]['img']['type'] = $data[$prefix.'type'];
					$data[$prefix . 'sizes_data'][$size]['img']['max'] = $all_sizes[$size]['width'];
				}


			}
		}
		else {
			$data[$prefix.'sizes']=array();
		}

		$data[$prefix.'file_path']=ROOT_DIR.'/uploads'.$this->upload_dir_images.'/original/' .$data[$prefix.'file_folder'] .'/'.$data[$prefix.'file_name'];

		if ($data[$prefix.'file_type']=='video') {
			$data[$prefix.'file_url'] = $this->registry->files->GetVideoUrl($data, $prefix);
		}
		elseif ($data[$prefix.'file_type']=='docs') {
			$data[$prefix.'icon_url'] = BASE_URL.'/assets/images/core/no_photo.jpg';
		}
		else {
			$data[$prefix.'item_icon_url'] = $this->registry->files->GetImageUrl($data, 'medium',$skip);
			$data[$prefix.'icon_url'] = $data[$prefix.'item_icon_url'];
			$data[$prefix.'thumb'] = $data[$prefix.'item_icon_url'];
			$data[$prefix.'cover'] = $this->registry->files->GetImageUrl($data,'large',$skip);
			$data[$prefix.'big_thumb'] = $this->registry->files->GetImageUrl($data,'normal',$skip);
			$data[$prefix.'file_url'] = $this->registry->files->GetImageUrl($data, 'original',$skip);
		}






		return $data;
	}

	function GetFileInfoFromDb ($id){
		return $this->db->query_first("SELECT *
        FROM `core_files`
        WHERE `file_id`=".$this->db->sql_prepare($id));
	}
	function GetVideoUrl($file_info,$prefix=''){
		$url = $this->upload_videos_url.$file_info[$prefix.'file_folder'] .'/'.$file_info[$prefix.'file_name'];
		return $url;
	}
	function GetDocUrl($file_info,$prefix=''){
		$url = $this->upload_docs_url.$file_info[$prefix.'file_folder'] .'/'.$file_info[$prefix.'file_name'];
		return $url;
	}
	function GetImageUrl($file_info,$size,$skip=0,$prefix=''){
		$url='';
		if (is_array($file_info[$prefix.'sizes']) && in_array($size,$file_info[$prefix.'sizes'])) {
			$url = $this->upload_images_url. $size. '/' .$file_info[$prefix.'file_folder'] .'/'.$file_info[$prefix.'file_name'];
		}
		elseif($skip==0) {
			$url=BASE_URL.'/assets/images/core/no_photo.jpg';
		}
		return $url;
	}

	function DeleteFileFromDb($id){
		$this->db->query_write("DELETE FROM `core_files`
        WHERE `file_id`=".$this->db->sql_prepare($id));
	}

	function DeleteImageFromDisk($data){
		$sizes=unserialize($data['file_sizes']);
		if (is_array($sizes)) {
			foreach ($sizes as $size) {
				@unlink($this->upload_dir.'/'.$this->upload_dir_images.'/'.$data['file_folder'] . '/' . $size.'/'.$data['file_name']);
			}
		}
	}
	function DeleteImage ($file_id) {
		$data=$this->GetFileInfo($file_id);
		if ($data) {
			$this->DeleteImageFromDisk($data);
			$this->DeleteFileFromDb($file_id);
			return true;
		}
		else {
			return false;
		}
	}

	function GetFiles ($filter_options=array(), $count=10, $start_page=0){
		$data=array();
		$result=$this->GetFilesFromDb($filter_options,$count,$start_page);
		while ($result_item = $this->db->fetch_array($result))
		{
			$result_item=$this->FilePrepare($result_item);
			$data[$result_item['file_id']]=$result_item;
		}
		return $data;
	}
	function PrepareFilesWhere ($filter_options){
		$sql='';
		if ($filter_options['file_module']!=''){
			if ($sql != '') {
				$sql .= ' AND ';
			} else {
				$sql .= ' WHERE ';
			}
			$sql.="`core_files`.`file_module`=".$this->db->sql_prepare($filter_options['file_module']);
		}

		if ($filter_options['file_folder']!=''){
			if ($sql != '') {
				$sql .= ' AND ';
			} else {
				$sql .= ' WHERE ';
			}
			$sql.="`core_files`.`file_folder`=".$this->db->sql_prepare($filter_options['file_folder']);
		}

		if ($filter_options['order_way']=='asc'){
			$filter_options['order_sort_sql']='ASC';
		}
		else {
			$filter_options['order_sort_sql']='DESC';
		}

		if ($filter_options['order']=='id'){
			$filter_options['order_sql']=' `core_files`.`file_id`';
		}
		else {
			$filter_options['order_sql']=' `core_files`.`file_id`';
		}

		$sql.=" ORDER BY ".$filter_options['order_sql']." ".$filter_options['order_sort_sql'];
		return $sql;
	}
	function GetFilesFromDb ($filter_options,$count,$start_page) {
		$sql=$this->PrepareFilesWhere($filter_options);
		$limit='';
		if ($count!='all') {
			$limit="LIMIT ".$start_page.",".$count;
		}
		return $this->db->query_read("SELECT *
        FROM `core_files`
        ".$sql." ".
		                             $limit);
	}


	function GetFilesTotal ($filter_options=array()) {
		$result=$this->GetFilesTotalFromDb($filter_options);
		return intval($result['count']);
	}

	function GetFilesTotalFromDb($filter_options){
		$sql=$this->PrepareFilesWhere($filter_options);
		return $this->db->query_first("SELECT count(`core_files`.`file_id`) as count
        FROM `core_files`
        ".$sql);
	}

	/*
	 *
	 * OLD
	 */






	function DeleteByTmp($tmp_id){
		$data=$this->GetByTmp($tmp_id);
		foreach ($data as $d) {
			$this->DeleteFromDb($d['file_id']);
			$this->DeleteFile($d);
		}
	}

	function DeleteById($file_id){
		$data=$this->GetFileInfo($file_id);
		$this->DeleteFromDb($data['file_id']);
		$this->DeleteFile($data);
	}





	function GetByTmp($tmp_id){
		$array=array();
		$result=$this->GetByTmpFromDb($tmp_id);
		while ($result_item = $this->db->fetch_array($result))
		{
			$array[$result_item['id']]=$result_item;
		}
		return $array;
	}

	function GetByTmpFromDb ($tmp_id) {
		return $this->db->query_read("SELECT *
        FROM `core_files`
        WHERE `tmp_id`=".$this->db->sql_prepare($tmp_id));
	}
	function GetOneByTmpFromDb ($tmp_id) {
		return $this->db->query_first("SELECT *
        FROM `core_files`
        WHERE `tmp_id`=".$this->db->sql_prepare($tmp_id));
	}
	function SetTmpImageUse ($file_id){
		$this->db->query_write("UPDATE `core_files`
        SET `tmp_ok`=1
        WHERE `file_id`=".$this->db->sql_prepare($file_id));
	}

	function ActivateTmpImage($tmp_id){
		$tmp_image=$this->GetOneByTmpFromDb($tmp_id);
		$image_id=0;
		if (intval($tmp_image['file_id'])>0) {
			$image_id=$tmp_image['file_id'];
			$this->SetTmpImageUse($tmp_image['file_id']);
		}
		return $image_id;
	}



	function DeleteFileIdsItem ($object,$item_id) {
		return $this->db->query_write("DELETE FROM `core_files_ids`
        WHERE `object`=".$this->db->sql_prepare($object)." AND `item_id`=".$this->db->sql_prepare($item_id));
	}
	function AddFileIdsItem  ($object,$item_id,$file_id) {

		$check=$this->db->query_first("SELECT * FROM `core_files_ids`
        WHERE `object`=".$this->db->sql_prepare($object)." AND `item_id`=".$this->db->sql_prepare($item_id)." AND `file_id`=".$this->db->sql_prepare($file_id));
		if ($check==false) {
			return $this->db->query_write("INSERT INTO `core_files_ids`
        (`object`,`item_id`,`file_id`)
        VALUES (
        " . $this->db->sql_prepare($object) . ",
       " . $this->db->sql_prepare($item_id) . ",
         " . $this->db->sql_prepare($file_id) . "
        )");
		}
		else {
			return false;
		}
	}
	function AddFileIdsItems ($object,$item_id,$files_array) {
		$this->DeleteFileIdsItem($object,$item_id);
		foreach ($files_array as $file_id) {
			$this-> AddFileIdsItem ($object,$item_id,$file_id);
		}
	}

	function GetFileIdsItems ($object,$item_id) {
		if ($item_id=='all') {
			return $this->GetFileIdsAllItems ($object);
		}
		else {
			return $this->GetFileIdsItem ($object,$item_id);
		}
	}
	function GetFileIdsItem ($object,$item_id) {
		$array=array();
		$result=$this->GetFileIdsItemsFromDb($object,$item_id);
		while ($result_item = $this->db->fetch_array($result))
		{
			$result_item=$this->FilePrepare($result_item);
			$array[]=$result_item;
		}

		return $array;
	}
	function GetFileIdsAllItems ($object) {
		$array=array();
		$result=$this->GetFileIdsAllItemsFromDb($object);
		while ($result_item = $this->db->fetch_array($result))
		{
			$result_item=$this->FilePrepare($result_item);
			$array[$result_item['item_id']][]=$result_item;
		}

		return $array;
	}
	function GetFileIdsAllItemsFromDb ($object) {
		return $this->db->query_read("SELECT *
        FROM `core_files_ids`
        LEFT JOIN `core_files` ON `core_files_ids`.`file_id`=`core_files`.`file_id`
        WHERE `core_files_ids`.`object`=".$this->db->sql_prepare($object));
	}

	function GetFileIdsItemsFromDb ($object,$item_id) {
		return $this->db->query_read("SELECT *
        FROM `core_files_ids`
        LEFT JOIN `core_files` ON `core_files_ids`.`file_id`=`core_files`.`file_id`
        WHERE `core_files_ids`.`object`=".$this->db->sql_prepare($object)." AND
        `core_files_ids`.`item_id`=".$this->db->sql_prepare($item_id)."
        ORDER BY `core_files_ids`.`sort`");
	}
	function UpdateFileIdsItemsSort ($object,$item_id,$files) {
		$pos=0;
		foreach ($files as $file_id) {
			$file_id=intval($file_id);
			$this->UpdateFileIdsItemSort($object,$item_id,$file_id,$pos);
			$pos++;
		}
	}
	function UpdateFileIdsItemSort ($object,$item_id,$file_id,$pos) {
		return $this->db->query_write("UPDATE `core_files_ids`
         SET `sort`=".$this->db->sql_prepare($pos)."
         WHERE  `object`=".$this->db->sql_prepare($object)." AND `item_id`=".$this->db->sql_prepare($item_id)." AND `file_id`=".$this->db->sql_prepare($file_id));
	}

}
