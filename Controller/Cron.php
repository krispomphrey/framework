<?php
class CronController extends Controller{
	public function init(){
		if(in_array($_SERVER['REMOTE_ADDR'], array('46.23.70.149'))){
			switch($this->router->action[1]){
				case 'game':
					for($c=0; $c < 2; $c++){
						$expires = strtotime('-1 day');
						$db = new Database($c);
						$results = $db->select("SELECT * FROM games");
						foreach($results as &$result){
							if((int)$result->date < $expires){
								$db->update("UPDATE games SET archive = 1 WHERE id = $result->id");
							} else {
								$db->update("UPDATE games SET archive = 0 WHERE id = $result->id");
							}
						}
						$db->destroy();
					}
					break;
				case 'regs':
					for($c=0; $c < 2; $c++){
						$db = new Database($c);
						$db->update("UPDATE players SET team_1_status = 0, team_2_status = 0, team_3_status = 0");
						$db->destroy();
					}
					break;
			}
		} else die('Access Denied');
	}
}
