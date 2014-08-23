Inactive User Clean-up for Moodle

Inactive User Cleanup plugin deletes inactive user accounts. Its cleanup process runs on Moodle cron job. 

The plugin works in two steps: 

Step 1: The admin sets up days of inactivity and drafts notification mails for all users from the 
‘Configuring A Inactive User’ block. 
If inactive users are found, a notification mail is sent to them. 

Step 2: If the user still remains inactive from the Moodle site within the time span mentioned in the notification mail, 
the deletion process starts. 
With the next run of the cleanup process, the particular inactive user account entry gets removed.

Inactive User Cleanup plugin deletes inactive user accounts. Its cleanup process runs on Moodle cron job. 

The plugin works in two steps: 

Step 1: The admin sets up days of inactivity and drafts notification mails for all users from the 
‘Configuring A Inactive User’ block. 
If inactive users are found, a notification mail is sent to them. 

Step 2: If the user still remains inactive from the Moodle site within the time span mentioned in the notification mail, 
the deletion process starts. 
With the next run of the cleanup process, the particular inactive user account entry gets removed.


To install, place all files in /blocks/inactive_user_cleanup and visit /admin/index.php in your browser.

This block is written by Dualcube <admin@dualcube.com>.
