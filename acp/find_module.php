<?php
/**
*
* @package phpBB Extension - Find Spamer
* @copyright (c) 2015 Sheer
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace sheer\stopforumspam\acp;

class find_module
{
	var $u_action;

	function main($id, $mode)
	{
		global $db, $user, $template, $cache, $request, $phpbb_container;
		global $config, $phpbb_root_path, $phpEx;
		include ($phpbb_root_path . 'includes/functions_user.' . $phpEx);

		$id			= $request->variable('t', 0);
		$start		= $request->variable('start', 0);
		$delmarked	= $request->variable('delmarked', false);
		$filter		= $request->variable('filter', '', true);
		$filter_key	= $request->variable('f_opt', 1);
		$no_post	= $request->variable('no_posts', '');
		$default_key = 'a';
		$sort_key = $request->variable('sk', $default_key);
		$sort_dir = $request->variable('sd', 'a');

		$full_check	= $request->variable('full_check', false);
		$ip			= $request->variable('ip', '');
		$ch_user	= $request->variable('ch_user', '');
		$whois		= $request->variable('whois', false);
		$action		= $request->variable('f', '');

		$users = $request->variable('id_list', array(0));
		$fail_chhk = false;

		$filter_options = array(1 => 'ip', 2 => 'email');
		$per_page = $config['topics_per_page'] = 6;

		$this->tpl_name = 'acp_find_body';
		$this->page_title = $user->lang('ACP_FIND_SPAMER');

		if ($full_check == true)
		{
			$this->full_check($ch_user);
		}

		if ($action == 'd')
		{
			$this->done();
		}

		if ($whois == true)
		{
			$this->who($ip);
		}

		// Sorting and order
		$order_by = '';
		$sort_key_text = array('a' => $user->lang['SORT_JOINED'], 'b' => $user->lang['SORT_USERNAME'], 'c' => $user->lang['SORT_IP'], 'd' => $user->lang['SORT_POST'], 'e' => $user->lang['SORT_EMAIL'], 'l' => $user->lang['LAST_VISIT']);
		$sort_key_sql = array('a' => 'user_regdate', 'b' => 'username_clean', 'c' => 'user_ip',  'd' => 'user_posts', 'e' => 'user_email', 'l' => 'user_lastvisit');
		$sort_dir_text = array('a' => $user->lang['ASCENDING'], 'd' => $user->lang['DESCENDING']);

		if (!isset($sort_key_sql[$sort_key]))
		{
			$sort_key = $default_key;
		}

		$s_sort_key = '';
		foreach ($sort_key_text as $key => $value)
		{
			$selected = ($sort_key == $key) ? ' selected="selected"' : '';
			$s_sort_key .= '<option value="' . $key . '"' . $selected . '>' . $value . '</option>';
		}

		$s_sort_dir = '';
		foreach ($sort_dir_text as $key => $value)
		{
			$selected = ($sort_dir == $key) ? ' selected="selected"' : '';
			$s_sort_dir .= '<option value="' . $key . '"' . $selected . '>' . $value . '</option>';
		}

		$s_filter_key = '';
		foreach ($filter_options as $key => $value)
		{
			$selected = ($filter_key == $key) ? ' selected="selected"' : '';
			$s_filter_key .= '<option value="' . $key . '"' . $selected . '>' . $value . '</option>';
		}

		$pagination	= $phpbb_container->get('pagination');
		$pagination_url = $this->u_action. '&amp;filter=' . $filter . '&amp;f=' . $action . '&amp;no_posts=' . $no_post . '&amp;sd=' . $sort_dir . '&amp;sk=' . $sort_key . '&amp;f_opt='. $filter_key .'';

		$sql_where = ($filter) ? ' AND user_' . $filter_options[$filter_key] . ' ' . $db->sql_like_expression(str_replace('*',$db->get_any_char(), $filter)) . '' : '';
		$sql_where .= ($no_post) ? ' AND user_posts = 0' : '';
		$order_by = ' ORDER BY ' . $sort_key_sql[$sort_key] . ' ' . (($sort_dir == 'a') ? 'ASC' : 'DESC') .' ';

		if ($delmarked)
		{
			if (confirm_box(true))
			{
				if(sizeof($users))
				{
					$sql = 'SELECT user_id, user_email, username, user_ip
						FROM ' . USERS_TABLE . '
						WHERE ' . $db->sql_in_set('user_id', $users) . $sql_where . ''. $order_by;
					$result = $db->sql_query_limit($sql, $per_page, $start);
					$i = 0;
					while ($row = $db->sql_fetchrow($result))
					{
						$this->backup($users[$i]);
						user_ban('email', $row['user_email'], 0, 0, 0, $user->lang['SPAM'], $user->lang['SPAM']);
						user_ban('user', $row['username'], 0, 0, 0, $user->lang['SPAM'], $user->lang['SPAM']);
						if($row['user_ip'])
						{
							user_ban('ip', $row['user_ip'], 0, 0, 0, $user->lang['SPAM'], $user->lang['SPAM']);
						}
						user_delete('remove', $users[$i]);
						add_log('admin', 'LOG_USER_DELETED', $row['username']);
						$i++;
					}
					$msg = $user->lang['SUCSESS_DELETE'];
				}

				meta_refresh(3, $pagination_url);
				trigger_error($msg . '<br /><br />' . sprintf($user->lang['RETURN_PAGE'], '<a href="' . $pagination_url . '">', '</a>'));
			}
			else
			{
				if (empty($users))
				{
					$msg = $user->lang['NONE_SELECTED'];
					meta_refresh(3, $pagination_url);
					trigger_error($msg . '<br /><br />' . sprintf($user->lang['RETURN_PAGE'], '<a href="' . $pagination_url . '">', '</a>'), E_USER_WARNING);
				}

				confirm_box(false, $user->lang['CONFIRM_DELETE'], build_hidden_fields(array(
					'id_list'		=> $users,
					'delmarked'		=> $delmarked,
					))
				);
			}
		}
		else
		{
			$current_time = time();
			$day = 86400;
			$week = $day * 7;
			$mounth = $day * 30;
			$year = $mounth * 12;

			switch ($action)
			{
				case 0:
					$option = $user->lang['PER_DAY'];
					$period = $current_time - $day;
				break;
				case 1:
					$option = $user->lang['PER_WEEK'];
					$period = $current_time - $week;
				break;
				case 2:
					$option = $user->lang['PER_MOUNTH'];
					$period = $current_time - $mounth;
				break;
				case 3:
					$option = $user->lang['PER_YEAR'];
					$period = $current_time - $year;
				break;
				case 4:
					$option = $user->lang['PER_ALL_TIME'];
					$period = 0;
				break;
				default:
					$option = $user->lang['PER_DAY'];
					$period = $current_time - 86400;
				break;
			}

			$time_start = $this->getmicrotime();
			$sql = 'SELECT count(user_id) AS total
				FROM '. USERS_TABLE .'
				WHERE user_type != ' . USER_IGNORE . ' AND user_type != ' . USER_FOUNDER . ' AND user_regdate > ' . $period . ' '
				. $sql_where;
			$result = $db->sql_query($sql);
			$total_users =  $db->sql_fetchfield('total');
			$db->sql_freeresult($result);

			$sql = 'SELECT user_id, username, user_ip, user_email, user_regdate, user_posts, user_lastvisit
					FROM '. USERS_TABLE .'
					WHERE user_type != ' . USER_IGNORE . ' AND user_type != ' . USER_FOUNDER . '
					AND user_regdate > ' . $period . '
					' . $sql_where . ''. $order_by;
			$result = $db->sql_query_limit($sql, $per_page, $start);

			while ($row = $db->sql_fetchrow($result))
			{
				$ip = $row['user_ip'];
				$uname = $row['username'];
				$ch_data = array(
					$row['username'],
					$row['user_ip'],
					$row['user_email']
				);

				$em = $nick = false;

				$res = $this->check_stopforumspam($ch_data);
				if (!is_array($res[0]))
				{
					//trigger_error($i_data, E_USER_WARNING);
					$res = array();
					$fail_chhk = true;
				}

				if (sizeof($res))
				{
					foreach ($res[0] as $key => $value)
					{
						switch ($key)
						{
							case 'username':
								($value == 'yes') ? $nick = true : $nick = false;
								break;
							case 'ip':
								($value == 'yes') ? $banned_ip = true : $banned_ip = false;
								break;
							case 'email':
								($value == 'yes') ? $em = true : $em = false;
								break;
						}
					}
				}
				if (!isset($banned_ip))
				{
					$banned_ip = false;
				}
				if ($em || $nick)
				{
					$class = 'icon buton em_spam';
					if ($em && $nick && $banned_ip)
					{
						$class = 'icon buton spam';
					}
				}
				else if (empty($ip) || $banned_ip)
				{
					$class = 'icon buton ip';
				}
				if ($em && $nick)
				{
					$class = 'icon buton spam';
				}

				$template->assign_block_vars('row', array(
					'USER_ID'		=> $row['user_id'],
					'IS_FIND'		=> ($banned_ip || $em || $nick || empty($ip))? true : false,
					'CLASS'			=> (isset($class)) ? $class : '',
					'SPAM_MAIL'		=> ($em) ? true : false,
					'SPAM_NICK'		=> ($nick) ? true : false,
					'IP_IMG'		=> ($banned_ip) ? 'icon buton find' : 'icon buton ip',
					'S_IP_FIND'		=> ($banned_ip || empty($ip)) ? true : false,

					'USER_REG_DATE'	=> $user->format_date($row['user_regdate']),
					'LAST_VISIT'	=> ($row['user_lastvisit']) ? $user->format_date($row['user_lastvisit']) : $user->lang['NEVER'],
					'USER_NAME'		=> '<a href="' . append_sid("{$phpbb_root_path}memberlist.$phpEx", 'mode=viewprofile&amp;u=' . intval($row['user_id'])) . '">' . $row['username'] .'</a>',
					'USER_EMAIL'	=> $row['user_email'],
					'USER_POSTS'	=> $row['user_posts'],
					'USER_IP'		=> (!empty($row['user_ip'])) ? $row['user_ip'] : $user->lang['READ_COMMENT'],
					'U_POSTS'		=> append_sid("{$phpbb_root_path}search.$phpEx", 'author_id=' . $row['user_id'] . '&sr=posts'),
					'S_USER_IP'		=> (!empty($row['user_ip'])) ? $this->u_action . '&amp;whois=true&amp;ip=' . $row['user_ip'] . '' : '',
					'U_FULL_CHECK'	=> $this->u_action. '&amp;full_check=true&ch_user=' . $row['user_id'],
					'S_FAIL_CHK'	=> $fail_chhk,
				));
			}
			$db->sql_freeresult($result);

			$pagination->generate_template_pagination($pagination_url, 'pagination', 'start', $total_users, $config['topics_per_page'], $start);
			$time_end = $this->getmicrotime();
			$time = $time_end - $time_start;

			$template->assign_vars(array(
				'S_MODE_SELECT'		=> $s_sort_key,
				'S_ORDER_SELECT'	=> $s_sort_dir,
				'FILTER'			=> $filter,
				'FILTER_OPTIONS'	=> $s_filter_key,
				'NOPOSTS'			=> ($no_post) ? true : false,
				'FILTER_OPTIONS'	=> $s_filter_key,
				'TOTAL_USERS'		=> ($total_users) ? $user->lang('LIST_USERS', (int) $total_users) : '',
				'EXEC_TIME'			=> sprintf($user->lang['EXEC_TIME'], $time),
				'S_ACTION'			=> $pagination_url,
			));
			$this->jump_to($action);
		}
	}

	function who($ip)
	{
		global $template, $phpbb_root_path, $phpEx;

		$this->tpl_name = 'viewonline_whois';

		$template->assign_vars(array(
			'WHOIS'	=> user_ipwhois($ip),
		));
	}

	function check_stopforumspam($chk_data)
	{
		$freq = array();
		$chk_data[0] = str_replace(' ', '%20', $chk_data[0]);
		if ($chk_data[0] == '' && $chk_data[1] == '' && $chk_data[2] == '')
		{
			return array();
		}

		$xmlUrl = 'http://api.stopforumspam.org/api?';
		$xmlUrl .= (!empty($chk_data[0])) ? 'username=' . $chk_data[0] . '&' : '';
		$xmlUrl .= (!empty($chk_data[1])) ? 'ip=' . $chk_data[1] . '&' : '';
		$xmlUrl .= (!empty($chk_data[2])) ? 'email=' . $chk_data[2] . '' : '';
		$xmlUrl .= '&serial';

		$xmlStr = (function_exists('file_get_contents')) ? @file_get_contents($xmlUrl) : $this->file_get_contents_curl($xmlUrl);

		if ($xmlStr)
		{
			$data = unserialize($xmlStr);
			if (!$data['success'])
			{
				return ('CONNECTION_ERROR');
			}

			$result = array();
			$result['username'] = ($data['username']['appears']) ? 'yes' : 'no';
			$freq['username'] = $data['username']['frequency'];
			$result['ip'] = ($data['ip']['appears']) ? 'yes' : 'no';
			$freq['ip'] = $data['ip']['frequency'];
			$result['email'] = ($data['email']['appears']) ? 'yes' : 'no';
			$freq['email'] = $data['email']['frequency'];

			return array($result, $freq);
		}
		else
		{
			return ('CONNECTION_ERROR');
		}
	}

	function jump_to($option_id)
	{
		global $user, $template;

		$options = array($user->lang['PER_DAY'], $user->lang['PER_WEEK'], $user->lang['PER_MOUNTH'], $user->lang['PER_YEAR'], $user->lang['PER_ALL_TIME']);

		for($row = 0; $row < count($options); $row++)
		{
			$template->assign_block_vars('jumpbox_options', array(
				'OPTION_ID'		=> $row,
				'OPTION'		=> $options[$row],
				'SELECTED'		=> ($row == $option_id) ? ' selected="selected"' : '',
				)
			);
		}
		return;
	}

	function full_check($uid)
	{
		global $db, $template, $user, $phpbb_root_path, $phpEx;

		$add = request_var('add', false);
		$this->tpl_name = 'is_spamer_full';

		if (file_exists('' . $phpbb_root_path. 'ext/sheer/stopforumspam/acp/apy_key.' . $phpEx . ''))
		{
			$file = @fopen('' . $phpbb_root_path . 'ext/sheer/stopforumspam/acp/apy_key.' . $phpEx . '', "r");
			fseek($file, 2);
			$apy_key = fgets($file,15);
		}
		if (!isset($apy_key))
		{
			$apy_key = request_var('apy_key', '', false);
		}

		$banned_ip = $em = $nick = false;
		$report_img = $ip_img = $em_img = $img = '';
		$report = $user->lang['RESUME'];

		$sql = 'SELECT user_id, user_ip, user_email, username
			FROM ' . USERS_TABLE . '
			WHERE user_id = ' . intval($uid);

		$result = $db->sql_query_limit($sql, 1);
		$row = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		$ip = $row['user_ip'];
		if ($add)
		{
			$this->PostToHost('username=' . $row['username'] . '&ip_addr=' . $row['user_ip'] . '&email=' . $row['user_email'] . '&api_key=' . $apy_key . '');
			redirect ($this->u_action. '&amp;f=d');
		}

		$ch_data = array(
			$row['username'],
			$row['user_ip'],
			$row['user_email']
		);

		$i_data = $this->check_stopforumspam($ch_data);
		$insp_data = $i_data[0];
		$freq = $i_data[1];
		$report_nic = $report_email = $report_ip = 'ip';
		$nick = $banned_ip = $em = false;
		if (!is_array($insp_data))
		{
			trigger_error($i_data);
		}
		if (sizeof($insp_data))
		{
			foreach ($insp_data as $key => $value)
			{
				switch ($key)
				{
					case 'username':
						if ($value == 'yes')
						{
							$nick = true;
							$report_nic = 'spam';
						}
					break;
					case 'ip':
						if ($value == 'yes')
						{
							$banned_ip = true;
							$report_ip = 'spam';
						}
					break;
					case 'email':
						if ($value == 'yes')
						{
							$em = true;
							$report_email = 'spam';
						}
					break;
				}
			}
		}

		if(!$nick && !$em && !$banned_ip && !empty($ip))
		{
			$report = $user->lang['NOT_SPAMMER'];
			$report_img = '';
		}
		else if($nick && $banned_ip && $em)
		{
			$report = $user->lang['SPAMMER'];
			$report_img = 'spam';
		}
		else if($nick && $em && empty($p))
		{
			$report = $user->lang['CHECK_IP'];
			$report_img = 'em_spam';
		}
		else
		{
			$report = $user->lang['POSSIBLE_NOT'];
			$report_img = 'ip';
		}

		$template->assign_vars(array(
			'IP_EMPTY'		=> (empty($ip)) ? true : false,
			'IP_FIND'		=> ($banned_ip)? sprintf($user->lang['IP_FIND'], $freq['ip']) : $user->lang['IP_NOT_FIND'],
			'FIND_MAIL'		=> ($em) ? sprintf($user->lang['EMAIL_FIND'], $freq['email']) : $user->lang['EMAIL_NOT_FIND'],
			'FIND_NICK'		=> ($nick) ? sprintf ($user->lang['NICK_FIND'], $freq['username']) : $user->lang['NICK_NOT_FIND'],

			'USER'			=> $row['username'],
			'S_FIND_MAIL'	=> $em,
			'S_FIND_NICK'	=> $nick,
			'S_IP_FIND'		=> $banned_ip,
			'IP'			=> (!empty($ip)) ? $row['user_ip'] : '',
			'EMAIL'			=> $row['user_email'],
			'APY_KEY'		=> $apy_key,

			'REPORT'		=> $report,
			'CLASS'			=> $report_img,
			'CLASS_IP'		=> $report_ip,
			'CLASS_NIC'		=> $report_nic,
			'CLASS_EM'		=> $report_email,

			'U_ACTION'	=> 	$this->u_action . '&amp;add=1&amp;full_check=true&ch_user=' . $row['user_id'],
		));

		if (empty($ip) || empty($banned_ip))
		{
			$sql = 'SELECT poster_ip
				FROM ' . POSTS_TABLE . '
				WHERE poster_id = ' . intval($uid) . '
				GROUP BY poster_ip';
			$result = $db->sql_query($sql);
			while ($ip_row = $db->sql_fetchrow($result))
			{
				$template->assign_block_vars('ip_row', array(
					'IP'	=> '<a href = "http://www.stopforumspam.com/ipcheck/' . $ip_row['poster_ip'] . '" target="_blank">' . $ip_row['poster_ip'] . '</a> <-- Click me',
				));
			}
		}
		$db->sql_freeresult($result);
	}

	function done()
	{
		global $template, $user;

		$this->tpl_name = 'is_spamer_full';

		$template->assign_vars(array(
			'DONE'	=> true,
		));
	}

	function PostToHost($data)
	{
		$fp = fsockopen("www.stopforumspam.com", 80);
		fputs($fp, "POST /add.php HTTP/1.1\n" );
		fputs($fp, "Host: www.stopforumspam.com\n" );
		fputs($fp, "Content-type: application/x-www-form-urlencoded\n" );
		fputs($fp, "Content-length: ".strlen($data)."\n" );
		fputs($fp, "Connection: close\n\n" );
		fputs($fp, $data);
		fclose($fp);
	}

	function getmicrotime()
	{
		list($usec, $sec) = explode(' ', microtime());
		return ((float)$usec + (float)$sec);
	}

	function backup($uid)
	{
		global $db;

		$sql = 'SELECT *
			FROM ' . USERS_TABLE . '
			WHERE user_id = ' . intval($uid);
		$result = $db->sql_query($sql);
		$row = $db->sql_fetchrow($result);

		$sql_layer = $db->get_sql_layer();
		switch ($sql_layer)
		{
			case 'mysqli':
			case 'mysql4':
			case 'mysql':
				$extractor = new mysql_extractor();
			break;

			case 'sqlite':
				$extractor = new sqlite_extractor();
			break;

			case 'sqlite3':
				$extractor = new sqlite3_extractor();
			break;

			case 'postgres':
				$extractor = new postgres_extractor();
			break;

			case 'oracle':
				$extractor = new oracle_extractor();
			break;

			case 'mssql':
			case 'mssql_odbc':
			case 'mssqlnative':
				$extractor = new mssql_extractor();
			break;
		}
		$extractor->write_start($uid, $sql_layer);
	}

	function file_get_contents_curl($url)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $url);
		$data = curl_exec($ch);
		curl_close($ch);
		return $data;
	}
}

class base_extractor
{
	function base_extractor($format, $filename, $time, $download = false, $store = false)
	{

	}

	function write_end($data, $uid)
	{
		global $phpbb_root_path;

		$filename = 'backup_user_id_' . $uid . '.sql';
		$file = $phpbb_root_path . 'store/' . $filename;
		$fp = fopen($file, "wb");
		if (!$fp)
		{
			trigger_error('FILE_WRITE_FAIL', E_USER_ERROR);
		}
		fputs($fp, $data);
		fclose ($fp);
	}
}

class mysql_extractor extends base_extractor
{
	function write_start($uid, $sql_layer)
	{
		global $db, $phpbb_root_path;
		$sql = 'SELECT *
			FROM ' . USERS_TABLE . '
			WHERE user_id = ' . intval($uid);
		$result = $db->sql_query($sql);
		$row = $db->sql_fetchrow($result);

		$fields_cnt = ($sql_layer == 'mysqli') ? mysqli_num_fields($result) : mysql_num_fields($result);

		// Get field information
		$field = array();
		for ($i = 0; $i < $fields_cnt; $i++)
		{
			$field[] = ($sql_layer == 'mysqli') ? mysqli_fetch_field($result) : mysql_fetch_field($result, $i);
		}
		if($sql_layer == 'mysqli')
		{
			mysqli_free_result($result);
		}
		else
		{
			mysql_free_result($result);
		}
		$db->sql_freeresult($result);

		$field_set = array();

		for ($j = 0; $j < $fields_cnt; $j++)
		{
			$field_set[] = $field[$j]->name;
		}

		$search			= array("\\", "'", "\x00", "\x0a", "\x0d", "\x1a", '"');
		$replace		= array("\\\\", "\\'", '\0', '\n', '\r', '\Z', '\\"');
		$fields			= implode(', ', $field_set);
		$sql_data		= 'INSERT INTO ' . USERS_TABLE . ' (' . $fields . ') VALUES ';

		$query = '(';
		for ($j = 0; $j < $fields_cnt; $j++)
		{
			$values = array();
			for ($j = 0; $j < $fields_cnt; $j++)
			{
				if (!isset($row[$field[$j]->name]) || is_null($row[$field[$j]->name]))
				{
					$values[$field[$j]->name] = 'NULL';
				}
				else
				{
					$values[$field[$j]->name] = "'" . str_replace($search, $replace, $row[$field[$j]->name]) . "'";
				}
			}
			$query .= implode(', ', $values) . ')';
		}
		$sql_data .= $query;
		$this->write_end($sql_data, $uid);
	}
}

class sqlite_extractor extends base_extractor
{
	function write_start($uid, $sql_layer)
	{
		return;
	}
}

class sqlite3_extractor extends base_extractor
{
	function write_start($uid, $sql_layer)
	{
		return;
	}
}

class postgres_extractor extends base_extractor
{
	function write_start($uid, $sql_layer)
	{
		return;
	}
}

class oracle_extractor extends base_extractor
{
	function write_start($uid, $sql_layer)
	{
		return;
	}
}

class mssql_extractor extends base_extractor
{
	function write_start($uid, $sql_layer)
	{
		return;
	}
}
