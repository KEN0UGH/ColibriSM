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

else{
	$cl["page_title"]    = cl_translate("Account settings");
	$cl["page_desc"]     = $cl["config"]["description"];
	$cl["page_kw"]       = $cl["config"]["keywords"];
	$cl["pn"]            = "settings";
	$cl["sbr"]           = true;
	$cl["sbl"]           = true;
	$cl["blocked_users"] = cl_get_blocked_users();
	$cl["settings_app"]  = fetch_or_get($_GET["sapp"], false);
	$cl["settings_app"]  = (not_empty($cl["settings_app"])) ? cl_text_secure($cl["settings_app"]) : 0;
	$cl["settings_apps"] = array("name", "email", "phone", "siteurl", "bio", "gender", "password", "language", "country", "city", "verification", "privacy", "notifications", "blocked", "delete", "information", "email_notifs", "cont_monetization", "social_links");
	$cl["page_tab"] = fetch_or_get($_GET["stab"], false);

	if (not_empty($cl["settings_app"]) && in_array($cl["settings_app"], $cl["settings_apps"])) {
		if ($cl["settings_app"] == "email_notifs" && $cl["config"]["email_notifications"] == "off") {
			require_once cl_full_path("apps/native/http/err404/content.php");
		}

		else if ($cl["settings_app"] == "premium" && $cl["config"]["prem_account_system_status"] == "off") {
			require_once cl_full_path("apps/native/http/err404/content.php");
		}

		else if ($cl["settings_app"] == "phone" && $cl["config"]["signup_conf_system"] != "phone") {
			require_once cl_full_path("apps/native/http/err404/content.php");
		}
		
		else{

			if ($cl["settings_app"] == "cont_monetization") {

				if (empty($cl["page_tab"])) {
					$cl["page_tab"] = "active_sub";
				}

				require_once(cl_full_path("core/apps/monetization/app_ctrl.php"));

				$cl["profile_total_active_subs"] = cl_get_profile_total_subscribers("active_sub");
				$cl["profile_total_inactive_subs"] = cl_get_profile_total_subscribers("inactive_sub");


				if ($cl["page_tab"] == "active_sub") {
					$cl["profile_subscribers"] = cl_get_profile_subscribers("active");
				}
				else{
					$cl["profile_subscribers"] = cl_get_profile_subscribers("inactive");
				}
			}

			$cl["http_res"] = cl_template(cl_strf("settings/includes/%s", $cl["settings_app"]));
		}
	}

	else{
		$cl["http_res"] = cl_template("settings/content");
	}
}


