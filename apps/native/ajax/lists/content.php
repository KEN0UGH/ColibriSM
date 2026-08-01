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

require_once(cl_full_path("core/apps/lists/app_ctrl.php"));

if (empty($cl["is_logged"])) {
	$data['status'] = 400;
	$data['error']  = 'Invalid access token';
}

else if ($action == 'fetch') {
	$data['status'] = 200;
	$data['items']  = array();
	$items          = cl_get_user_lists($me['id']);

	if (not_empty($items)) {
		$data['items'] = $items;
	}
}

else if ($action == 'load_more') {
	$data['err_code'] = 0;
	$data['status']   = 400;
	$list_id          = fetch_or_get($_GET['list_id'], 0);
	$offset           = fetch_or_get($_GET['offset'], 0);
	$html_arr         = array();

	if (is_posnum($list_id) && is_posnum($offset) && not_empty(cl_get_user_list($list_id, $me['id']))) {
		$posts = cl_get_lists_feed($list_id, $me['id'], 30, $offset);

		if (not_empty($posts)) {
			foreach ($posts as $cl['li']) {
				$html_arr[] = cl_template('timeline/post');
			}

			$data['status'] = 200;
			$data['html']   = implode('', $html_arr);
		}
	}
}

else if ($action == 'load_more_members') {
	$data['err_code'] = 0;
	$data['status']   = 400;
	$list_id          = fetch_or_get($_GET['list_id'], 0);
	$offset           = fetch_or_get($_GET['offset'], 0);
	$html_arr         = array();

	if (is_posnum($list_id) && is_posnum($offset) && not_empty(cl_get_user_list($list_id, $me['id']))) {
		$members = cl_get_list_members($list_id, $me['id'], 50, $offset);

		if (not_empty($members)) {
			foreach ($members as $cl['li']) {
				$html_arr[] = cl_template('lists/includes/member_item');
			}

			$data['status'] = 200;
			$data['html']   = implode('', $html_arr);
		}
	}
}

else if ($action == 'create') {
	$data['status'] = 400;
	$name           = cl_text_secure(fetch_or_get($_POST['name'], ''));
	$about          = cl_text_secure(fetch_or_get($_POST['about'], ''));

	if (not_empty($name) && (mb_strlen($name) <= 60) && (mb_strlen($about) <= 190)) {
		$insert_id = cl_db_insert(T_LISTS, array(
			'owner_id' => $me['id'],
			'name'     => $name,
			'about'    => $about,
			'time'     => time()
		));

		if (is_posnum($insert_id)) {
			$data['status']       = 200;
			$data['list_id']      = $insert_id;
			$data['redirect_url'] = cl_link(cl_strf('lists/%s', $insert_id));
		}
	}

	else {
		$data['error'] = 'Please enter a list name up to 60 characters';
	}
}

else if ($action == 'delete') {
	$data['status'] = 400;
	$list_id        = fetch_or_get($_POST['list_id'], 0);
	$list_data      = cl_get_user_list($list_id, $me['id']);

	if (not_empty($list_data)) {
		cl_db_delete_item(T_LIST_MEMBERS, array(
			'list_id' => $list_id
		));

		cl_db_delete_item(T_LISTS, array(
			'id'       => $list_id,
			'owner_id' => $me['id']
		));

		$data['status']       = 200;
		$data['redirect_url'] = cl_link('lists');
	}
}

else if ($action == 'add_member') {
	$data['status'] = 400;
	$list_id        = fetch_or_get($_POST['list_id'], 0);
	$username       = cl_text_secure(fetch_or_get($_POST['username'], ''));
	$username       = preg_replace('/[^A-Za-z0-9_]/', '', ltrim($username, '@'));
	$list_data      = cl_get_user_list($list_id, $me['id']);

	if (not_empty($list_data) && not_empty($username)) {
		$user_data = cl_get_user_by_name($username);

		if (empty($user_data) || $user_data['active'] != '1') {
			$data['error'] = 'User not found';
		}

		else if (cl_is_blocked($me['id'], $user_data['id']) || cl_is_blocked($user_data['id'], $me['id'])) {
			$data['error'] = 'You cannot add this user to a list';
		}

		else if (cl_can_view_profile($user_data['id']) != true) {
			$data['error'] = 'This profile is not available for your lists';
		}

		else if (cl_is_list_member($list_id, $user_data['id'], $me['id'])) {
			$data['error'] = 'This user is already in the list';
		}

		else {
			$insert_id = cl_db_insert(T_LIST_MEMBERS, array(
				'list_id'   => $list_id,
				'member_id' => $user_data['id'],
				'time'      => time()
			));

			if (is_posnum($insert_id)) {
				$data['status'] = 200;
			}
		}
	}

	else {
		$data['error'] = 'Select a list and enter a valid username';
	}
}

else if ($action == 'remove_member') {
	$data['status'] = 400;
	$list_id        = fetch_or_get($_POST['list_id'], 0);
	$member_id      = fetch_or_get($_POST['member_id'], 0);
	$list_data      = cl_get_user_list($list_id, $me['id']);

	if (not_empty($list_data) && is_posnum($member_id) && cl_is_list_member($list_id, $member_id, $me['id'])) {
		cl_db_delete_item(T_LIST_MEMBERS, array(
			'list_id'   => $list_id,
			'member_id' => $member_id
		));

		$data['status'] = 200;
	}
}