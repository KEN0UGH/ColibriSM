<?php 
# @*************************************************************************@
# @ Software author: Mansur Terla (Mansur_TL)                               @
# @ UI/UX Designer & Web developer ;)                                       @
# @                                                                         @
# @*************************************************************************@
# @ Instagram: https://www.instagram.com/mansur_tl                          @
# @ VK: https://vk.com/mansur_tl_uiux                                       @
# @ Envato: http://codecanyon.net/user/mansur_tl                            @
# @ Behance: https://www.behance.net/mansur_tl                              @
# @ Telegram: https://t.me/mansurtl_contact                                 @
# @*************************************************************************@
# @ E-mail: mansurtl.contact@gmail.com                                      @
# @ Website: https://www.mansurtl.com                                       @
# @*************************************************************************@
# @ ColibriSM - The Ultimate Social Network PHP Script                      @
# @ Copyright (c)  ColibriSM. All rights reserved                           @
# @*************************************************************************@

function cl_get_bookmarks($user_id = false, $limit = 10, $offset = false) {
	global $db, $cl;

	if (not_num($user_id)) {
		return false;
	}

	$data          = array();
	$sql           = cl_sqltepmlate('apps/bookmarks/sql/fetch_bookmarks', array(
		't_notes'  => T_BOOKMARKS,
		't_blocks' => T_BLOCKS,
		't_posts'  => T_PUBS,
		'offset'   => $offset,
		'user_id'  => $user_id,
		'limit'    => $limit
	));

	$bookmarks = $db->rawQuery($sql);
	
	if (cl_queryset($bookmarks)) {
		foreach ($bookmarks as $row) {
			$post_data = cl_raw_post_data($row['publication_id']);

			if (not_empty($post_data)) {
				$post_data['offset_id'] = $row['bookmark_id'];
				$data[]                 = cl_post_data($post_data);
			}
		}
	}

	return $data;
}