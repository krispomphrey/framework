<?php require_once('plugins/PHPMailer.php');
class Mail extends WebApp{
	public $from = array('email' => 'webmaster@scottishicehockey.net', 'title' => 'Scottish Ice Hockey System');
	public $subject = '';
	public $message = '';
	public $recievers = array();
	public $to = array();
	public $attachment;
	public $type;
	public $data;
	public $config;
	public $mailer;
	public $db;
	public function __construct(){
		$this->mailer = new PHPMailer();
		$this->mailer->From = $this->from['email'];
		$this->mailer->FromName = $this->from['title'];
	}
	public function type($type){
		if($type != 99){
			$mail = array_pop($this->db->select("SELECT * FROM emails WHERE type = $type"));
			$this->subject = $mail->subject;
			$this->message = $mail->message;
			if($mail->attachment || !empty($mail->attachment)) $this->attachment = UPLOAD_ROOT.'/attachments/'.$mail->attachment;
			$this->config = array(
				'managers' => (int)$mail->managers,
				'gcs' => (int)$mail->gcs,
				'admin' => (int)$mail->admin,
				'users' => (int)$mail->users,
				'referees' => 0
			);
		}
		switch($type){
			// Player Reg emails
			case 0 :
				$replace = array(
					'{{team}}' => $this->data->team_1->name,
					'{{name}}' => $this->data->name,
					'{{licence}}' => $this->data->licence,
					'{{banned_games}}' => $this->data->banned_games,
					'{{photo}}' => "http://{$_SERVER['HTTP_HOST']}/uploads/players/{$this->data->image}",
					'{{dob}}' => $this->data->dob
				);
				foreach($replace as $key => $value){
					$this->subject = str_replace($key, $value, $this->subject);
					$this->message = str_replace($key, $value, $this->message);
				}
				$acl = 'OR level = 2';
				break;
            case 1 :
				$search = array(
					'{{team}}',
					'{{name}}',
					'{{licence}}',
					'{{banned_games}}',
					'{{photo}}',
					'{{dob}}'
				);
				$message_parts = explode('{{loop}}', $this->message);
				$subject = $this->subject;
				foreach($this->data as $tid => $team){
					$this->recievers = array();
					$this_team = null;
					$this->pull_reciepts('OR level = 2', "WHERE id = $tid", null, "team = $tid");
					//var_dump($this->recievers);
					$this_team = $this->db->select("SELECT * FROM teams WHERE id = '{$tid}'");
					//var_dump("SELECT * FROM teams WHERE id = {$tid}");
					//var_dump($this_team[0]);
					$this->subject = str_replace('{{team}}', $this_team[0]->name, $subject);
					$message_parts[0] = str_replace('{{team}}', $this_team[0]->name, $message_parts[0]);
					$loop = array();
					foreach($team as $player){
						$replace = array(
							$this_team[0]->name,
							$player->name,
							$player->licence,
							$player->banned_games,
							"http://{$_SERVER['HTTP_HOST']}/uploads/players/{$player->image}",
							$player->dob
						);
						$loop[] = str_replace($search, $replace, $message_parts[1]);
					}
					$mid_section = implode('',$loop);
					$this->message = $message_parts[0].$mid_section.$message_parts[2];
					//var_dump($this->subject);
					//var_dump($this->message);
					$this->send();
				}
				break;
            case 2 :
				if(!is_array($this->data)){
					$team = "team_{$this->data->this_team}";
					$player = $this->data;
					//var_dump($player);
					$replace = array(
						'{{team}}' => $player->$team->name,
						'{{name}}' => $player->name,
						'{{licence}}' => $player->licence,
						'{{banned_games}}' => $player->banned_games,
						'{{photo}}' => "http://{$_SERVER['HTTP_HOST']}/uploads/players/{$player->image}",
						'{{dob}}' => $player->dob
					);
					$this->message = str_replace(array('{{loop}}'), array(''), $this->message);
					foreach($replace as $key => $value){
						$this->subject = str_replace($key, $value, $this->subject);
						$this->message = str_replace($key, $value, $this->message);
					}
				} else {
					$this_team = $this->data['this_team'];
					unset($this->data['this_team']);
					$search = array(
						'{{team}}',
						'{{name}}',
						'{{licence}}',
						'{{banned_games}}',
						'{{photo}}',
						'{{dob}}'
					);
					$this->subject = str_replace('{{team}}', $this_team->name, $this->subject);
					$message_parts = explode('{{loop}}', $this->message);
					$message_parts[0] = str_replace('{{team}}', $this_team->name, $message_parts[0]);
					$loop = array();
					foreach($this->data as $player){
						$replace = array(
							$this_team->name,
							$player->name,
							$player->licence,
							$player->banned_games,
							"http://{$_SERVER['HTTP_HOST']}/uploads/players/{$player->image}",
							$player->dob
						);
						$loop[] = str_replace($search, $replace, $message_parts[1]);
					}
					$mid_section = implode('',$loop);
					$this->message = $message_parts[0].$mid_section.$message_parts[2];
				}
				$acl = 'OR level = 2';
				$tl = null;
				$refs = null;
				break;
			// Ban emails
            case 3 :
            case 4 :
				$replace = array(
					'{{team}}' => $this->data->team_1->name,
					'{{name}}' => $this->data->name,
					'{{licence}}' => $this->data->licence,
					'{{banned_games}}' => $this->data->banned_games,
					'{{photo}}' => "http://{$_SERVER['HTTP_HOST']}/uploads/players/{$this->data->image}",
					'{{dob}}' => $this->data->dob
				);
				foreach($replace as $key => $value){
					$this->subject = str_replace($key, $value, $this->subject);
					$this->message = str_replace($key, $value, $this->message);
				}
				$team = "WHERE id = {$this->data->team_1->id} ";
				$tl = "OR team = {$this->data->team_1->id} ";
				for($t=2;$t<=3;$t++){
					$tm = "team_{$t}";
					if($this->data->$tm || $this->data->$tm > 0){
						$team .= "OR id = {$this->data->$tm->id} ";
						$tl .= "OR team = {$this->data->$tm->id} ";
					}
				}
				$acl = "OR level = 1";
				$refs = null;
				break;
			// Game emails
            case 5 :
            case 6 :
            case 7 :
            case 8 :
            case 9 :
            case 11 :
				$game = $this->data;
				$search = array(
					'{{crdate}}',
					'{{date}}',
					'{{faceoff}}',
					'{{rink}}',
					'{{type}}',
					'{{home_team}}',
					'{{away_team}}',
					'{{notes}}',
					'{{ref_1}}',
					'{{ref_2}}',
					'{{ref_3}}'
				);
				$replace = array(
					date('d/m/Y'),
					date('d/m/Y', $game->date),
					$game->faceoff,
					$game->rink->name,
					$game->type,
					$game->team_home->name,
					$game->team_away->name,
					$game->notes,
					$game->referee_1->name,
					$game->referee_2->name,
					$game->referee_3->name
				);
				$this->subject = str_replace($search, $replace, $this->subject);
				$this->message = str_replace($search, $replace, $this->message);
				$team = "WHERE id = {$game->team_home->id} OR id = {$game->team_away->id}";
				$tl = "team = {$game->team_home->id} OR team = {$game->team_away->id}";
				//var_dump($tl);
				$this->config['referees'] = 1;
                $acl = 'OR level = 3';
				for($i=1;$i<=3;$i++){
					$r = 'referee_'.$i;
					if(isset($game->$r) || !empty($game->$r)){
						if($i == 1) $refs = 'WHERE ';
						else $refs .= " OR ";
						$refs .= "id = {$game->$r->id}";
					}
				}
                break;
			case 12:
				$this->config['referees'] = 1;
				$acl = null;
				$team = null;
				$tl = null;
				$game = $this->data['game'];
				$search = array(
					'{{crdate}}',
					'{{date}}',
					'{{faceoff}}',
					'{{type}}',
					'{{rink}}',
					'{{home_team}}',
					'{{away_team}}',
					'{{notes}}'
				);
				$replace = array(
					$game->crdate,
					date('d/m/Y', $game->date),
					$game->faceoff,
					$game->type,
					$game->rink->name,
					$game->team_home->name,
					$game->team_away->name,
					$game->notes
				);
				$this->subject = str_replace($search, $replace, $this->subject);
				$this->message = str_replace($search, $replace, $this->message);
				$refs = "WHERE id = '{$this->data['ref']}'";
				break;
			case 99:
				//var_dump($this->config);
				$acl = true;
				$team = true;
				$refs = true;
				$tl = true;
				break;
			default:
				$acl = null;
				$team = null;
				$refs = null;
				$t = null;
				$this->config = array(
					'managers' => 0,
					'gcs' => 0,
					'admin' => 0,
					'users' => 0,
					'referees' => 0
				);
		}
		$this->pull_reciepts($acl, $team, $refs, $tl);
	}
	public function pull_reciepts($acl = null, $team = null, $ref = null, $team_info = null){
		if($this->config['managers'] == 1 && $team){
			if(is_bool($team)) $team = '';
			$emails = $this->db->select("SELECT manager_email FROM teams $team");
			//var_dump("SELECT manager_email FROM teams $team");
			////var_dump($this->db->error);
			foreach($emails as $manager) if(!empty($manager->manager_email)) $this->recievers[] = $manager->manager_email;
		}
		if($this->config['gcs'] == 1 && $team){
			if(is_bool($team)) $team = '';
			$emails = $this->db->select("SELECT gc_email FROM teams $team");
			//var_dump("SELECT gcs_email FROM teams $team");
			//var_dump($this->db->error);
			foreach($emails as $gcs) if(!empty($gcs->gcs_email)) $this->recievers[] = $gcs->gcs_email;
		}
		if($this->config['admin'] == 1 && $acl){
			if(is_bool($acl)) $acl = '';
			$emails = $this->db->select("SELECT email FROM users WHERE (level = 99 $acl) AND notify = 1 AND league = {$this->db->db}");
			//var_dump("SELECT email FROM users WHERE (level = 99 $acl) AND notify = 1 AND league = {$this->db->db}");
			//var_dump($this->db->error);
			foreach($emails as $admin) $this->recievers[] = $admin->email;
		}
		if($this->config['users'] == 1 && $team_info){
			if(is_bool($team_info)) $team_info = 'level = 1';
			$emails = $this->db->select("SELECT email FROM users WHERE level = 0 AND {$team_info} AND notify = 1 AND league = {$this->db->db}");
			//var_dump("SELECT email FROM users WHERE level = 0 AND ({$team_info}) AND notify = 1 AND league = {$this->db->db}");
			//var_dump($this->db->error);
			foreach($emails as $user) $this->recievers[] = $user->email;
		}
		if($this->config['referees'] == 1 && $ref){
			$emails = $this->db->select("SELECT email FROM referees $ref");
			//var_dump("SELECT email FROM referees $ref");
			foreach($emails as $user) $this->recievers[] = $user->email;
		}
	}
	public function send(){
		$this->mailer->ClearAllRecipients();
		$this->recievers = array_unique($this->recievers);
		//var_dump($this->recievers);
		foreach($this->recievers as $email){
			$this->mailer->AddBCC($email);
			//$this->mailer->AddBCC('dev@tkfnetwork.co.uk');
		}
		//var_dump($this->mailer);
		if($this->attachment || !empty($this->attachment)) $this->mailer->AddAttachment($this->attachment);
		$this->mailer->Subject = $this->subject;
		$this->mailer->MsgHTML($this->message);
		$this->mailer->send();
	}
}
