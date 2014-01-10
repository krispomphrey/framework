<?php class AdminModel extends Model{
	public $models = array('Players');
	public function init(){
		$this->pending();
		$this->banned();
		isset($_COOKIE['admin_tab_active']) ? $this->active_tab = $_COOKIE['admin_tab_active'] : $this->active_tab = 'primary';
	}
	public function pending(){
		//var_dump($this->Players);
		$pending_players = array(
			1 => $this->Players->get(array('team_1_status' => 1), 'OR', 'ORDER BY team_1 ASC, name ASC'),
			2 => $this->Players->get(array('team_2_status' => 1), 'OR', 'ORDER BY team_2 ASC, name ASC'),
			3 => $this->Players->get(array('team_3_status' => 1), 'OR', 'ORDER BY team_3 ASC, name ASC')
		);
		$this->pending = $pending_players;
	}
	public function banned(){
		$this->banned = $this->Players->get(array('banned' => '1'));
	}
}
