<?php
/**
*
* @package phpBB Extension - Find Spamer
*
* @copyright (c) 2019 Sheer
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace sheer\stopforumspam\controller;

use Symfony\Component\HttpFoundation\Response;

/**
* Main controller
*/
class sfsfinder
{
	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\request\request_interface */
	protected $request;

	/** @var \phpbb\controller\helper */
	protected $helper;

	/** @var string phpBB root path */
	protected $root_path;

	/**
	* Constructor
	*
	* @param \phpbb\template\template           $template       Template object
	* @param \phpbb\user                        $user           User object
	* @param string                             $root_path      phpBB root path
	* @param string                             $php_ext        phpEx
	* @access public
	*/
	public function __construct(
			\phpbb\template\template $template,
			\phpbb\request\request_interface $request,
			\phpbb\controller\helper $helper,
			$root_path
	)
	{
		$this->template				= $template;
		$this->request				= $request;
		$this->helper				= $helper;
		$this->root_path			= $root_path;
	}

	/**
	 * Display the flags page
	 *
	 * @access public
	 */
	public function main()
	{
		global $phpbb_container;

		$sfs = $phpbb_container->get('sheer.stopforumspam.core.functions_sfs');

		$id = $this->request->variable('u', 0);

		page_header();
		$url = $this->helper->route('sheer_stopforumspam_sfsfinder', array('u' => $id));

		$sfs->sfull_check($id, $url, 'memberlist');
		page_footer();
		return new Response($this->template->return_display('body'), 200);
	}
}
