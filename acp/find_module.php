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
		$sfs = $phpbb_container->get('sheer.stopforumspam.core.functions_sfs');

		$default_key = 'a';

		$id			= $request->variable('t', 0);
		$start		= $request->variable('start', 0);
		$delmarked	= $request->variable('delmarked', false);
		$filter		= $request->variable('filter', '', true);
		$filter_key	= $request->variable('f_opt', 1);
		$no_post	= $request->variable('no_posts', '');
		$sort_key	= $request->variable('sk', $default_key);
		$sort_dir	= $request->variable('sd', 'a');
		$full_check	= $request->variable('full_check', false);
		$ip			= $request->variable('ip', '');
		$ch_user	= $request->variable('ch_user', '');
		$whois		= $request->variable('whois', false);
		$action		= $request->variable('f', '');
		$apy_key	= $request->variable('apy_key', $config['sfs_apikey']);
		$save		= $request->variable('save', false);
		$s_inactive	= $request->variable('s_inactive', false);

		$users = $request->variable('id_list', array(0));
		$fail_chhk = false;

		$filter_options = array(1 => 'ip', 2 => 'email');
		$per_page = 6;

		$this->tpl_name = 'acp_find_body';
		$this->page_title = $user->lang('ACP_FIND_SPAMER');

		if ($save)
		{
			$config->set('sfs_apikey', $apy_key);
			meta_refresh(3, $this->u_action);
			trigger_error($user->lang['CONFIG_UPDATED'] . adm_back_link($this->u_action));
		}

		if ($full_check == true)
		{
			$sfs->sfull_check($ch_user, $this->u_action, 'acp');
		}

		if ($whois == true)
		{
			$sfs->who($ip);
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
		$pagination_url = $this->u_action. '&amp;filter=' . $filter . '&amp;f=' . $action . '&amp;no_posts=' . $no_post . '&amp;s_inactive=' . $s_inactive. '&amp;sd=' . $sort_dir . '&amp;sk=' . $sort_key . '&amp;f_opt='. $filter_key . '';

		$sql_where = ($filter) ? ' AND user_' . $filter_options[$filter_key] . ' ' . $db->sql_like_expression(str_replace('*', $db->get_any_char(), $filter)) . '' : '';
		$sql_where .= ($no_post) ? ' AND user_posts = 0' : '';
		$sql_where .= ($s_inactive) ? ' AND user_inactive_reason <> 0' : '';
		$order_by = ' ORDER BY ' . $sort_key_sql[$sort_key] . ' ' . (($sort_dir == 'a') ? 'ASC' : 'DESC') . '';

		if ($delmarked)
		{
			if (confirm_box(true))
			{
				if (sizeof($users))
				{
					$sql = 'SELECT user_id, user_email, username, user_ip
						FROM ' . USERS_TABLE . '
							WHERE ' . $db->sql_in_set('user_id', $users) . $sql_where . ''. $order_by;
					$result = $db->sql_query_limit($sql, $per_page, $start);

					$i = 0;
					while ($row = $db->sql_fetchrow($result))
					{
						$sfs->backup($users[$i]);
						user_ban('email', $row['user_email'], 0, 0, 0, $user->lang['SPAM'], $user->lang['SPAM']);
						user_ban('user', $row['username'], 0, 0, 0, $user->lang['SPAM'], $user->lang['SPAM']);
						if ($row['user_ip'])
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

			$time_start = $sfs->getmicrotime();
			$sql = 'SELECT count(user_id) AS total
				FROM ' . USERS_TABLE . '
					WHERE user_type != ' . USER_IGNORE . '
						AND user_type != ' . USER_FOUNDER . '
						AND user_regdate > ' . $period . ' '
						. $sql_where;
			$result = $db->sql_query($sql);
			$total_users =  $db->sql_fetchfield('total');
			$db->sql_freeresult($result);

			$sql = 'SELECT user_id, username, user_ip, user_email, user_regdate, user_posts, user_lastvisit, user_inactive_reason
					FROM ' . USERS_TABLE . '
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

				$res = $sfs->check_stopforumspam($ch_data);
				if (!is_array($res[0]))
				{
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

				$class = ' find';
				if (!isset($banned_ip))
				{
					$banned_ip = false;
				}
				if ($em || $nick)
				{
					$class = ' em_spam';
					if ($em && $nick && $banned_ip)
					{
						$class = ' spam';
					}
				}
				else if (empty($ip) || $banned_ip)
				{
					$class = ' ip';
				}
				if ($em && $nick)
				{
					$class = ' spam';
				}

				$template->assign_block_vars('row', array(
					'USER_ID'		=> $row['user_id'],
					'IS_FIND'		=> ($banned_ip || $em || $nick || empty($ip))? true : false,
					'CLASS'			=> $class,
					'SPAM_MAIL'		=> ($em) ? true : false,
					'SPAM_NICK'		=> ($nick) ? true : false,
					'IP_IMG'		=> ($banned_ip) ? ' fa-check' : ' fa-info',
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
					'S_USER_INACTIVE'	=> $row['user_inactive_reason'],
				));
			}
			$db->sql_freeresult($result);

			$pagination->generate_template_pagination($pagination_url, 'pagination', 'start', $total_users, $per_page, $start);
			$time_end = $sfs->getmicrotime();
			$time = $time_end - $time_start;

			$template->assign_vars(array(
				'S_MODE_SELECT'		=> $s_sort_key,
				'S_ORDER_SELECT'	=> $s_sort_dir,
				'FILTER'			=> $filter,
				'FILTER_OPTIONS'	=> $s_filter_key,
				'NOPOSTS'			=> ($no_post) ? true : false,
				'UNACTIVE'			=> ($s_inactive) ? true : false,
				'FILTER_OPTIONS'	=> $s_filter_key,
				'TOTAL_USERS'		=> ($total_users) ? $user->lang('LIST_USERS', (int) $total_users) : '',
				'EXEC_TIME'			=> sprintf($user->lang['EXEC_TIME'], $time),
				'S_ACTION'			=> $pagination_url,
				'APY_KEY'			=> $apy_key,
			));
			$sfs->jump_to($action);
		}
	}
}

