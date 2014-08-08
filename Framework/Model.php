<?php
class Model extends WebApp{
	public $table;
	public $models = array();
	public $data;
	public $pagination;
	public $page_break = 50;
	public function debug($data){
		echo '<pre>';
		var_dump($data);
		echo '</pre>';
	}
	public function __construct(){
		parent::__construct();
		$this->other_models();
		$this->init();
	}
	public function init(){ }
	public function create($statement, $instances){ }
	public function get($statement, $instances){
    $statement['table'] = $this->table;
		$this->data = $this->db->select($statement, $instances);
		if(empty($this->data)) $this->data = false;
	}
	public function save($statement, $instances){ }
	public function delete($statement, $instances){ }
	private function other_models(){
		if(!empty($this->models)){
			foreach($this->models as $model){
				$this->incl(MODEL_ROOT."{$model}.php");
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
