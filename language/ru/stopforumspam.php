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
	'ADD'			=> 'Добавить',
	'ADD_AND_DELETE'=> 'Удалить пользователя и добавить данные',
	'ADD_DATA'		=> 'Добавить данные в базу <a href = "https://www.stopforumspam.com" target = "_blank">stopforumspam</a>',
	'CONNECTION_ERROR'	=> 'Не удалось получить данные с сервера <a href = "https://www.stopforumspam.com" target = "_blank">stopforumspam.com</a>',
	'DONE'			=> '<b>Информация добавлена в базу данных www.stopforunspam.com<br />Спасибо за сотрудничество!</b>',
	'EMAIL_FIND'	=> 'Запись об адресе e-mail обнаружена <b>%s</b> раз(а).',
	'EMAIL_NOT_FIND'=> 'Запись об адресе e-mail не обнаружена.',
	'ENTER_APY'		=> 'Введите код API',
	'FAIL_ADD_DATA'	=> 'Не удалось добавить данные - возможно указан недействительный код API.',
	'GET_APY_KEY'	=> 'Получить код API',
	'IP_FIND'		=> 'Запись об IP-адресе обнаружена <b>%s</b> раз(а).',
	'IP_NOT_FIND'	=> 'Запись об IP-адресе не обнаружена.',
	'NICK_FIND'		=> 'Запись об имени пользователя обнаружена <b>%s</b> раз(а).',
	'NICK_NOT_FIND'	=> 'Запись об имени пользователя не обнаружена.',
	'NOT_SPAMMER'	=> 'Скорее всего это не спамер',
	'OTHER_IP'		=> 'IP-адреса, с которых пользователь отправлял сообщения',
	'POSSIBLE_NOT'	=> 'Возможно это не спамер',
	'READ_COMMENT'	=> 'Запись об IP-адресе была утрачена.',
	'RESUME'		=> 'Резюме',
	'SFS'			=> 'Это спамер?',
	'SFS_INFO'		=> 'Ответ stopforunspam.com',
	'SPAM'			=> 'Спамер',
	'SPAMMER'		=> 'Это спамер!',
	'SUCSESS_DELETE'=> 'Пользователь успешно удален. Резервная копия сделана.',
));
