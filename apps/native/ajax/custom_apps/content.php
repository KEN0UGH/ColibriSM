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

$custom_app_name = (isset($_GET["action"])) ? $_GET["action"] : false;

$subaction = (isset($_GET["subaction"])) ? $_GET["subaction"] : false;

if (not_empty($custom_app_name) && not_empty($subaction) && file_exists(cl_strf("apps/native/ajax/%s/content.php", $custom_app_name))) {
	include_once(cl_strf("apps/native/ajax/%s/content.php", $custom_app_name));
}

else{
	$data =  array(
        "status"   => "400s",
        "err_code" => "invalid_endpoint",
        "message"  => "Invalid endpoint error on API call"
    );
}