<?php

class block_inactive_user_cleanup extends block_base {
		
    function init() {
    	if(is_siteadmin()){
    		$this->title = get_string('pluginname', 'block_inactive_user_cleanup');
      }
    }
    function get_content () {
    		if(is_siteadmin()){
					global $USER, $CFG, $DB;
					$this->content =  new stdClass;
					$settingPanelInsert = new stdClass;
					$settingPanelUpdate = new stdClass;
					$this->content->text .= '<P> Today is ' . date("d-m-y") . '</P>';
					$this->content->text .= '<P> Run cron script : ';
					$cronUrl = new moodle_url('/admin/cron.php');
					$this->content->text .= html_writer::link($cronUrl,get_string('runcron','block_inactive_user_cleanup'));
					$this->content->text .= '</P>';
					$interval = date("d-m-y",$USER->lastlogin) - date("d-m-y");
					if (! empty($this->config->daysofinactivity)) {
						$settingPanelInsert->daysbeforedeletion = $this->config->daysbeforedeletion;
						$settingPanelInsert->daysofinactivity = $this->config->daysofinactivity;
						$settingPanelInsert->emailsubject = $this->config->subjectemail;
						$settingPanelInsert->emailbody = $this->config->bodyemail['text'];
						$count = $DB->count_records('block_inactive_user_cleanup');
						$id = $DB->get_records_sql('select id as ids from {block_inactive_user_cleanup}');
						foreach($id as $updateid) {
							$settingPanelUpdate->id = $updateid->ids;
						}
						$settingPanelUpdate->daysbeforedeletion = $this->config->daysbeforedeletion;
						$settingPanelUpdate->daysofinactivity = $this->config->daysofinactivity;
						$settingPanelUpdate->emailsubject = $this->config->subjectemail;
						$settingPanelUpdate->emailbody = $this->config->bodyemail['text'];
						if($count == 0) {
							$DB->insert_record('block_inactive_user_cleanup',$settingPanelInsert);
						}
						else {
							$DB->update_record('block_inactive_user_cleanup',$settingPanelUpdate);
						}
					}
					return $this->content;
				}
    }
    public function cron() {
    	if(is_siteadmin()){
				global $DB,$CFG;
				mtrace( "Hey, my inactive user cleanup is running" );
				$emailSetting = $DB->get_records('block_inactive_user_cleanup');
				foreach($emailSetting as $emailSettingDetails) {
					$inactivity = $emailSettingDetails->daysofinactivity;
					$beforedelete = $emailSettingDetails->daysbeforedeletion;
					$subject = $emailSettingDetails->emailsubject;
					$body = $emailSettingDetails->emailbody;
				}
				$users = $DB->get_records( 'user' );
				foreach($users as $usersDetails) {
					$subject = $emailSettingDetails->emailsubject;
					$messageText = html_to_text($emailSettingDetails->emailbody);
					if(date("d-m-y",$usersDetails->lastlogin) - date("d-m-y") > $inactivity) {
							if ($mail_results = email_to_user($usersDetails, $users, $subject, $messageText)) {
								mtrace('email sent');
							}
					}
					if($beforedelete != 0) {
						$deleted = 1;
						$userName = $usersDetails->email . '.' . time();
						$letters = 'abcefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
						$deleteEmail = substr(str_shuffle($letters),0,32);
						if((date("d-m-y",$usersDetails->lastlogin) - date("d-m-y")) >= ($inactivity + $beforedelete)) {
							$sql = 'update {user} set deleted = ?, username = ?, email = ? where id = ?';
							$DB->execute($sql,array($deleted, $userName, $deleteEmail, $usersDetails->id));
							mtrace('delete user' . $usersDetails->id);
						}
					}
				}
				return true;
			}
    }
}


