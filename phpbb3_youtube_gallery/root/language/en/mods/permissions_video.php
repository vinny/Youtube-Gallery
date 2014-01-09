<?php
/**
*
* permissions_video [English]
*
* @package language
* @version $Id$
* @copyright (c) 2014 Vinny
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/
/**
*/

/**
* DO NOT CHANGE
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

$lang['permission_cat']['video'] = 'phpBB Video Gallery';

// Adding the permissions
$lang = array_merge($lang, array(
	'acl_u_video_view_full'	=> array('lang'	=> 'Can view Video Gallery',	'cat' => 'video'),
	'acl_u_video_view'		=> array('lang'	=> 'Can view videos',			'cat' => 'video'),
	'acl_u_video_delete'	=> array('lang' => 'Can delete own videos',		'cat' => 'video'),
	'acl_u_video_post'		=> array('lang'	=> 'Can post videos',			'cat' => 'video'),
));
?>