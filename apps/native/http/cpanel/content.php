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

if (empty($cl["is_logged"])) {
	require_once cl_full_path("apps/native/http/err404/content.php");
}

else if(empty($cl['is_admin'])) {
	require_once cl_full_path("apps/native/http/err404/content.php");
}
else{
	$cl["page_title"] = cl_translate("Control panel");
	$cl["page_desc"]  = $cl["config"]["description"];
	$cl["page_kw"]    = $cl["config"]["keywords"];
	$cl["pn"]         = "cpanel";
	$cl["cp_section"] = fetch_or_get($_GET['section'], 'dashboard');
	$section_handler  = cl_full_path(cl_strf('apps/native/http/cpanel/sections/%s.php', $cl["cp_section"]));

	if (file_exists($section_handler)) {

		if ($cl["cp_section"] == "dashboard") {
			require_once($section_handler);
		}
		else{
			if ($me["is_root"] == "Y") {
				require_once($section_handler);
			}
			else{
				$moder_perms = cl_get_admin_perms($me["id"]);

				if (is_array($moder_perms) && isset($moder_perms[$cl["cp_section"]]) && in_array($cl["cp_section"], array_keys($cl['morer_perms']))) {
					require_once($section_handler);
				}

				else{
					$section_handler = cl_full_path("apps/native/http/cpanel/sections/access_denied.php");

					require_once($section_handler);
				}
			}
		}

		echo cl_template("cpanel/content");
		exit();
	}
	else {
		require_once cl_full_path("apps/native/http/err404/content.php");
	}
}

