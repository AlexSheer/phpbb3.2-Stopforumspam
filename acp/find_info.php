<?php
/**
*
* @package phpBB Extension - Find Spamer
* @copyright (c) 2015 Sheer
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace sheer\stopforumspam\acp;

class find_info
{
	function module()
	{
		return array(
			'filename'	=> '\sheer\stopforumspam\acp\find_module',
			'version'	=> '1.0.0',
			'title' => 'ACP_FIND_SPAMER',
			'modes'		=> array(
				'find'	=> array(
					'title' => 'ACP_FIND_SPAMER',
					'auth' => 'ext_sheer/stopforumspam && acl_a_board && acl_a_user',
					'cat' => array('ACP_CAT_USERS')
				),
			),
		);
	}
}
