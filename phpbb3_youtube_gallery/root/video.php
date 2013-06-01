<?php
/**
*
* @package phpBB3
* @version $Id$
* @copyright (c) 2013 Vinny
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* @ignore
*/
define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_root_path . 'includes/functions_display.' . $phpEx);
include($phpbb_root_path . 'includes/functions_posting.' . $phpEx);
include($phpbb_root_path . 'includes/functions_user.' . $phpEx);

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup('mods/info_acp_video');

// Initial var setup
$video_id	= request_var('id', 0);
$video_url = request_var('video_url', '', true);
$video_title = request_var('video_title', '', true);
$video_cat_id = request_var('cid', 0);
$username = request_var('username', '', true);
$user_id = request_var('user_id', 0);
$youtube_id = request_var('youtube_id', '', true);
$create_time = request_var('create_time', '');

$sql_start = request_var('start', 0);
$sql_limit = request_var('limit', 10);

$mode = request_var('mode', '');
$submit = (isset($_POST['submit'])) ? true : false;

// Determine board url - we may need it later
$board_url = generate_board_url() . '/';
$web_path = (defined('PHPBB_USE_BOARD_URL_PATH') && PHPBB_USE_BOARD_URL_PATH) ? $board_url : $phpbb_root_path;

/**
 * Get youtube video ID from URL
 * From: http://halgatewood.com/php-get-the-youtube-video-id-from-a-youtube-url/
 */
function getYouTubeIdFromURL($url) 
{
	$pattern = '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})/i';
	preg_match($pattern, $url, $matches);

	return isset($matches[1]) ? $matches[1] : false;
}
$youtube_id = getYouTubeIdFromURL($video_url);
$url = "http://gdata.youtube.com/feeds/api/videos/". $youtube_id;
$doc = new DOMDocument;
$doc->load($url);
$video_title = $doc->getElementsByTagName("title")->item(0)->nodeValue;

$sql_ary = array(
	'video_id'			=> $video_id,
	'video_url'			=> $video_url,
	'video_title'		=> $video_title,
	'video_cat_id'		=> $video_cat_id,
	'username'			=> $username,
	'user_id'			=> $user_id,
	'youtube_id'		=> $youtube_id,
	'create_time'		=> (int) time(),
);

$error = $row = array();
$current_time = time();

$template->assign_vars(array(
	//'S_NEW_VIDEO'	 		=> $auth->acl_get('u_video_post') ? true : false,
	'SCRIPT_NAME'			=> 'video',	
	'U_VIDEO'				=> append_sid("{$phpbb_root_path}video.$phpEx"),
));

$template->assign_block_vars('navlinks', array(
	'FORUM_NAME' 	=> ($user->lang['VIDEO_INDEX']),
	'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}video.$phpEx"),	
)); 

