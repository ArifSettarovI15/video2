<?php

class DbDataColumns {
	public function getFieldsNames() {
		$array=array();
		foreach ((array)$this as $value) {
			$array[]=$value->getName();
		}
		return $array;
	}
	public function getFields() {
		return (array)$this;
	}
	public function setFields() {
		return (array)$this;
	}
}


class DbColumn {
	private $name;
	private $type;
	private $value;
	private $search=false;
	private $dop;

	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @param string $name
	 */
	public function setName( $name ) {
		$this->name = $name;
	}

	/**
	 * @return int
	 */
	public function getType() {
		return $this->type;
	}

	/**
	 * @param int $type
	 */
	public function setType( $type ) {
		$this->type = $type;
	}

	/**
	 * @return mixed
	 */
	public function getValue() {
		return $this->value;
	}

	public function unsetValue() {
		unset($this->value);
	}
	public function setValue( $value ) {
		$this->value = $value;
	}
	public function notValue( $value ) {
		$this->value = $value;
		$this->dop = 'not';
	}
	public function moreValue( $value ) {
		$this->value = $value;
		$this->dop = 'more';
	}
	public function lessValue( $value ) {
		$this->value = $value;
		$this->dop = 'less';
	}
	public function rangeValue( $min,$max ) {
		$this->value = array(
			'min'=>$min,
			'max'=>$max
		);
		$this->dop = 'range';
	}
	public function inValue( $value ) {
		$this->value[] = $value;
		$this->dop = 'in';
	}
	public function inValues( $value ) {
		if (count($value)==1) {
			$this->setValue(array_values($value)[0]);
			$this->dop = '';
		}
		else {
			$this->value = $value;
			$this->dop = 'in';
		}
	}
	/**
	 * @return bool
	 */
	public function isSearch() {
		return $this->search;
	}

	/**
	 * @param bool $search
	 */
	public function setSearch( $search ) {
		$this->search = $search;
	}

	/**
	 * @return mixed
	 */
	public function getDop() {
		return $this->dop;
	}

	/**
	 * @param mixed $dop
	 */
	public function setDop( $dop ) {
		$this->dop = $dop;
	}

}


class DbDataModel {

	// table
	private $table_name='';
	private $table_item_prefix='';
	private $table_primary_key='';

	private $select_fields=array();
	private $joins=array();
	/**
	 * @var  DbDataColumns $columns_where
	 */
	public $columns_where;
	/**
	 * @var  DbDataColumns $columns_update
	 */
	public $columns_update;


	private $count;
	private $start=0;
	private $where=array();
	private $where_custom=array();
	private $or_custom='';
	private $order_by=array();
	private $order_way=array();
	private $group_by=array();
	private $group_way=array();


	function __construct()
	{
		$this->Init();
		$this->InitDop();
	}
	public function Init () {

	}

	public function InitDop () {
		$this->columns_where=new DbDataColumns();
		$this->columns_update=new DbDataColumns();
	}



	public function GetTableItemNameSimple($name)
	{
		return $this->table_item_prefix.$name;
	}

	public function GetTableItemName($name)
	{
		return $this->table_name.".`".$this->table_item_prefix.$name."`";
	}

	/**
	 * @return string
	 */
	public function getTableName() {
		return $this->table_name;
	}

	/**
	 * @param string $table_name
	 */
	public function setTableName( $table_name ) {
		$this->table_name = $table_name;
	}

	/**
	 * @param string $table_item_prefix
	 */
	public function setTableItemPrefix( $table_item_prefix ) {
		$this->table_item_prefix = $table_item_prefix;
	}


	/**
	 * @return int
	 */
	public function getCount() {
		return $this->count;
	}

	/**
	 * @param int $count
	 */
	public function setCount( $count ) {
		if (is_int($count)) {
			$this->count = $count;
		}
	}

	/**
	 * @return int
	 */
	public function getStart() {
		return $this->start;
	}

	/**
	 * @param int $start
	 */
	public function setStart( $start ) {
		if (is_int($start)) {
			$this->start = $start;
		}
	}
	/**
	 * @return mixed
	 */
	public function getOrderBy() {
		return $this->order_by;
	}


	/**
	 * @param mixed $order_by
	 */
	public function setOrderByWithColumn( $order_by ) {
		$fields=$this->columns_where->getFieldsNames();
		if (in_array($order_by,$fields)) {
			$this->order_by[] =$this->GetTableItemName($order_by) ;
		}
	}

