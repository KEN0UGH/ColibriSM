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


$user_id = fetch_or_get($_GET["id"], false);
$user_id = (is_posnum($user_id)) ? $user_id : 0;
$cl["user_data"] = cl_raw_user_data($user_id);

if (not_empty($cl["user_data"])) {
	$cl["app_statics"] = array(
		"scripts" => array(
			cl_static_file_path("statics/js/libs/jquery-plugins/jquery.form-v4.2.2.min.js")
		)
	);

	$cl['http_res'] = cl_template("cpanel/assets/wallet_balance/content");
}
else {
	cl_redirect("404");
}