switch ($mode)
{
	case 'submit':
		// User is a bot?!
		if ($user->data['is_bot'])
		{
			redirect(append_sid("{$phpbb_root_path}index.$phpEx"));
		}

		$redirect_url = append_sid("{$phpbb_root_path}video.$phpEx", "mode=submit");

		// Is a guest?!
		if ($user->data['user_id'] == ANONYMOUS)
		{
			login_box($redirect_url);
		}

		$l_title = $user->lang['VIDEO_SUBMIT'];
		$template_html = 'video_editor.html';

		$s_action = append_sid("{$phpbb_root_path}video.$phpEx", "mode=submit");
		$s_hidden_fields = '';
		$form_enctype = '';
		add_form_key('postform');

		// List of categories
		$sql = 'SELECT * FROM ' . VIDEO_CAT_TABLE . '
				ORDER BY video_cat_id DESC';
		$result = $db->sql_query($sql);
		
		while ($row = $db->sql_fetchrow($result))
		{
		$template->assign_block_vars('cat', array(
			'VIDEO_CAT_ID'		=> censor_text($row['video_cat_id']),
			'VIDEO_CAT_TITLE'	=> censor_text($row['video_cat_title']),
		));
		}

		// Start assigning vars for main posting page ...
		$template->assign_vars(array(
			'S_USER_ID'				=> $user->data['user_id'],
			'S_USERNAME'			=> $user->data['username'],
			//'S_EDIT_VIDEO'			=> ($mode == 'edit') ? true : false,
			'S_FORM_ENCTYPE'		=> $form_enctype,
			'S_POST_ACTION'			=> $s_action,
			'S_HIDDEN_FIELDS'		=> $s_hidden_fields,
			'ERROR'					=> (sizeof($error)) ? implode('<br />', $error) : '',
		));

		$template->assign_block_vars('navlinks', array(
			'FORUM_NAME' 	=> ($user->lang['VIDEO_SUBMIT']),
		));

		add_form_key('postform');
			if ($submit)
			{
				if (!check_form_key('postform'))
				{
					trigger_error('FORM_INVALID');
				}
			}

		switch ($submit)
		{
			case 'add':
				if ($video_url == '')
				{
					$meta_info = append_sid("{$phpbb_root_path}video.$phpEx", 'mode=submit');
					$message = $user->lang['NEED_VIDEO_URL'];

					meta_refresh(3, $meta_info);
					$message .= '<br /><br />' . sprintf($user->lang['PAGE_RETURN'], '<a href="' . $meta_info . '">', '</a>');
					trigger_error($message);
				}
				else
				{
					$db->sql_query('INSERT INTO ' . VIDEO_TABLE .' ' . $db->sql_build_array('INSERT', $sql_ary));
					$u_action = append_sid("{$phpbb_root_path}video.$phpEx");

					$meta_info = append_sid("{$phpbb_root_path}video.$phpEx");
					$message = $user->lang['VIDEO_CREATED'];

					meta_refresh(3, $meta_info);
					$message .= '<br /><br />' . sprintf($user->lang['PAGE_RETURN'], '<a href="' . $meta_info . '">', '</a>');
					trigger_error($message);
				}
			break;
		}
	break;


	case 'delete':
		$l_title = ($user->lang['DELETE_VIDEO']);

		if (confirm_box(true))
		{
			$sql = 'DELETE FROM ' . VIDEO_TABLE . '
					WHERE video_id = '. $video_id;
			$db->sql_query($sql);

			$meta_info = append_sid("{$phpbb_root_path}video.$phpEx");
			$message = $user->lang['VIDEO_DELETED'];

			meta_refresh(3, $meta_info);
			$message .= '<br /><br />' . sprintf($user->lang['PAGE_RETURN'], '<a href="' . $meta_info . '">', '</a>');
			trigger_error($message);
		}
		else
		{
			$s_hidden_fields = build_hidden_fields(array(
				//'mode'		=> 'delete',
				'submit' 	=> true,
				'video_id' 	=> $video_id,
			));
			confirm_box(false, $user->lang['DELETE_VIDEO'], $s_hidden_fields);
			trigger_error($user->lang['ERROR']);
		
		}
	break;

	case 'view':
	/*if (!$auth->acl_get('u_video_view'))
	{
			trigger_error($user->lang['UNAUTHED']);
	}*/

	$sql_ary = array(
		'SELECT'	=> 'v.*, u.*',
		'FROM'		=> array(
			VIDEO_TABLE			=> 'v',
			USERS_TABLE			=> 'u',
		),
		'WHERE'		=> 'v.video_id = '.(int) $video_id .' and u.user_id = v.user_id',
		'ORDER_BY'	=> 'v.video_id DESC',
	);

	$sql = $db->sql_build_query('SELECT', $sql_ary);
	$result = $db->sql_query($sql);

	while ($row = $db->sql_fetchrow($result))
	{
	$page_title 	= $row['video_title'];
	$user_id 		= $row['user_id'];
	$flash_status	= $config['allow_post_flash'] ? true : false;
	$delete_allowed = ($user->data['is_registered'] && (/*$auth->acl_get('m_video_delete', '') || (*/$row['user_id'] == $user->data['user_id']));

	$template->assign_block_vars('video',array(
		'VIDEO_ID'			=> censor_text($row['video_id']),
		'VIDEO_TITLE'		=> censor_text($row['video_title']),
		'USERNAME'			=> get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']),
		'YOUTUBE_ID'		=> censor_text($row['youtube_id']),
		'VIDEO_TIME'		=> $user->format_date($row['create_time']),
		'YOUTUBE_VIDEO'		=> 'http://www.youtube.com/watch?v='.$row['youtube_id'],
		'VIDEO_LINK' 		=> append_sid(generate_board_url() ."/video.$phpEx", 'mode=view&amp;id=' . $row['video_id']),
		'U_USER_VIDEOS' 	=> append_sid(generate_board_url() ."/video.$phpEx", 'mode=user_videos&amp;user_id=' . $row['user_id']),
		'U_DELETE'			=> ($delete_allowed) ? append_sid("{$phpbb_root_path}video.$phpEx", "mode=delete&amp;id={$row['video_id']}") : '',
		'S_BBCODE_FLASH'	=> $flash_status,
		'FLASH_STATUS'		=> ($flash_status) ? $user->lang['FLASH_IS_ON'] : $user->lang['FLASH_IS_OFF'],
		'S_VIDEO_WIDTH'		=> $config['video_width'],
		'S_VIDEO_HEIGHT'	=> $config['video_height'],
	));	
	}
	$db->sql_freeresult($result);

	// Count the videos user video ...
	$sql = 'SELECT COUNT(video_id) AS total_videos FROM ' . VIDEO_TABLE . ' WHERE user_id = '. (int)$user_id;
	$result = $db->sql_query($sql);
	$total_videos = (int) $db->sql_fetchfield('total_videos');
	$db->sql_freeresult($result);

	$template->assign_vars(array(
		'TOTAL_VIDEOS'		=> $total_videos,
	));

	$l_title = ($user->lang['VIEW_VIDEO'] . ' - ' . $page_title);
	$template_html = 'video_view.html';

	$template->assign_block_vars('navlinks', array(
		'FORUM_NAME' 	=> ($user->lang['VIEW_VIDEO'] . ' - ' . $page_title),
	));

	break;

	case 'cat';
	$sql_limit = ($sql_limit > 10) ? 10 : $sql_limit;
	$pagination_url = append_sid("{$phpbb_root_path}video.$phpEx", "mode=cat&amp;cid=$video_cat_id");

	$sql_ary = array(
		'SELECT'	=> 'v.*,
		ct.video_cat_title,ct.video_cat_id,
		u.username,u.user_colour,u.user_id',
		'FROM'		=> array(
			VIDEO_TABLE			=> 'v',
			VIDEO_CAT_TABLE		=> 'ct',
			USERS_TABLE			=> 'u',
		),
		'WHERE'		=> 'v.video_cat_id = '. $video_cat_id .' AND ct.video_cat_id = '. $video_cat_id .' AND v.user_id = u.user_id',
		'ORDER_BY'	=> 'v.video_id DESC',
	);

	$sql = $db->sql_build_query('SELECT', $sql_ary);
	$result = $db->sql_query_limit($sql, $sql_limit, $sql_start);

	while ($row = $db->sql_fetchrow($result))
	{

		$page_title	= $row['video_cat_title'];

		$template->assign_block_vars('video', array(
			'VIDEO_TITLE'	=> $row['video_title'],
			'VIDEO_CAT_ID'	=> $row['video_cat_id'],
			'VIDEO_CAT_TITLE'	=> $row['video_cat_title'],
			'U_CAT'			=> append_sid("{$phpbb_root_path}video.$phpEx", 'mode=cat&amp;id=' .$row['video_cat_id']),
			'VIDEO_TIME'	=> $user->format_date($row['create_time']),
			'VIDEO_ID'		=> censor_text($row['video_id']),
			'U_VIEW_VIDEO'	=> append_sid("{$phpbb_root_path}video.$phpEx", 'mode=view&amp;id=' .$row['video_id']),
			'U_POSTER'		=> append_sid("{$phpbb_root_path}memberlist.$phpEx", array('mode' => 'viewprofile', 'u' => $row['user_id'])),
			'USERNAME'		=> get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']),
			'YOUTUBE_ID'	=> censor_text($row['youtube_id']),
		));
	}
	$db->sql_freeresult($result);

	// We need another query for the video count
	$sql = 'SELECT COUNT(*) as video_count FROM ' . VIDEO_TABLE .' WHERE video_cat_id = '. (int)$video_cat_id;
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
	$template_html = 'video_cat.html';

	$template->assign_block_vars('navlinks', array(
		'FORUM_NAME' 	=> ($user->lang['VIEW_CAT'] . ' - ' . $page_title),
	));
	break;

	case 'user_videos';

	$template->assign_vars(array(
		'S_SEARCH_USER_VIDEO' 	=> true,
	));

	$sql_limit = ($sql_limit > 10) ? 10 : $sql_limit;
	$pagination_url = append_sid("{$phpbb_root_path}video.$phpEx", "mode=user_videos&amp;user_id=$user_id");

	$sql_ary = array(
		'SELECT'	=> 'v.*,
		ct.video_cat_title,ct.video_cat_id,
		u.username,u.user_colour,u.user_id',
		'FROM'		=> array(
			VIDEO_TABLE			=> 'v',
			VIDEO_CAT_TABLE		=> 'ct',
			USERS_TABLE			=> 'u',
		),
		'WHERE'		=> 'u.user_id = v.user_id AND ct.video_cat_id = v.video_cat_id AND u.user_id = '. $user_id,
		'ORDER_BY'	=> 'v.video_id DESC',
	);

	$sql = $db->sql_build_query('SELECT', $sql_ary);
	$result = $db->sql_query_limit($sql, $sql_limit, $sql_start);

	while ($row = $db->sql_fetchrow($result))
	{
		$page_title	= $row['username'];

		$template->assign_block_vars('video', array(
			'VIDEO_TITLE'	=> $row['video_title'],
			'VIDEO_CAT_ID'	=> $row['video_cat_id'],
			'VIDEO_CAT_TITLE'	=> $row['video_cat_title'],
			'U_CAT'			=> append_sid("{$phpbb_root_path}video.$phpEx", 'mode=cat&amp;cid=' .$row['video_cat_id']),
			'VIDEO_TIME'	=> $user->format_date($row['create_time']),
			'VIDEO_ID'		=> censor_text($row['video_id']),
			'U_VIEW_VIDEO'	=> append_sid("{$phpbb_root_path}video.$phpEx", 'mode=view&amp;id=' .$row['video_id']),
			'U_POSTER'		=> append_sid("{$phpbb_root_path}memberlist.$phpEx", array('mode' => 'viewprofile', 'u' => $row['user_id'])),
			'USERNAME'		=> get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']),
			'YOUTUBE_ID'	=> censor_text($row['youtube_id']),
		));
	}
	$db->sql_freeresult($result);

	// We need another query for the video count
	$sql = 'SELECT COUNT(*) as video_count FROM '. VIDEO_TABLE .' WHERE user_id = '. $user_id;
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

	$l_title = ($user->lang['USER_VIDEOS'] . ' - ' . $page_title);
	$template_html = 'video_search.html';

	$template->assign_block_vars('navlinks', array(
		'FORUM_NAME' 	=> ($user->lang['USER_VIDEOS'] . ' - ' . $page_title),
	));
	break;

	default:
	//Listing categories
	$sql = 'SELECT * FROM ' . VIDEO_CAT_TABLE . " ORDER BY video_cat_id";
	$res = $db->sql_query($sql);
	while($row = $db->sql_fetchrow($res))
	{
		$template->assign_block_vars('videocat', array(
			'VIDEO_CAT_ID'		=> $row['video_cat_id'],
			'VIDEO_CAT_TITLE'	=> $row['video_cat_title'],
			'U_CAT'				=> append_sid("{$phpbb_root_path}video.$phpEx", 'mode=cat&amp;cid=' .$row['video_cat_id']),
		));
	}

	// Count the videos ...
	$sql = 'SELECT COUNT(video_id) AS total_videos FROM ' . VIDEO_TABLE;
	$result = $db->sql_query($sql);
	$total_videos = (int) $db->sql_fetchfield('total_videos');
	$db->sql_freeresult($result);

	// Count the videos categories ...
	$sql = 'SELECT COUNT(video_cat_id) AS total_categories FROM ' . VIDEO_CAT_TABLE . '';
	$result = $db->sql_query($sql);
	$total_categories = (int) $db->sql_fetchfield('total_categories');
	$db->sql_freeresult($result);

	$l_title = ($user->lang['VIDEO_INDEX']);
	$template_html = 'video_body.html';

	$l_total_video_s 	= ($total_videos == 0) ? 'TOTAL_VIDEO_ZERO' : 'TOTAL_VIDEOS_OTHER';
	$l_total_category_s = ($total_categories == 0) ? 'TOTAL_CATEGORY_ZERO' : 'TOTAL_CATEGORIES_OTHER';

	$template->assign_vars(array(
		'U_VIDEO_SUBMIT' 	=> append_sid("{$phpbb_root_path}video.$phpEx", 'mode=submit'),
		'U_MY_VIDEOS'		=> append_sid(generate_board_url() ."/video.$phpEx", 'mode=user_videos&amp;user_id='. $user->data['user_id']),
		'BUTTON_VIDEO_NEW'	=> "{$web_path}styles/" . rawurlencode($user->theme['imageset_path']) . '/imageset/' . $user->lang_name .'/button_video_new.gif',
		'TOTAL_VIDEOS'		=> sprintf($user->lang[$l_total_video_s], $total_videos),
		'TOTAL_CATEGORIES'	=> sprintf($user->lang[$l_total_category_s], $total_categories),
		//'S_DISPLAY_POST_INFO'	=> ((/*$auth->acl_get('u_video_post') || */$user->data['user_id'] == ANONYMOUS)) ? true : false,
	));


	$sql_limit = ($sql_limit > 10) ? 10 : $sql_limit;
	$pagination_url = append_sid("{$phpbb_root_path}video.$phpEx");

	$sql_ary = array(
		'SELECT'	=> 'v.*,
		ct.video_cat_title,ct.video_cat_id,
		u.username,u.user_colour,u.user_id',
		'FROM'		=> array(
			VIDEO_TABLE			=> 'v',
			VIDEO_CAT_TABLE		=> 'ct',
			USERS_TABLE			=> 'u',
		),
		'WHERE'		=> 'ct.video_cat_id = v.video_cat_id AND u.user_id = v.user_id',
		'ORDER_BY'	=> 'v.video_id DESC',
	);

	$sql = $db->sql_build_query('SELECT', $sql_ary);
	$result = $db->sql_query_limit($sql, $sql_limit, $sql_start);

	while ($row = $db->sql_fetchrow($result))
	{
		$template->assign_block_vars('video', array(
			'VIDEO_TITLE'	=> $row['video_title'],
			'VIDEO_CAT_ID'	=> $row['video_cat_id'],
			'VIDEO_CAT_TITLE'	=> $row['video_cat_title'],
			'U_CAT'			=> append_sid("{$phpbb_root_path}video.$phpEx", 'mode=cat&amp;cid=' .$row['video_cat_id']),
			'VIDEO_TIME'	=> $user->format_date($row['create_time']),
			'VIDEO_ID'		=> censor_text($row['video_id']),
			'U_VIEW_VIDEO'	=> append_sid("{$phpbb_root_path}video.$phpEx", 'mode=view&amp;id=' .$row['video_id']),
			'U_POSTER'		=> append_sid("{$phpbb_root_path}memberlist.$phpEx", array('mode' => 'viewprofile', 'u' => $row['user_id'])),
			'USERNAME'		=> get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']),
			'YOUTUBE_ID'	=> censor_text($row['youtube_id']),
		));
	}
	$db->sql_freeresult($result);

	// We need another query for the video count
	$sql = 'SELECT COUNT(*) as video_count FROM ' . VIDEO_TABLE;
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

	break;
}

if (!$row) 
{
	$template->assign_vars(array(
		'NO_ENTRY'	=> ($user->lang['NO_VIDEOS']),
	));
}

// Output page
page_header($l_title, false);

$template->set_filenames(array(
	'body' => $template_html)
);

page_footer();

?>