<?php
/**
*
* permissions_video [Portuguese]
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

$lang['permission_cat']['video'] = 'Galeria de Vídeo';

// Adding the permissions
$lang = array_merge($lang, array(
	'acl_u_video_view_full'	=> array('lang'	=> 'Pode ver a Galeria de Vídeo',	'cat' => 'video'),
	'acl_u_video_view'		=> array('lang'	=> 'Pode ver vídeos',			'cat' => 'video'),
	'acl_u_video_delete'	=> array('lang' => 'Pode apagar os seus próprios vídeos',		'cat' => 'video'),
	'acl_u_video_post'		=> array('lang'	=> 'Pode colocar vídeos',			'cat' => 'video'),
));
?>