	public function deleteOrderBy( ) {
		$this->order_by = array();
	}

	/**
	 * @param mixed $order_by
	 */
	public function setOrderBy( $order_by ) {
		$this->order_by[] = $order_by;
	}

	/**
	 * @return mixed
	 */
	public function getOrderWay() {
		return $this->order_way;
	}

	/**
	 * @param mixed $order_way
	 */
	public function setOrderWay( $order_way ) {
		if ($order_way=='ASC' OR $order_way=='DESC'){
			$this->order_way[] = $order_way;
		}

	}

	/**
	 * @return mixed
	 */
	public function getGroupBy() {
		return $this->group_by;
	}

	/**
	 * @param mixed $group_by
	 */
	public function setGroupCustom( $group_by ) {
		$this->group_by[] =$group_by ;
	}

	/**
	 * @param mixed $group_by
	 */
	public function setGroupByWithColumn( $group_by ) {
		$this->group_by[] =$this->GetTableItemName($group_by) ;
	}

	/**
	 * @param mixed $group_by
	 */
	public function setGroupBy( $group_by ) {
		$this->group_by[] = $group_by;
	}

	/**
	 * @return mixed
	 */
	public function getGroupWay() {
		return $this->group_way;
	}

	/**
	 * @param mixed $group_way
	 */
	public function setGroupWay( $group_way ) {
		if ($group_way=='ASC' OR $group_way=='DESC'){
			$this->group_way[] = $group_way;
		}

	}

	/**
	 * @return string
	 */
	public function getTablePrimaryKey() {
		return $this->table_primary_key;
	}

	/**
	 * @param string $table_primary_key
	 */
	public function setTablePrimaryKey( $table_primary_key ) {
		$this->table_primary_key = $table_primary_key;
	}

	/**
	 * @return array
	 */
	public function getSelectFields() {
		return $this->select_fields;
	}

	/**
	 * @param array $select_fields
	 */
	public function setSelectFields( $select_fields ) {
		$this->select_fields = $select_fields;
	}


	public function setSelectField( $select_field ) {
		$this->select_fields[] = $select_field;
	}

	/**
	 * @return array
	 */
	public function getJoins() {
		return $this->joins;
	}

	/**
	 * @param array $joins
	 */
	public function setJoins( $joins ) {
		$this->joins = $joins;
	}

	public function setJoin( $join ) {
		$this->joins[] = $join;
	}

	/**
	 * @param $select
	 * @param $join
	 */
	public function setJoinData( $select,$join ) {
		$this->select_fields[] = $select;
		$this->joins[] = $join;
	}

	public function JoinImage ($prefix,$join_on) {
		$data= new stdClass;
		$data->select=' '.$prefix.'_data.file_sizes as '.$prefix.'_file_sizes, '.$prefix.'_data.file_folder as '.$prefix.'_file_folder, '.$prefix.'_data.file_name as '.$prefix.'_file_name ';
		$data->join=' LEFT JOIN `core_files` '.$prefix.'_data ON '.$join_on.'='.$prefix.'_data.`file_id` ';
		return $data;
	}
	public function SetJoinImage ($prefix,$join_on) {
		$data=$this->JoinImage($prefix,$join_on);
		$this->setJoinData($data->select,$data->join);
	}

	/**
	 * @return array
	 */
	public function getWhere() {
		return $this->where;
	}

	/**
	 * @param array $where
	 */
	public function setWhere( $where ) {
		$this->where = $where;
	}


	public function addWhere( $where ) {
		$this->where[] = $where;
	}

	/**
	 * @return array
	 */
	public function getWhereCustom() {
		return $this->where_custom;
	}

	/**
	 * @param array $where_custom
	 */
	public function setWhereCustom( $where_custom ) {
		$this->where_custom = $where_custom;
	}
	public function addWhereCustom( $where_custom ) {
		if ($where_custom!='') {
			$this->where_custom[] = $where_custom;
		}
	}

	/**
	 * @return string
	 */
	public function getOrCustom(): string {
		return $this->or_custom;
	}

	/**
	 * @param string $or_custom
	 */
	public function setOrCustom( string $or_custom ): void {
		$this->or_custom = $or_custom;
	}

}

class DbData
{
	/**
	 * @var MainClass
	 */
	var $registry;

	var $db;

	/**
	 * @var  DbDataModel $model
	 */
	var $model;


