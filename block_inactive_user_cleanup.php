<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Inactive User Cleanup block
 * @package   block inactive user cleanup
 * @copyright 2014 dualcube  {@link http://dualcube.com/}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class block_inactive_user_cleanup extends block_base {
    public function init() {
        if (is_siteadmin()) {
            $this->title = get_string('pluginname', 'block_inactive_user_cleanup');
        }
    }
    public function get_content() {
        if (is_siteadmin()) {
            global $USER, $CFG, $DB;
            $this->content = new stdClass;
            $settingpanelinsert = new stdClass;
            $settingpanelupdate = new stdClass;
            $this->content->text .= '<P> Today is ' . date("d-m-y") . '</P>';
            $this->content->text .= '<P> Run cron script :';
            $cronurl = new moodle_url('/admin/cron.php');
            $this->content->text .= html_writer::link($cronurl, get_string('runcron', 'block_inactive_user_cleanup'));
            $this->content->text .= '</P>';
            $interval = date("d-m-y", $USER->lastlogin) - date("d-m-y");
            if (!empty($this->config->daysofinactivity)) {
                $settingpanelinsert->daysbeforedeletion = $this->config->daysbeforedeletion;
                $settingpanelinsert->daysofinactivity = $this->config->daysofinactivity;
                $settingpanelinsert->emailsubject = $this->config->subjectemail;
                $settingpanelinsert->emailbody = $this->config->bodyemail['text'];
                $count = $DB->count_records('block_inactive_user_cleanup');
                $id = $DB->get_records_sql('select id as ids from {block_inactive_user_cleanup}');
                foreach ($id as $updateid) {
                    $settingpanelupdate->id = $updateid->ids;
                }
                $settingpanelupdate->daysbeforedeletion = $this->config->daysbeforedeletion;
                $settingpanelupdate->daysofinactivity = $this->config->daysofinactivity;
                $settingpanelupdate->emailsubject = $this->config->subjectemail;
                $settingpanelupdate->emailbody = $this->config->bodyemail['text'];
                if ($count == 0) {
                    $DB->insert_record('block_inactive_user_cleanup', $settingpanelinsert);
                } else {
                    $DB->update_record('block_inactive_user_cleanup', $settingpanelupdate);
                }
            }
            return $this->content;
        }
    }
    public function cron() {
        if (is_siteadmin()) {
            global $DB, $CFG;
            mtrace("Hey, my inactive user cleanup is running");
            $emailsetting = $DB->get_records('block_inactive_user_cleanup');
            foreach ($emailsetting as $emailsettingdetails) {
                $inactivity = $emailsettingdetails->daysofinactivity;
                $beforedelete = $emailsettingdetails->daysbeforedeletion;
                $subject = $emailsettingdetails->emailsubject;
                $body = $emailsettingdetails->emailbody;
            }
            $users = $DB->get_records('user');
            foreach ($users as $usersdetails) {
                $subject = $emailsettingdetails->emailsubject;
                $messagetext = html_to_text($emailsettingdetails->emailbody);
                if (date("d-m-y", $usersdetails->lastlogin) - date("d-m-y") > $inactivity) {
                    if ($mailresults = email_to_user($usersdetails, $users, $subject, $messagetext)) {
                        mtrace('email sent');
                    }
                }
                if ($beforedelete != 0) {
                    $deleted = 1;
                    $username = $usersdetails->email . '.' . time();
                    $letters = 'abcefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
                    $deleteemail = substr(str_shuffle($letters), 0, 32);
                    if ((date("d-m-y", $usersdetails->lastlogin) - date("d-m-y")) >= ($inactivity + $beforedelete)) {
                        $sql = 'update {user} set deleted = ?, username = ?, email = ? where id = ?';
                        $DB->execute($sql, array($deleted, $username, $deleteemail, $usersdetails->id));
                        mtrace('delete user' . $usersdetails->id);
                    }
                }
            }
            return true;
        }
    }
}


