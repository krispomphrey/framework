<?php
class Model{
	public $table;
	public $db;
	public $models = array();
	public $data;
	private $user;
	public $pagination;
	public $page_break = 50;
	public function debug($data){
		echo '<pre>';
		var_dump($data);
		echo '</pre>';
	}
	public function __construct(){
		$this->user = new Auth();
		$this->db = new Database();
		$this->other_models();
		$this->init();
	}
	public function init(){ }
	public function create($data, $args = array(), $type = 'OR'){
		$cols = null;
		$vals = null;
		$where = array();
		$wf = null;
		if(!empty($args)){
			$wf = 'WHERE ';
			foreach($args as $col => $arg){
				$where[] = " $col = '$arg' ";
			}
			$wf .= implode($type, $where);
		}
		foreach($data as $column => $value){
			if(is_null($value)) $value = 0;
			$cols[] = $column;
			$vals[] = "'$value'";
		}
		$columns = implode(',', $cols);
		$values = implode(',', $vals);
		$this->db->update("INSERT INTO {$this->table} ({$columns}) VALUES ({$values}) {$wf}");
	}
	public function get($args = array(), $type = 'OR', $order = null, $limit = null, $condition = '='){
		$where = array();
		$wf = null;
		if(!empty($args)){
			$wf = 'WHERE ';
			foreach($args as $col => $arg){
				$where[] = " $col $condition '$arg' ";
			}
			$wf .= implode($type, $where);
		}
		$this->data = $this->db->select("SELECT * FROM {$this->table} {$wf} {$order} {$limit}");
		//$this->debug("SELECT * FROM {$this->table} {$wf} {$order} {$limit}");
		if(empty($this->data)) $this->data = false;
	}
	public function save($data, $args = array(), $type = 'OR'){
		$set = null;
		$where = array();
		$wf = null;
		if(!empty($args)){
			$wf = 'WHERE ';
			foreach($args as $col => $arg){
				$where[] = " $col = '$arg' ";
			}
			$wf .= implode($type, $where);
		}
		foreach($data as $column => $value){
			if(is_null($value)) $value = 0;
			$set[] = "$column = '$value'";
		}
		$st = implode(', ', $set);
		$this->db->update("UPDATE {$this->table} SET {$st} {$wf}");
		//$this->debug("UPDATE {$this->table} SET ({$st}) {$wf}");
	}
	public function delete($args = array(), $type = 'OR'){
		$where = array();
		$wf = null;
		if(!empty($args)){
			$wf = 'WHERE ';
			foreach($args as $col => $arg){
				$where[] = " $col = '$arg' ";
			}
			$wf .= implode($type, $where);
		}
		$this->db->delete("DELETE FROM {$this->table} {$wf}");
	}
	private function other_models(){
		if(!empty($this->models)){
			foreach($this->models as $model){
				include_once(MODEL_ROOT."{$model}.php");
				$m = $model.'Model';
				$this->$model = new $m;
			}
		}
	}
	public function pagination($where = null){
		if(isset($_GET['page'])){
			$page = $_GET['page'];
			$paginate = $this->page_break*($page-1);
		} else {
			$page = 1;
			$paginate = 0;
		}
		$count = $this->db->select("SELECT count(id) FROM {$this->table} {$where}", false);
		$pagination = array(
			'count' => $count[0]['count(id)'],
			'pages' => ceil($count[0]['count(id)']/$this->page_break),
			'current' => $page,
			'paginate' => $paginate
		);
		$this->pagination = $pagination;
	}
}
