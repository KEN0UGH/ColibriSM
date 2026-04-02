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
else {

	if (file_exists($me["info_file"])) {

		header("Content-Description: File Transfer");
		header("Content-Type: application/octet-stream");
		header(cl_strf("Content-Disposition: attachment; filename=\"%s.html\"", $me['username']));
		header("Expires: 0");
		header("Cache-Control: must-revalidate");
		header("Pragma: public");
		header(cl_strf("Content-Length: %s", filesize($me["info_file"])));
		flush();
		readfile($me["info_file"]);

		unlink($me["info_file"]);

		cl_update_user_data($me['id'], array(
			'info_file' => ""
		));

		exit;
    }
    else {

    	header('Content-Type: application/json');

    	$data          =  array(
		    "status"   => "500",
		    "err_code" => "invalid_file_name",
		    "message"  => "Something went wrong while processing your request. Please try again later"
		);

		echo json_encode($data, JSON_PRETTY_PRINT);
		exit();
    }
}