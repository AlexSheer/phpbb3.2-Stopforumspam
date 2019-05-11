<?php
/**
*
* @package phpBB Extension - Find Spamer
* @copyright (c) 2018 Sheer
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace sheer\stopforumspam\migrations;

class stopforumspam_1_0_4 extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return isset($this->config['stopforumspam']) && version_compare($this->config['stopforumspam'], '1.0.4', '>=');
	}

	static public function depends_on()
	{
		return array('\sheer\stopforumspam\migrations\stopforumspam_1_0_3');
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
			array('config.update', array('stopforumspam', '1.0.4')),
			// Add permissions
			array('permission.add', array('m_chk_sfs', true)),
			// Add permissions sets
			array('permission.permission_set', array('ADMINISTRATORS', 'm_chk_sfs', 'group', true)),
			array('permission.permission_set', array('GLOBAL_MODERATORS', 'm_chk_sfs', 'group', true)),
			array('permission.permission_set', array('ROLE_ADMIN_FULL', 'm_chk_sfs', 'role', true)),
			array('permission.permission_set', array('ROLE_MOD_FULL', 'm_chk_sfs', 'role', true)),
		);
	}
}
