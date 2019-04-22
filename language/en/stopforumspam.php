<?php
/**
*
* @package phpBB Extension - Stop spamer register
* @copyright (c) 2017 Sheer
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

/**
* DO NOT CHANGE
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

$lang = array_merge($lang, array(
	'ADD'			=> 'Add',
	'ADD_AND_DELETE'=> 'Delete user and add data',
	'ADD_DATA'		=> 'Add data to database <a href = "https://www.stopforumspam.com" target = "_blank">stopforumspam</a>',
	'CONNECTION_ERROR'	=> 'Failed to retrieve data from <a href = "https://www.stopforumspam.com" target = "_blank">stopforumspam.com</a>',
	'DONE'			=> '<strong> Information added to the database at www.stopforunspam.com <br /> Thank you for your help! </strong>',
	'EMAIL_FIND'	=> 'Record of e-mail address found <b>%s</b> times.',
	'EMAIL_NOT_FIND'=> 'No record of the e-mail address was found.',
	'ENTER_APY'		=> 'Enter the API code',
	'FAIL_ADD_DATA'	=> 'Failed to add data - possibly invalid API code specified.',
	'GET_APY_KEY'	=> 'Get API key',
	'IP_FIND'		=> 'Record of the IP address found <b>%s</b> times.',
	'IP_NOT_FIND'	=> 'No record of the IP address was found.',
	'NICK_FIND'		=> 'Record found for the username <b>%s</b> times.',
	'NICK_NOT_FIND'	=> 'No record for the username was found.',
	'NOT_SPAMMER'	=> 'This is not a spammer!',
	'OTHER_IP'		=> 'IP addresses from which the user has sent messages',
	'POSSIBLE_NOT'	=> 'Maybe it\'s not a spammer',
	'POSSIBLE_YES'	=> 'Most likely this is a spammer',
	'READ_COMMENT'	=> 'No IP address found',
	'RESUME'		=> 'Summary',
	'SFS'			=> 'Is a spammer?',
	'SFS_INFO'		=> 'Answer stopforunspam.com',
	'SPAM'			=> 'Spammer',
	'SPAMMER'		=> 'This is a spammer!',
	'SUCSESS_DELETE'=> 'User has been successfully deleted. Backups are done.',
));
