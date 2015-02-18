<?php
/**
*
* @package phpBB Extension - Find Spamer
* @copyright (c) 2015 Sheer
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace sheer\stopforumspam\migrations;

class stopforumspam_0_0_1 extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return;
	}

	static public function depends_on()
	{
		return array('\phpbb\db\migration\data\v310\dev');
	}

	public function update_schema()
	{
		return array(
		);
	}

	public function revert_schema()
	{
		return array(
		);
	}

	public function update_data()
	{
		return array(
			// Current version
			array('config.add', array('stopforumspam', '0.0.1')),
			// ACP
			array('module.add', array('acp', 'ACP_CAT_USERS', array(
				'module_basename'	=> '\sheer\stopforumspam\acp\find_module',
				'module_langname'	=> 'ACP_FIND_SPAMER',
				'module_mode'		=> 'find',
				'module_auth'		=> 'ext_sheer/stopforumspam && acl_a_board && acl_a_user',
			))),
		);
	}
}
