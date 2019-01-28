<?php
/**
*
* @package phpBB Extension - Find Spamer
* @copyright (c) 2019 Sheer
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace sheer\stopforumspam\core;

class functions_sfs
{
	/* @var \phpbb\db\driver\driver */
	protected $db;

	/* @var \phpbb\user */
	protected $user;

	/** @var \phpbb\request\request_interface */
	protected $request;

	/** @var \phpbb\config\config $config Config object */
	protected $config;

	protected $phpbb_root_path;
	protected $php_ext;

	/**
	 * Constructor
	 *
	 * @param \phpbb\db\driver\driver|\phpbb\db\driver\driver_interface $db           Database object
	 * @param \phpbb\user                                               $user         User object
	 * @param string                                                    $albums_table Gallery albums table
	 */
	public function __construct(
		\phpbb\db\driver\driver_interface $db,
		\phpbb\user $user,
		\phpbb\template\template $template,
		\phpbb\request\request_interface $request,
		\phpbb\config\config $config,
		$phpbb_root_path,
		$php_ext
	)
	{
		$this->db					= $db;
		$this->user					= $user;
		$this->template				= $template;
		$this->request				= $request;
		$this->config				= $config;
		$this->phpbb_root_path		= $phpbb_root_path;
		$this->php_ext				= $php_ext;
	}

	public function check_stopforumspam($chk_data)
	{
		$freq = array();
		$chk_data[0] = str_replace(' ', '%20', $chk_data[0]);
		if ($chk_data[0] == '' && $chk_data[1] == '' && $chk_data[2] == '')
		{
			return array();
		}

		$xmlUrl = 'http://api.stopforumspam.org/api?';
		$xmlUrl .= (!empty($chk_data[0])) ? 'username=' . urlencode(iconv('GBK', 'UTF-8', $chk_data[0])) . '&' : '';
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
			if (isset($data['ip']['appears']))
			{
				$result['ip'] = ($data['ip']['appears']) ? 'yes' : 'no';
			}
			$freq['ip'] = (isset($data['ip']['frequency'])) ?  $data['ip']['frequency'] : '';
			$result['email'] = ($data['email']['appears']) ? 'yes' : 'no';
			$freq['email'] = $data['email']['frequency'];

			return array($result, $freq);
		}
		else
		{
			return ('CONNECTION_ERROR');
		}
	}

	function sfull_check($uid, $u_action, $mode = 'acp')
	{
		$add = $this->request->variable('submit', false);
		$add_and_delete = $this->request->variable('delete', false);
		$apy_key	= $this->request->variable('apy_key', $this->config['sfs_apikey']);

		$this->template->set_filenames(array(
			'body' => '@sheer_stopforumspam/is_spamer_full.html')
		);

		$banned_ip = $em = $nick = false;
		$report_img = $ip_img = $em_img = $img = '';
		$report = $this->user->lang['RESUME'];

		$sql = 'SELECT user_id, user_ip, user_email, username
			FROM ' . USERS_TABLE . '
				WHERE user_id = ' . intval($uid);
		$result = $this->db->sql_query_limit($sql, 1);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		if (!$row)
		{
			trigger_error('NO_USER');
		}

		$ip = $row['user_ip'];

		if ($add)
		{
			if ($this->PostToHost('username=' . $row['username'] . '&ip_addr=' . $row['user_ip'] . '&email=' . $row['user_email'] . '&api_key=' . $apy_key . ''))
			{
				$this->done();
				redirect ($u_action. '&amp;f=d');
			}
			else
			{
				$this->template->assign_vars(array(
					'S_ERROR'	=> true,
				));
			}
		}

		if ($add_and_delete)
		{
			include_once ($this->phpbb_root_path . 'includes/functions_user.' . $this->php_ext);

			$sql = 'SELECT user_email, username, user_ip
				FROM ' . USERS_TABLE . '
					WHERE user_id = ' . $uid;
			$result = $this->db->sql_query($sql);
			$row = $this->db->sql_fetchrow($result);
			$this->db->sql_freeresult($result);

			if ($this->PostToHost('username=' . $row['username'] . '&ip_addr=' . $row['user_ip'] . '&email=' . $row['user_email'] . '&api_key=' . $apy_key . ''))
			{
				$this->backup($uid);
				user_ban('email', $row['user_email'], 0, 0, 0, $this->user->lang['SPAM'], $this->user->lang['SPAM']);
				user_ban('user', $row['username'], 0, 0, 0, $this->user->lang['SPAM'], $this->user->lang['SPAM']);
				if ($row['user_ip'])
				{
					user_ban('ip', $row['user_ip'], 0, 0, 0, $this->user->lang['SPAM'], $this->user->lang['SPAM']);
				}
				user_delete('remove', $uid);
				add_log('admin', 'LOG_USER_DELETED', $row['username']);
				$this->template->assign_vars(array(
					'L_DONE'	=> '<strong>' . $this->user->lang['SUCSESS_DELETE'] . ' </strong><br />' . $this->user->lang['DONE'],
				));

				$this->done();
				redirect ($u_action. '&amp;f=d');
			}
			else
			{
				$this->template->assign_vars(array(
					'S_ERROR'	=> true,
				));
			}
		}

		if (empty($ip))
		{
			$sql = 'SELECT poster_ip
				FROM ' . POSTS_TABLE . '
					WHERE poster_id = ' . intval($uid) . '
						GROUP BY poster_ip';
			$result = $this->db->sql_query($sql);
			while ($rw = $this->db->sql_fetchrow($result))
			{
				$this->template->assign_block_vars('rw', array(
					'IP'	=> '<a href = "https://www.stopforumspam.com/ipcheck/' . $rw['poster_ip'] . '">' . $rw['poster_ip'] . '</a>',
				));
			}
			$this->db->sql_freeresult($result);
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
			$report_ip = $report_email = $report_nic = 'clear ip';
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

		if (!$nick && !$em && !$banned_ip && !empty($ip))
		{
			$report = $this->user->lang['NOT_SPAMMER'];
			$report_img = ' find';
		}
		else if ($nick && $banned_ip && $em)
		{
			$report = $this->user->lang['SPAMMER'];
			$report_img = ' spam';
		}
		else if ($nick && $em && empty($p))
		{
			$report = $this->user->lang['CHECK_IP'];
			$report_img = ' em_spam';
		}
		else
		{
			$report = $this->user->lang['POSSIBLE_NOT'];
			$report_img = ' ip';
		}

		$this->template->assign_vars(array(
			'IP_EMPTY'		=> (empty($ip)) ? true : false,
			'IP_FIND'		=> ($banned_ip)? sprintf($this->user->lang['IP_FIND'], $freq['ip']) : $this->user->lang['IP_NOT_FIND'],
			'FIND_MAIL'		=> ($em) ? sprintf($this->user->lang['EMAIL_FIND'], $freq['email']) : $this->user->lang['EMAIL_NOT_FIND'],
			'FIND_NICK'		=> ($nick) ? sprintf ($this->user->lang['NICK_FIND'], $freq['username']) : $this->user->lang['NICK_NOT_FIND'],

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

			'U_ACTION'	=> 	$u_action . '&amp;add=1&amp;full_check=true&ch_user=' . $row['user_id'],

			'PAGE_TITLE'			=> $this->user->lang['SFS_INFO'],
		));

		if ($mode == 'acp')
		{
			$this->template->assign_vars(array(
				'T_ASSETS_VERSION'		=> $this->config['assets_version'],
				'T_FONT_AWESOME_LINK'	=> !empty($this->config['allow_cdn']) && !empty($this->config['load_font_awesome_url']) ? $this->config['load_font_awesome_url'] : "{$this->phpbb_root_path}assets/css/font-awesome.min.css?assets_version=" . $this->config['assets_version'],
				'T_JQUERY_LINK'			=> !empty($this->config['allow_cdn']) && !empty($this->config['load_jquery_url']) ? $this->config['load_jquery_url'] : "{$this->phpbb_root_path}assets/javascript/jquery.min.js",
				'S_ALLOW_CDN'			=> !empty($this->config['allow_cdn']),
			));
		}
		$this->template->display('body');

		garbage_collection();
		exit_handler();
	}

	public function backup($uid)
	{
		$sql = 'SELECT *
			FROM ' . USERS_TABLE . '
				WHERE user_id = ' . intval($uid);
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);

		$sql_layer = $this->db->get_sql_layer();
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

	public function file_get_contents_curl($url)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $url);
		$data = curl_exec($ch);
		curl_close($ch);
		return $data;
	}

	public function PostToHost($data)
	{
		$fp = fsockopen("www.stopforumspam.com",80);
		if ($fp)
		{
			fputs($fp, "POST /add.php HTTP/1.1\n" );
			fputs($fp, "Host: www.stopforumspam.com\n" );
			fputs($fp, "Content-type: application/x-www-form-urlencoded\n" );
			fputs($fp, "Content-length: ".strlen($data)."\n" );
			fputs($fp, "Connection: close\n\n" );
			fputs($fp, $data);
			// read the result
			$h = '';
			while (!feof($fp))
			{
				$h .= fgets($fp);
			}
			fclose($fp);
			if (stristr($h, '503') === false)
			{
				return(true);
			}
		}
		return(false);
	}

	public function jump_to($option_id)
	{
		$options = array($this->user->lang['PER_DAY'], $this->user->lang['PER_WEEK'], $this->user->lang['PER_MOUNTH'], $this->user->lang['PER_YEAR'], $this->user->lang['PER_ALL_TIME']);

		for ($row = 0; $row < count($options); $row++)
		{
			$this->template->assign_block_vars('jumpbox_options', array(
				'OPTION_ID'		=> $row,
				'OPTION'		=> $options[$row],
				'SELECTED'		=> ($row == $option_id) ? ' selected="selected"' : '',
				)
			);
		}
		return;
	}

	function done()
	{
		$this->template->assign_vars(array(
			'DONE'	=> true,
		));

		$this->template->display('is_spamer_full.html');

		garbage_collection();
		exit_handler();
	}

	function who($ip)
	{
		$this->template->assign_vars(array(
			'WHOIS'	=> user_ipwhois($ip),
		));

		$this->template->display('viewonline_whois.html');
		garbage_collection();
		exit_handler();
	}

	public function getmicrotime()
	{
		list($usec, $sec) = explode(' ', microtime());
		return ((float)$usec + (float)$sec);
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
		if ($sql_layer == 'mysqli')
		{
			mysqli_free_result($result);
		}
		else
		{
			mysql_free_result($result);
		}

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
