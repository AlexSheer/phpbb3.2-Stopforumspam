<?php
/**
*
* info_acp_find [Russian]
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
	'ACP_FIND_SPAMER'			=> 'Поиск спамеров',
	'ACP_FIND_SPAMER_EXPLAIN'	=> 'Здесь вы можете среди пользователей найти предположительных спамеров, заблокировать их (по имени, IP-адресу и адресу e-mail) и удалить. Поиск осуществляется по IP-адресу, имени пользователя и адресу e-mail. Для поиска используется база данных ресурса <a href="http://www.stopforumspam.com" target="_blank">www.stopforumspam.com</a>. ',

	'ADD'			=> 'Добавить',
	'ADD_DATA'		=> 'Добавить данные в базу <a href = "http://www.stopforumspam.com" target = "_blank">stopforumspam</a>',
	'CHECK_IP'		=> 'Запись об IP-адресе отсутствует, но записи об имени пользователя и адресе e-mail обнаружены.<br />Проверьте IP-адреса, с которых пользователь оставлял сообщения, на сайте <a href = "http://www.stopforumspam.com" target="_blank">www.stopforumspam.com</a><br />Вероятнее всего это спаммер! ',
	'CHECK_EMAIL'	=> 'проверять e-mail',
	'CHECK_NICK'	=> 'проверять имя пользователя',
	'CONFIRM_DELETE'=> 'Вы действительно желаете удалить этих пользователей?',
	'DELETE_SELECTED'=>'Удалить отмеченное',
	'DONE'			=> '<b>Информация добавлена в базу данных www.stopforunspam.<br />Спасибо за сотрудничество!</b>',
	'EMAIL_FIND'	=> 'Запись об адресе e-mail обнаружена <b>%s</b> раз(а).',
	'EMAIL_NOT_FIND'=> 'Запись об адресе e-mail не обнаружена.',
	'EM_IS_FIND'	=> 'Найдены запись об адресе e-mail или имени пользователя. <b>Возможно это спаммер</b>! Кликните мышкой на значок и проведите полную проверку.',
	'EM_NOT_FIND'	=> 'Запись об IP-адресе обнаружена в базе данных <a href = "http://www.stopforumspam.com" target = "_blank">stopforumspam</a> или утрачена в базе данных конференции, но записей об адресе e-mail или имени пользователя не найдено. Возможно это не спаммер.  ',
	'ENTER_APY'		=> 'Введите код API',
	'F_EXPLAIN'		=> 'Используйте в качестве шаблона <b>*</b>, например <b>*mail.ru</b> или <b>*.169.*.*</b>',
	'EXEC_TIME'		=> 'Время поиска на этой странице: %s секунд',
	'FILTER'		=> 'Фильтр',
	'FIND'			=> 'Запись обнаружена',
	'FULL_CHECK'	=> 'Провести полную проверку пользователя',
	'GET_APY_KEY'	=> 'Получить код API',
	'IP_FIND'		=> 'Запись об IP-адресе обнаружена <b>%s</b> раз(а).',
	'IP_NOT_FIND'	=> 'Запись об IP-адресе не обнаружена.',
	'IS_FIND'		=> 'Найдены запись об адресе e-mail и имени пользователя. <b>Это наверняка спамер</b>!<br />Кликните мышкой на значок и, если желаете, отправьте данные о пользователе в базу данных <a href = "http://www.stopforumspam.com" target = "_blank">stopforumspam</a>.',
	'LIST_USERS'	=> 'Пользователей: %s',
	'NAME'			=> 'Ник',
	'NICK_FIND'		=> 'Запись об имени пользователя обнаружена <b>%s</b> раз(а).',
	'NICK_NOT_FIND'	=> 'Запись об имени пользователя не обнаружена.',
	'NO_AUTH'		=> 'У вас нет прав для выполнения этой операции',
	'NOT_FIND'		=> 'Пользователей за указанный период и с этими условиями поиска не обнаружено.',
	'NOT_FULL_SEARCH'	=> 'Обнаружена запись об IP-адресе. Подозрительный пользователь. Включите режимы "<b>проверять e-mail"</b> и "<b>проверять имя пользователя</b>". Воспользуйтесь этими режимами так же в случае, если запись об IP была утрачена. Кликните мышкой на значок и проведите полную проверку.',
	'NONE_SELECTED'	=> 'Ничего не выбрано!',
	'NOT_SPAMMER'	=> 'Это не спаммер!',
	'NO_POSTS_ONLY' => 'только среди не оставивших ни одного сообщения',
	'ORDER'			=> 'Упорядочить',
	'OTHER_IP'		=> 'IP-адреса, с которых пользователь отправлял сообщения',
	'PER_DAY'		=> 'день',
	'PER_WEEK'		=> 'неделю',
	'PER_MOUNTH'	=> 'месяц',
	'PER_YEAR'		=> 'год',
	'PER_ALL_TIME'	=> 'все время',
	'POSSIBLE_NOT'	=> 'Возможно это не спамер',
	'READ_COMMENT'	=> 'Запись об IP-адресе была утрачена',
	'RESUME'		=> 'Резюме',
	'SEARCH_OPTION'	=> 'Искать за',
	'SELECT_SORT'	=> 'Поле сортировки',
	'SORT_EMAIL'	=> 'Адрес e-mail',
	'SORT_IP'		=> 'IP-адрес',
	'SORT_POST'		=> 'Сообщения',
	'SPAM'			=> 'Спаммер',
	'SPAMMER'		=> 'Это спаммер!',
	'SUCSESS_DELETE'=> 'Пользователь(и) успешно удален(ы). Резервные копии сделаны.',
	'USER_NAME'		=> 'Имя пользователя',
	'USER_EMAIL'	=> 'Адрес e-mail',
	'WARNING_MESSAGE'	=> '<h3>Будьте внимательны, действие необратимо!</h3><p>Однако после выполнения операции будет сделана резервная копия для таблицы <b>users</b> для каждого отдельно взятого пользователя. Копии записываются в папку <c>store</c></p>',
));
