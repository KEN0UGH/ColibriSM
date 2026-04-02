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

function cl_get_account_wallet_history($offset = false, $limit = 10) {
	global $db, $cl;

	$data    = array();
	$db      = $db->where('user_id', $cl['me']['id']);
	$db      = (is_posnum($offset)) ? $db->where('id', $offset, '<') : $db;
	$db      = $db->orderBy('id','DESC');
	$history = $db->get(T_WALLET_HISTORY, $limit);

	if (cl_queryset($history)) {
		foreach ($history as $row) {
			$row['time']      = cl_time2str($row['time']);
			$row['json_data'] = json($row['json_data']);
			$data[]           = $row;
		}
	}

	return $data;
}