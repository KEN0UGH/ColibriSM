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

$cl['follow_suggestion'] = cl_get_follow_suggestions(5);
$cl['hot_topics']        = cl_get_hot_topics(15);
$cl['visitor_uniqid']    = null;

if (empty($_COOKIE['visid'])) {
	$cl_unid_hash = sha1(rand(11111, 99999)) . time() . md5(microtime());

	setcookie("visid", $cl_unid_hash, strtotime("+ 1 year"), '/') or die('unable to create cookie');
}

else {
	$cl['visitor_uniqid'] = $_COOKIE['visid'];
}
