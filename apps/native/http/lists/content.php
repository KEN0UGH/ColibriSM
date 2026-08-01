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

if (empty($cl['is_logged'])) {
	cl_redirect("guest");
}

else {
	require_once(cl_full_path("core/apps/lists/app_ctrl.php"));

	$cl["page_title"]       = cl_translate("Lists");
	$cl["page_desc"]        = $cl["config"]["description"];
	$cl["page_kw"]          = $cl["config"]["keywords"];
	$cl["pn"]               = "lists";
	$cl["sbr"]              = true;
	$cl["sbl"]              = true;
	$cl["user_lists"]       = cl_get_user_lists($me['id']);
	$cl["list_members"]     = array();
	$cl["list_feed"]        = array();
	$cl["current_list"]     = false;
	$cl["prefill_username"] = cl_text_secure(fetch_or_get($_GET['add'], ''));
	$cl["prefill_username"] = preg_replace('/[^A-Za-z0-9_]/', '', ltrim($cl["prefill_username"], '@'));
	$list_id                = fetch_or_get($_GET['list_id'], 0);

	if (empty($list_id) && not_empty($cl['prefill_username']) && not_empty($cl['user_lists'])) {
		$list_id = $cl['user_lists'][0]['id'];
	}

	if (is_posnum($list_id)) {
		$cl["current_list"] = cl_get_user_list($list_id, $me['id']);

		if (empty($cl['current_list'])) {
			require_once cl_full_path("apps/native/http/err404/content.php");
		}

		else {
			$cl["list_members"] = cl_get_list_members($list_id, $me['id'], 50);
			$cl["list_feed"]    = cl_get_lists_feed($list_id, $me['id'], 30);
			$cl["http_res"]     = cl_template("lists/content");
		}
	}

	else {
		$cl["http_res"] = cl_template("lists/content");
	}
}