	var $sql_types=array(
		QUERY_TYPE_SELECT=>'select',
		QUERY_TYPE_FIRST=>'select_first',
		QUERY_TYPE_COUNT=>'select_count',
		QUERY_TYPE_MINMAX=>'select_minmax',
		QUERY_TYPE_DELETE=>'delete',
		QUERY_TYPE_UPDATE=>'update'
	);

	function __construct(&$registry)
	{
		$this->registry =& $registry;
		$this->db =& $this->registry->db;
	}

	public function CreateModel () {
		$this->model=new DbDataModel();
	}

	/**
	 *
	 * @return string
	 */
	private function SqlLimit(){
		$sql="";

		if (intval($this->model->getCount())>0) {
			$sql="LIMIT ".$this->model->getStart().",".$this->model->getCount();
		}
		return $sql;
	}

	/**
	 * @param bool $raw
	 *
	 * @return string
	 */
	private function SqlGroup($raw=false){
		$sql="";

		$group_array=array();
		$a1=$this->model->getGroupBy();
		$a2=$this->model->getGroupWay();
		if (is_array($a1)) {
			foreach ($a1 as $key=>$a) {
				if ($a!=='') {
					$group_array[] =$a." ".$a2[$key];
				}
			}
		}

		if (count($group_array)>0) {
			$sql="GROUP BY ".implode(', ',$group_array);
		}

		return $sql;
	}

	/**
	 * @param bool $raw
	 *
	 * @return string
	 */
	private function SqlOrder($raw=false){
		$sql="";

		$order_array=array();
		$a1=$this->model->getOrderBy();
		$a2=$this->model->getOrderWay();
		if (is_array($a1)) {
			foreach ($a1 as $key=>$a) {
				if ($a!=='') {
						$order_array[] =$a." ".$a2[$key];
				}
			}
		}

		if (count($order_array)>0) {
			$sql="ORDER BY ".implode(', ',$order_array);
		}

		return $sql;
	}

	private function MakeInsertSql ($array=array()){
		if (count($array)>0) {
			return '('.implode(',',array_keys($array)).')
			 VALUES (
			 '.implode(',',array_values($array)).'
			 )';
		}
		else {
			return '';
		}
	}
	private function MakeUpdateSql ($array=array()){
		if (count($array)>0) {
			return 'SET '.implode(', ',$array);
		}
		else {
			return '';
		}
	}

	private function MakeWhereSql (){
		$array=array_merge(
			$this->model->getWhere(),
			$this->model->getWhereCustom()
		);

		$r='WHERE '.implode(' AND ',$array);

		if ($this->model->getOrCustom() and count($array)>0){
			$r='WHERE ('.implode(' AND ',$array).') OR '.$this->model->getOrCustom();
		}

		if (count($array)>0) {
			return $r;
		}
		else {
			return '';
		}
	}


	private function PrepareInSql ($array) {
		$sql=false;
		$safe_array=array();

		if (is_array($array)) {
			foreach ($array as $a) {
				$safe_array[]=$this->db->sql_prepare($a);
			}

			if (count($safe_array)>0) {
				$sql=implode(',',$safe_array);
			}
		}
		return $sql;
	}


	public function SqlWherePrepare ($type,$key,$value,$dop=''){
		if ($type=='simple') {
			if ($dop=='not') {
				return $key.'!='.$this->db->sql_prepare($value);
			}
			elseif ($dop=='more') {
				return $key.'>='.$this->db->sql_prepare($value);
			}
			elseif ($dop=='less') {
				return $key.'<='.$this->db->sql_prepare($value);
			}
			elseif ($dop=='range') {
				$sql='';
				if ($value['min']>0 or $value['max']>0) {
					$sql='(';

					if ($value['min']>0) {
						$sql.=$key.'>='.$this->db->sql_prepare($value['min']);
					}

					if ($value['max']>0) {
						if ($value['min']>0) {
							$sql.=' AND ';
						}
						$sql.=$key.'<='.$this->db->sql_prepare($value['max']);
					}
					$sql.=')';
				}
				return $sql;
			}
			elseif ($dop=='in') {
				if (count($value)==1) {
					return $key.'='.$this->db->sql_prepare(array_values($value)[0]);
				}
				else {
					$value=$this->PrepareInSql($value);
					if ($value) {
						return $key.' IN ('.$value.') ';
					}
				}

				return false;

			}
			else {
				return $key.'='.$this->db->sql_prepare($value);
			}

		}
		elseif ($type=='like') {
			if ($dop=='not') {
				$exp=' NOT LIKE ';
			}
			else {
				$exp=' LIKE ';
			}
			return $key.' '.$exp.' "%'.$this->db->escape_string_like($value).'%"';
		}
		return false;
	}

