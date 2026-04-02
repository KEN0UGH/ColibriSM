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

$custom_app_name = (isset($_GET["custom_app_name"])) ? $_GET["custom_app_name"] : "404";

if ($custom_app_name == "404") {
	require_once cl_full_path("apps/native/http/err404/content.php");
}

else{
	if (file_exists(cl_strf("apps/native/http/%s/content.php", $custom_app_name))) {
		include_once(cl_strf("apps/native/http/%s/content.php", $custom_app_name));
	}
	else{
		require_once cl_full_path("apps/native/http/err404/content.php");
	}
}