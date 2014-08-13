<?php
class block_inactive_user_cleanup_edit_form extends block_edit_form {
    protected function specific_definition($mform) {
       global $CFG,$DB;
        $mform->addElement('header', 'configheader', get_string('setting', 'block_inactive_user_cleanup')); 
        $mform->addElement('text', 'config_daysofinactivity', get_string('daysofinactivity', 'block_inactive_user_cleanup'));
        $mform->addElement('text', 'config_daysbeforedeletion', get_string('daysbeforedeletion', 'block_inactive_user_cleanup'));
        $mform->setDefault('config_daysofinactivity', '365');
        $mform->setType('config_daysofinactivity', PARAM_TEXT);
        $mform->setDefault('config_daysbeforedeletion', '10');
        $mform->setType('config_daysbeforedeletion', PARAM_TEXT);
        $mform->addElement('header', 'config_headeremail', get_string('emailsetting', 'block_inactive_user_cleanup')); 
        $mform->addElement('text', 'config_subjectemail', get_string('emailsubject', 'block_inactive_user_cleanup'));
        $editorfieldoptions = array('trusttext'=>true, 'subdirs'=>true, 'maxfiles'=> 1,
                          'maxbytes'=>1024);
        $mform->addElement('editor', 'config_bodyemail', get_string('emailbody', 'block_inactive_user_cleanup'), $editorfieldoptions);
				$mform->setType('config_subjectemail', PARAM_TEXT);
				$mform->setDefault('config_subjectemail', 'subject');
				$mform->setType('config_bodyemail', PARAM_TEXT);
				$mform->setDefault('config_bodyemail', 'body');
    } 
}