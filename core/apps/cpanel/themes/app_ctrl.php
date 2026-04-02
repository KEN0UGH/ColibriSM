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

function cl_admin_get_themes() {
	$theme_dirs = glob(cl_full_path("themes/*"), GLOB_ONLYDIR);
	$theme_list = array();

	if (is_array($theme_dirs)) {
		foreach ($theme_dirs as $dir_path) {
			$theme_info = file_get_contents(cl_strf("%s/info.json", $dir_path));
			$theme_info = json($theme_info);

			array_push($theme_list, $theme_info);
		}
	}

    return $theme_list;
}