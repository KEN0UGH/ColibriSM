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

$user_id = fetch_or_get($_GET["moder"], false);
$cl["moder_data"] = false;

if (is_posnum($user_id)) {
	$moder_data = cl_user_data($user_id);

	if (not_empty($moder_data) && $moder_data["is_root"] == "N" && $moder_data["admin"] == "1") {
		$moder_perms = cl_get_admin_perms($user_id);

		if (is_array($moder_perms)) {
			$cl["moder_data"] = $moder_data;
			$cl["moder_perms"] = $moder_perms;
		}
	}
}

$cl["app_statics"] = array(
	"scripts" => array(
		cl_static_file_path("statics/js/libs/jquery-plugins/jquery.form-v4.2.2.min.js")
	)
);

$cl['http_res'] = cl_template("cpanel/assets/edit_user_perms/content");