	/**
	 *
	 * @return string
	 */
	public function SqlWhere (){
		$where_array=array();
		/** @var DbColumn $column_value */

		foreach ($this->model->columns_where->getFields() as $key=>$column_value) {
			if (is_null($column_value->getValue())===false) {
				if ($column_value->getType()==TYPE_UINT OR $column_value->getType()==TYPE_INT OR $column_value->getType()==TYPE_NUM OR $column_value->getType()==TYPE_UNUM) {
					$type='simple';
				}
				elseif ($column_value->getType()==TYPE_STR OR $column_value->getType()==TYPE_NOHTML) {
					if ($column_value->isSearch() and $column_value->getValue()!='') {
						$type='like';
					}
					else {
						$type='simple';
					}
				}
				elseif ($column_value->getType()==TYPE_BOOL ){
					$type='simple';
				}
				else {
					$type='simple';
				}

				if ($type) {
					$sql_query=$this->SqlWherePrepare($type,$this->model->GetTableItemName($column_value->getName()),$column_value->getValue(),$column_value->getDop());
					if ($sql_query){
						$where_array[]=$sql_query;
					}
				}
			}
		}

		$this->model->setWhere($where_array);

		return $this->MakeWhereSql();
	}

	/**
	 *
	 * @return string
	 */
	private function SqlUpdate (){
		$sql_array=array();

		/** @var DbColumn $column_value */
		foreach ($this->model->columns_update->GetFields() as $key=>$column_value) {
			if (is_null($column_value->getValue())===false) {
				$sql_query=$this->model->GetTableItemName($column_value->getName())."=".$this->db->sql_prepare($column_value->getValue());
				if (is_null($sql_query)===false){
					$sql_array[]=$sql_query;
				}
			}
		}

		return $this->MakeUpdateSql($sql_array);
	}

	/**
	 *
	 * @return string
	 */
	private function SqlInsert (){
		$sql_array=array(

		);

		/** @var DbColumn $column_value */
		foreach ($this->model->columns_update->GetFields() as $key=>$column_value) {

			if (is_null($column_value->getValue())===false) {
				$sql_query_1=$this->model->GetTableItemName($column_value->getName());
				$sql_query_2=$this->db->sql_prepare($column_value->getValue());
				if (is_null($sql_query_1)===false && is_null($sql_query_2)===false){
					$sql_array[$sql_query_1]=$sql_query_2;
				}
			}
		}

		return $this->MakeInsertSql($sql_array);
	}


