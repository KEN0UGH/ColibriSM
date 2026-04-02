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

function cl_get_slider_images() {
	global $cl;

	$images = glob(cl_full_path(cl_strf("themes/%s/statics/img/guest/default/*.jpg", $cl["config"]["theme"])));
	$img_links = array();

	if (is_array($images)) {
		foreach($images as $img_path) {
			$path_info = explode("/", $img_path);
			$path_name = end($path_info);

			array_push($img_links, cl_link(cl_strf("themes/%s/statics/img/guest/default/%s", $cl["config"]["theme"], $path_name)));
		}
	}

	return $img_links;
}