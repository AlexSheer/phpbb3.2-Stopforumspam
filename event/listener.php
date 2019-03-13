<?php
/**
*
* @package phpBB Extension - Find Spamer
* @copyright (c) 2015 Sheer
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace sheer\stopforumspam\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
* Event listener
*/
class listener implements EventSubscriberInterface
{
	/** @var \phpbb\controller\helper */
	protected $helper;

	/** @var \phpbb	emplate	emplate */
	protected $template;

	/** @var \phpbb\auth\auth */
	protected $auth;

	/* @var \phpbb\db\driver\driver */
	protected $db;

	protected $phpbb_root_path;
	protected $php_ext;

/**
* Assign functions defined in this class to event listeners in the core
*
* @return array
* @static
* @access public
*/
	static public function getSubscribedEvents()
	{
		return array(
			'core.user_setup'					=> 'load_language_on_setup',
			'core.memberlist_view_profile'		=> 'chk_profile',
			'core.acp_users_overview_before'	=> 'users_overview',
		);
	}

	/**
	* Constructor
	*/
	public function __construct(
		\phpbb\controller\helper $helper,
		\phpbb\template\template $template,
		\phpbb\auth\auth $auth,
		\phpbb\db\driver\driver_interface $db,
		$phpbb_root_path,
		$php_ext
	)
	{
		$this->helper				= $helper;
		$this->template				= $template;
		$this->auth					= $auth;
		$this->db					= $db;
		$this->phpbb_root_path		= $phpbb_root_path;
		$this->php_ext				= $php_ext;
	}

	public function load_language_on_setup($event)
	{
		$lang_set_ext = $event['lang_set_ext'];
		$lang_set_ext[] = array(
			'ext_name' => 'sheer/stopforumspam',
			'lang_set' => 'stopforumspam',
		);
		$event['lang_set_ext'] = $lang_set_ext;
	}

	public function users_overview($event)
	{
		$user_id = $event['user_row']['user_id'];
		$this->template->assign_vars(array(
			'U_CHK_SFS'		=> append_sid("{$this->phpbb_root_path}adm/index.$this->php_ext?i=-sheer-stopforumspam-acp-find_module", "mode=find&amp;full_check=true&amp;ch_user=$user_id"),
		));
	}

	public function chk_profile($event)
	{
		if ($this->auth->acl_get('a_') || $this->auth->acl_get('m_'))
		{
			$groups = array();
			$s_chk_sfs_allowed = true;
			$member = $event['member'];
			$sql = 'SELECT group_id
				FROM ' . USER_GROUP_TABLE . '
					WHERE user_id = ' . $member['user_id'];
			$result = $this->db->sql_query($sql);
			while ($row = $this->db->sql_fetchrow($result))
			{
				$groups[] = $row['group_id'];
			}
			$this->db->sql_freeresult($result);

			foreach ($groups as $key => $group)
			{
				$sql = 'SELECT group_name
					FROM ' . GROUPS_TABLE . '
						WHERE group_id = ' . $group;
				$result = $this->db->sql_query($sql);
				$group_name = (string) $this->db->sql_fetchfield('group_name');
				$this->db->sql_freeresult($result);

				if ($group_name == 'GLOBAL_MODERATORS' || $group_name == 'ADMINISTRATORS')
				{
					$s_chk_sfs_allowed = false;
					break;
				}
			}

			$this->template->assign_vars(array(
				'U_CHK_SFS'			=> $this->helper->route('sheer_stopforumspam_sfsfinder', array('u' => $member['user_id'])),
				'S_CHK_SFS_ALLOWED'	=> $s_chk_sfs_allowed,
			));
		}
	}
}
