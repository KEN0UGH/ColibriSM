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

function cl_get_affiliate_payouts($offset = false, $limit = false) {
	global $db, $cl;

	if (empty($cl['is_logged'])) {
		return array();
	}

	$data          = array();
	$sql           = cl_sqltepmlate('apps/affiliates/sql/fetch_history', array(
		't_affils' => T_AFF_PAYOUTS,
		'offset'   => $offset,
		'user_id'  => $cl['me']['id'],
		'limit'    => $limit
	));

	$history = $db->rawQuery($sql);
	
	if (cl_queryset($history)) {
		foreach ($history as $row) {
			$row['amount'] = cl_money($row['amount']);
			$row['time']   = date('d M, Y h:m', $row['time']);
			$data[]        = $row;
		}
	}

	return $data;
}