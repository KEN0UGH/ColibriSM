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
	require_once(cl_full_path("core/apps/subscriptions/app_ctrl.php"));

	$cl["page_title"] = cl_translate("Subscriptions");
	$cl["page_desc"] = $cl["config"]["description"];
	$cl["page_kw"] = $cl["config"]["keywords"];
	$cl["pn"] = "subscriptions";
	$cl["sbr"] = true;
	$cl["sbl"] = true;
	$cl["subscriptions"] = cl_get_my_subscriptions();
	$cl["http_res"] = cl_template("subscriptions/content");
}