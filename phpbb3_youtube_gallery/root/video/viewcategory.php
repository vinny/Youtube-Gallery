<?php
/**
*
* @package phpBB3
* @version $Id$
* @copyright (c) 2015 Vinny
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* @ignore
*/
define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : '../';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_root_path . 'includes/functions_display.' . $phpEx);
include($phpbb_root_path . 'includes/functions_posting.' . $phpEx);
include($phpbb_root_path . 'includes/functions_user.' . $phpEx);

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup('mods/info_acp_video');

if (!$auth->acl_get('u_video_view_full'))
{
	trigger_error($user->lang['UNAUTHED']);
}

if (!$config['google_api_key'])
{	
	if ($auth->acl_get('a_'))
	{
		trigger_error($user->lang['NO_KEY_ADMIN']);
	}
	else
	{
		trigger_error($user->lang['NO_KEY_USER']);
	}
}

// Initial var setup
$video_id	= request_var('id', 0);
$video_cat_id = request_var('cid', 0);

$sql_start = request_var('start', 0);
$sql_limit = request_var('limit', 10);

$template->assign_block_vars('navlinks', array(
	'FORUM_NAME' 	=> ($user->lang['VIDEO_INDEX']),
	'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}video/index.$phpEx"),	
));

$pagination_url = append_sid("{$phpbb_root_path}video/viewcategory.$phpEx", "cid=$video_cat_id");

$sql = 'SELECT v.*, ct.video_cat_id, ct.video_cat_title, u.username, u.user_colour, u.user_id
			FROM ' . VIDEO_TABLE . ' v, ' . USERS_TABLE . ' u
			LEFT JOIN ' . VIDEO_CAT_TABLE . ' ct ON ct.video_cat_id = ' . (int) $video_cat_id . '
			WHERE 
			u.user_id = v.user_id';

	/*$sql = 'SELECT v.*, ct.video_cat_title,ct.video_cat_id, u.username,u.user_colour,u.user_id
	FROM ' . VIDEO_TABLE . ' v, ' . VIDEO_CAT_TABLE . ' ct, ' . USERS_TABLE . ' u
	WHERE u.user_id = v.user_id
		AND v.video_cat_id = ' . (int) $video_cat_id . '
			';*/
	$result = $db->sql_query_limit($sql, $sql_limit, $sql_start);
	$row = $db->sql_fetchrow($result);
	
	if (!$row)
	{
		$meta_info = append_sid("{$phpbb_root_path}video/index.{$phpEx}");
		meta_refresh(3, $meta_info);
		trigger_error('NO_CAT_VIDEOS');
	}
	
	while ($row = $db->sql_fetchrow($result))
	{
		$page_title	= $row['video_cat_title'];

		$template->assign_block_vars('video', array(
			'VIDEO_TITLE'	=> $row['video_title'],
			'VIDEO_CAT_ID'	=> $row['video_cat_id'],
			'VIDEO_CAT_TITLE'	=> $row['video_cat_title'],
			'VIDEO_VIEWS'	=> $row['video_views'],
			'U_CAT'			=> append_sid("{$phpbb_root_path}video.$phpEx", 'mode=cat&amp;id=' . $row['video_cat_id']),
			'VIDEO_TIME'	=> $user->format_date($row['create_time']),
			'VIDEO_ID'		=> censor_text($row['video_id']),
			'U_VIEW_VIDEO'	=> append_sid("{$phpbb_root_path}video.$phpEx", 'mode=view&amp;id=' . $row['video_id']),
			'U_POSTER'		=> append_sid("{$phpbb_root_path}memberlist.$phpEx", array('mode' => 'viewprofile', 'u' => $row['user_id'])),
			'USERNAME'		=> get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']),
			'YOUTUBE_ID'	=> censor_text($row['youtube_id']),
		));
	}
	$db->sql_freeresult($result);

	// We need another query for the video count
	$sql = 'SELECT COUNT(*) as video_count FROM ' . VIDEO_TABLE . ' WHERE video_cat_id = ' . (int)$video_cat_id;
	$result = $db->sql_query($sql);
	$videorow['video_count'] = $db->sql_fetchfield('video_count');
	$db->sql_freeresult($result);

	//Start pagination
	$template->assign_vars(array(
		'PAGINATION'		=> generate_pagination($pagination_url, $videorow['video_count'], $sql_limit, $sql_start),
		'PAGE_NUMBER'		=> on_page($videorow['video_count'], $sql_limit, $sql_start),
		'TOTAL_VIDEOS'		=> ($videorow['video_count'] == 1) ? $user->lang['LIST_VIDEO'] : sprintf($user->lang['LIST_VIDEOS'], $videorow['video_count']),
	));
	//End pagination

	$template->assign_vars(array(
		'CAT_NAME'			=> $page_title,
	));

	$l_title = ($user->lang['VIEW_CAT'] . ' - ' . $page_title);

	$template->assign_block_vars('navlinks', array(
		'FORUM_NAME' 	=> ($user->lang['VIEW_CAT'] . ' - ' . $page_title),
	));

// Output page
page_header($l_title, false);

$template->set_filenames(array(
	'body' => 'video/video_cat.html')
);

page_footer();

?>