<?php
/**
*
* info_acp_find [English]
*
* @package phpBB Extension - StopForumSpam
* @copyright (c) 2015 Sheer
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

/**
* @ignore
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine

$lang = array_merge($lang, array(
	'ACL_M_CHK_SFS'				=> 'Can check users via SFS database',
	'ACP_FIND_SPAMER'			=> 'Find Spammer',
	'ACP_FIND_SPAMER_EXPLAIN'	=> 'Here you can search for spammers among your users, ban them (by name, IP address and e-mail) and delete them. This page uses the database resources at <a href="https://www.stopforumspam.com" target="_blank"> www.stopforumspam.com </a>. ',

	'ADD_DATA'		=> 'Add data to the <a href = "https://www.stopforumspam.com" target = "_blank"> stopforumspam </a> database',
	'CHECK_IP'		=> 'There isn\'t a record of this IP address available, but there is a record of the username and e-mail addresse.<br />Check the IP address from which a user leaves a message on the website <a href="https://www.stopforumspam.com "target ="_ blank "> www.stopforumspam.com </a> <br /> this is most likely a spammer! ',
	'CHECK_EMAIL'	=> 'Check Email',
	'CHECK_NICK'	=> 'Check Username',
	'CONFIRM_DELETE'=> 'Are you sure you want to delete these users?',
	'DELETE_SELECTED'=>'Delete all marked',
	'EM_IS_FIND'	=> 'Found an e-mail address or username entry. <b>Maybe its a spammer</b>! Click on the icon and run a full scan.',
	'EM_NOT_FIND'	=> 'Record of the IP address was discovered in the database at <a href=“https://www.stopforumspam.com” target=”_blank ">stopforumspam</a>, but no records for either the address e-mail or username were found. This is possibly not a spammer.   ',
	'F_EXPLAIN'		=> 'Use as a template <b>*</b>, for example <b>*mail.ru</b> or <b>*.169.*.*</b>',
	'EXEC_TIME'		=> 'While searching on this page: %s seconds',
	'FILTER'		=> 'Filter',
	'FIND'			=> 'Entry Found',
	'FULL_CHECK'	=> 'Carry out a full scan of the user',
	'IS_FIND'		=> 'E-mail address and username found. <b> This is probably a spammer </b>!<br />Click on the icon if you want to send user data to the database at <a href="https://www.stopforumspam.com" target="_blank ">stopforumspam</a>.',
	'LIST_USERS'	=> 'Users: %s',
	'NAME'			=> 'Name',
	'NICK_NOT_FIND'	=> 'No record for the username was found.',
	'NO_AUTH'		=> 'You are not authorized to perform this operation',
	'NOT_FIND'		=> 'No users within the period indicated and with these conditions where found. ',
	'NOT_FULL_SEARCH'	=> 'Found a record of the IP address. Suspicious user. Turn modes "<b>check e-mail"</b> and "<b>check username</b>". Use these modes as well, if the IP record was lost. Click on the icon and run a full scan.',
	'NONE_SELECTED'	=> 'Nothing selected!',
	'NO_POSTS_ONLY' => 'Search users with no posts',
	'ORDER'			=> 'Sort Order',
	'PER_DAY'		=> 'Day',
	'PER_WEEK'		=> 'Week',
	'PER_MOUNTH'	=> 'Month',
	'PER_YEAR'		=> 'Year',
	'PER_ALL_TIME'	=> 'All entries',
	'POSSIBLE_NOT'	=> 'Maybe it\'s not a spammer',
	'SEARCH_OPTION'	=> 'Search for',
	'SELECT_SORT'	=> 'Sort by',
	'SORT_EMAIL'	=> 'Email address',
	'SORT_IP'		=> 'IP address',
	'SORT_POST'		=> 'Messages',
	'USER_NAME'		=> 'Username',
	'USER_EMAIL'	=> 'Email Address',
	'UNACTIVE'		=> 'only among non-activated users',
	'WARNING_MESSAGE'	=> '<h3>Be careful this action is irreversible!</h3><p>However, the operation will be backed up on a table <b>users</b> for each individual user. Copies are recorded in the folder <c>"store"</c></p>',
));
