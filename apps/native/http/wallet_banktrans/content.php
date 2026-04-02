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
	cl_redirect("guest");
}
else if($cl["config"]["user_wallet_status"] != "on") {
	require_once cl_full_path("apps/native/http/err404/content.php");
}
else{
	$transfer_sid = fetch_or_get($_GET["sid"], false);

	$bank_trans_session = cl_session("bank_trans_session");

	if ($cl["config"]["bank_method_status"] != "on" || empty($transfer_sid) || empty($bank_trans_session) || $bank_trans_session["sess_id"] != $transfer_sid) {
		require_once cl_full_path("apps/native/http/err404/content.php");
	}

	else {
		$cl["page_title"] = cl_translate("Bank transfer");
		$cl["page_desc"]  = $cl["config"]["description"];
		$cl["page_kw"]    = $cl["config"]["keywords"];
		$cl["pn"]         = "wallet_banktrans";
		$cl["sbr"]        = true;
		$cl["sbl"]        = true;
		$cl["http_res"]   = cl_template("wallet_banktrans/content");
	}
}