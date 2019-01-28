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
	'ACP_FIND_SPAMER_EXPLAIN'	=> 'Здесь вы можете среди пользователей найти предположительных спамеров, заблокировать их (по имени, IP-адресу и адресу e-mail) и удалить. Поиск осуществляется по IP-адресу, имени пользователя и адресу e-mail. Для поиска используется база данных ресурса <a href="https://www.stopforumspam.com" target="_blank">www.stopforumspam.com</a>. ',

	'CHECK_EMAIL'	=> 'проверять e-mail',
	'CHECK_IP'		=> 'Запись об IP-адресе отсутствует, но записи об имени пользователя и адресе e-mail обнаружены.<br />Проверьте IP-адреса, с которых пользователь оставлял сообщения, на сайте <a href = "https://www.stopforumspam.com" target="_blank">www.stopforumspam.com</a><br />Вероятнее всего это спамер! ',
	'CHECK_NICK'	=> 'проверять имя пользователя',
	'CHK_FAIL_EXPLAIN'	=> 'Если строка в списке выделена этим цветом, значит не удалось получить данные с сервера <a href = "https://www.stopforumspam.com" target = "_blank">stopforumspam.com</a>.',
	'CONFIRM_DELETE'=> 'Вы действительно желаете удалить этих пользователей?',
	'DELETE_SELECTED'=>'Удалить отмеченное',
	'EM_IS_FIND'	=> 'Найдены запись об адресе e-mail или имени пользователя. <b>Возможно это спамер</b>! Кликните мышкой на значок и проведите полную проверку.',
	'EM_NOT_FIND'	=> 'Запись об IP-адресе обнаружена в базе данных <a href = "https://www.stopforumspam.com" target = "_blank">stopforumspam</a> или утрачена в базе данных конференции, но записей об адресе e-mail или имени пользователя не найдено. Возможно это не спамер.  ',
	'EXEC_TIME'		=> 'Время поиска на этой странице: %s секунд',
	'F_EXPLAIN'		=> 'Используйте в качестве шаблона <b>*</b>, например <b>*mail.ru</b> или <b>*.169.*.*</b>',
	'FILTER'		=> 'Фильтр',
	'FIND'			=> 'Запись обнаружена.',
	'FULL_CHECK'	=> 'Провести полную проверку пользователя',
	'IS_FIND'		=> 'Найдены запись об адресе e-mail и имени пользователя. <b>Это наверняка спамер</b>!<br />Кликните мышкой на значок и, если желаете, отправьте данные о пользователе в базу данных <a href = "https://www.stopforumspam.com" target = "_blank">stopforumspam</a>.',
	'LIST_USERS'	=> 'Пользователей: %s',
	'NAME'			=> 'Ник',
	'NO_AUTH'		=> 'У вас нет прав для выполнения этой операции',
	'NO_POSTS_ONLY' => 'только среди не оставивших ни одного сообщения',
	'NONE_SELECTED'	=> 'Ничего не выбрано!',
	'NOT_FIND'		=> 'Пользователей за указанный период и с этими условиями поиска не обнаружено.',
	'NOT_FULL_SEARCH'	=> 'Обнаружена запись об IP-адресе. Подозрительный пользователь. Включите режимы "<b>проверять e-mail"</b> и "<b>проверять имя пользователя</b>". Воспользуйтесь этими режимами так же в случае, если запись об IP была утрачена. Кликните мышкой на значок и проведите полную проверку.',
	'ORDER'			=> 'Упорядочить',
	'PER_ALL_TIME'	=> 'все время',
	'PER_DAY'		=> 'день',
	'PER_MOUNTH'	=> 'месяц',
	'PER_WEEK'		=> 'неделю',
	'PER_YEAR'		=> 'год',
	'SAVE'			=> 'Сохранить',
	'SEARCH_OPTION'	=> 'Искать за',
	'SELECT_SORT'	=> 'Поле сортировки',
	'SORT_EMAIL'	=> 'Адрес e-mail',
	'SORT_IP'		=> 'IP-адрес',
	'SORT_POST'		=> 'Сообщения',
	'SUCSESS_DELETE'=> 'Пользователь(и) успешно удален(ы). Резервные копии сделаны.',
	'USER_CHK'		=> 'Записей в базе данных не обнаружено, однако, если вы считаете, что это спамер, кликните мышкой на значок, если желаете отправить данные о пользователе в базу данных <a href = "https://www.stopforumspam.com" target = "_blank">stopforumspam</a>.',
	'USER_EMAIL'	=> 'Адрес e-mail',
	'USER_NAME'		=> 'Имя пользователя',
	'WARNING_MESSAGE'	=> '<h3>Будьте внимательны, действие необратимо!</h3><p>Однако после выполнения операции будет сделана резервная копия для таблицы <b>users</b> для каждого отдельно взятого пользователя. Копии записываются в папку <c>store</c></p>',
));
