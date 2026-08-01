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

require_once(cl_full_path("core/apps/profile/app_ctrl.php"));

function cl_get_lists_feed($list_id = false, $owner_id = false, $limit = 30, $offset = false) {
	global $db, $cl, $me;

	if (empty($cl["is_logged"])) {
		return false;
	}

	if (is_posnum($list_id) != true || is_posnum($owner_id) != true) {
		return false;
	}

	if (empty(cl_get_user_list($list_id, $owner_id))) {
		return false;
	}

	$data = array();
	$sql  = cl_sqltepmlate("apps/lists/sql/fetch_lists_feed", array(
		"t_posts"        => T_POSTS,
		"t_pubs"         => T_PUBS,
		"t_list_members" => T_LIST_MEMBERS,
		"t_reports"      => T_PUB_REPORTS,
		"t_mutes"        => T_MUTES,
		"list_id"        => $list_id,
		"limit"          => $limit,
		"offset"         => $offset,
		"user_id"        => $me['id']
	));

	$query_res = $db->rawQuery($sql);
	$counter   = 0;

	if (cl_queryset($query_res)) {
		foreach ($query_res as $row) {
			if (cl_is_blocked($me['id'], $row['user_id']) || cl_is_blocked($row['user_id'], $me['id'])) {
				continue;
			}

			if (cl_can_view_profile($row['user_id']) != true) {
				continue;
			}

			$post_data = cl_raw_post_data($row['publication_id']);
			$is_word_muted = (not_empty($post_data) && $row['user_id'] != $me['id']) ? cl_is_text_muted($me['id'], $post_data['text']) : false;

			if (not_empty($post_data) && in_array($post_data['status'], array('active')) && empty($is_word_muted)) {
				$post_data['offset_id']   = $row['offset_id'];
				$post_data['is_repost']   = (($row['type'] == 'repost') ? true : false);
				$post_data['is_reposter'] = false;
				$post_data['attrs']       = array();

				if ($post_data['is_repost']) {
					$post_data['attrs'][]  = cl_html_attrs(array('data-repost' => $row['offset_id']));
					$reposter_data         = cl_user_data($row['user_id']);
					$post_data['reposter'] = array(
						'name' => $reposter_data['name'],
						'username' => $reposter_data['username'],
						'url' => $reposter_data['url'],
					);
				}

				if ($row['user_id'] == $me['id']) {
					$post_data['is_reposter'] = true;
				}

				$post_data['attrs'] = ((not_empty($post_data['attrs'])) ? implode(' ', $post_data['attrs']) : '');
				$data[]             = cl_post_data($post_data);
			}

			if ($cl['config']['advertising_system'] == 'on') {
				if (cl_is_feed_nad_allowed()) {
					if (not_empty($offset)) {
						if ($counter == 5) {
							$counter = 0;
							$ad      = cl_get_timeline_ads();

							if (not_empty($ad)) {
								$data[] = $ad;
							}
						}
						else {
							$counter += 1;
						}
					}
				}
			}
		}

		if ($cl['config']['advertising_system'] == 'on') {
			if (cl_is_feed_nad_allowed()) {
				if (empty($offset)) {
					$ad = cl_get_timeline_ads();

					if (not_empty($ad)) {
						$data[] = $ad;
					}
				}
			}
		}
	}

	return $data;
}