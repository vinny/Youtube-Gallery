<?php
/**
*
* @package Video Gallery
* @version $Id$
* @copyright (c) 2013 _Vinny_ vinny@suportephpbb.com.br http://www.suportephpbb.com.br
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/


class acp_video_info
{
	function module()
	{
		return array(
			'filename'	=> 'acp_video',
			'title'		=> 'ACP_VIDEO',
			'version'	=> '1.0.0',
			'modes'		=> array(
				'settings'			=> array('title' => 'ACP_VIDEO_SETTINGS',				'auth' => 'acl_a_board',	'cat' => array('ACP_VIDEO')),
				'cat'				=> array('title' => 'ACP_VIDEO_CATEGORY',				'auth' => 'acl_a_board',	'cat' => array('ACP_VIDEO')),
				),
			);
	}
}

?>