	/**
	 * @param bool|int $type
	 * @param string $custom_query
	 *
	 * @return bool|string
	 */
	private function SqlQuery ($type=false,$custom_query='') {
		$select='';

		$select_array=$this->model->getSelectFields();
		$update='';
		$join_array=array();
		$where=$this->SqlWhere();

		$group=$this->SqlGroup();
		$order=$this->SqlOrder();

		$limit='';
		if ($type==QUERY_TYPE_SELECT) {
			$limit=$this->SqlLimit();
		}

		if ($type==QUERY_TYPE_MINMAX) {
			if ($custom_query) {
				$select="SELECT MIN(".$custom_query.") as min, MAX(".$custom_query.") as max FROM ";
				$join_array=$this->model->getJoins();
				$group='';
				$order='';
				$limit='';
			}
		}
		elseif ($type==QUERY_TYPE_COUNT) {
			$select="SELECT count(*) as count FROM ";
			$join_array=$this->model->getJoins();
			$order='';
			$limit='';
		}
		elseif ($type==QUERY_TYPE_UPDATE) {
			$select='UPDATE ';
			$update=$this->SqlUpdate();
		}
		elseif ($type==QUERY_TYPE_INSERT) {
			$select='INSERT INTO ';
			$update=$this->SqlInsert();
			$where='';
			$limit='';
			$order='';
		}
		elseif ($type==QUERY_TYPE_DELETE) {
			$select="DELETE FROM ";
		}
		elseif ($type==QUERY_TYPE_SELECT OR $type==QUERY_TYPE_FIRST) {
			if (count($select_array)>0) {
				$select='SELECT '.implode(',',$select_array).' FROM ';
			}
			else {
				$select='SELECT * FROM ';
			}
			$join_array=$this->model->getJoins();
		}
		elseif ($type==QUERY_TYPE_CUSTOM) {
			return $custom_query;
		}
		$join=implode('
			',$join_array);
		if ($select!='') {

			return $select."
        ".$this->model->getTableName()."
        ".$update."
        ".$join."
        ".$where."
        ".$group."
        ".$order."
        ".$limit;
		}
		else {
			return false;
		}

	}


	/**
	 * @param int $type
	 * @param string $custom_query
	 *
	 * @return bool|mixed|mysqli_result|string
	 */
	private function GetSqlResult ($type=QUERY_TYPE_FIRST,$custom_query='') {


		$sql=$this->SqlQuery($type,$custom_query);


		if ($sql) {
			if ($type==QUERY_TYPE_FIRST) {
				return $this->db->query_first($sql);
			}
			elseif ($type==QUERY_TYPE_COUNT) {
				$group=$this->SqlGroup();
				if ($group) {
					$n=$this->db->query_read($sql);
					return $this->db->num_rows($n);
				}
				else {
					return $this->db->query_first($sql);
				}
			}
			elseif ($type==QUERY_TYPE_MINMAX) {
				return $this->db->query_first($sql);
			}
			elseif ($type==QUERY_TYPE_SELECT) {
				return $this->db->query_read($sql);
			}
			elseif ($type==QUERY_TYPE_DELETE) {
				return $this->db->query_write($sql);
			}
			elseif ($type==QUERY_TYPE_UPDATE) {
				return $this->db->query_write($sql);
			}
			elseif ($type==QUERY_TYPE_CUSTOM) {
				return $this->db->query_read($sql);
			}
			elseif ($type==QUERY_TYPE_INSERT) {
				$status=$this->db->query_write($sql);
				if ($status) {
					return $this->db->insert_id();
				}
				else {
					return $status;
				}
			}
			else {
				return false;
			}
		}
		else {
			return false;
		}
	}

	public function PrepareData ($result_item,$full=0) {

		return $result_item;
	}

	public function GetItem ($full=0) {
		$result_item=$this->GetSqlResult(QUERY_TYPE_FIRST);
		if ($result_item) {
			$result_item = $this->PrepareData($result_item, $full);
		}
		return $result_item;
	}

	public function CustomQuery ($query) {
		return $this->GetSqlResult(QUERY_TYPE_CUSTOM,$query);
	}

	/**
	 * @return bool|mixed|mysqli_result|string
	 */
	public function GetListSimple () {
		return $this->GetSqlResult(QUERY_TYPE_SELECT);
	}

	/**
	 *
	 * @return array
	 */
	public function GetList () {
		$array=array();
		$result=$this->GetSqlResult(QUERY_TYPE_SELECT);
		while ($result_item = $this->db->fetch_array($result))
		{

			$result_item=$this->PrepareData($result_item);
			$id=false;
			$key=$this->model->getTablePrimaryKey();
			if ($key){
				$id=$key;
			}

			if ($id) {
				$array[$result_item[$id]]=$result_item;
			}
			else {
				$array[]=$result_item;
			}
		}

		return $array;
	}


	public function GetTotal () {
		$result=$this->GetSqlResult(QUERY_TYPE_COUNT);
		if ($result and is_array($result)) {
			return intval($result['count']);
		}
		elseif ($result and is_int($result)) {
			return $result;
		}
		else {
			return 0;
		}

	}


	public function GetMinMax ($elem) {
		$elem=$this->model->GetTableItemName($elem);
		$result=$this->GetSqlResult(QUERY_TYPE_MINMAX,$elem);
		return array(
			'min'=>intval($result['min']),
			'max'=>intval($result['max'])
		);

	}
	/**
	 *
	 * @return array
	 */
	public function Delete () {
		return $this->GetSqlResult(QUERY_TYPE_DELETE);
	}
	/**
	 *
	 * @return array
	 */
	public function Update (){
		return $this->GetSqlResult(QUERY_TYPE_UPDATE);
	}
	/**
	 *
	 * @return array
	 */
	public function Insert (){
		return $this->GetSqlResult(QUERY_TYPE_INSERT);
	}


	/**
	 * @param $result bool|mysqli_result
	 *
	 * @return array
	 */
	public function ToArray ($result) {
		$array=array();
		while ($result_item = $this->db->fetch_array($result))
		{
			$array[]=$result_item;
		}
		return $array;
	}
}
