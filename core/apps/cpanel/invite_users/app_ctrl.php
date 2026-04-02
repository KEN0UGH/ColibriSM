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

function cl_admin_get_user_invitations() {
	global $db;

	$db->where("expires_at", time(), "<")->delete(T_USER_INVITES);

	$db      = $db->orderBy("id", "DESC");
	$invites = $db->get(T_USER_INVITES);
	$data    = array();

	if (cl_queryset($invites)) {
		foreach ($invites as $itemdata) {
			$itemdata["time"]        = date("d M, Y h:i", $itemdata["time"]);
			$itemdata["expire_time"] = date("d M, Y h:i", $itemdata["expires_at"]);
			$itemdata["link"]        = cl_link(cl_strf("guest?auth=signup&invite_code=%s", $itemdata["code"]));
			$itemdata["link_short"]  = cl_croptxt($itemdata["link"], 45, "...");


			array_push($data, $itemdata);
		}
	}

	return $data;
}