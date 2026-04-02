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

function cl_admin_get_withdrawal_requests($args = array()) {
	global $db;

	$args           = (is_array($args)) ? $args : array();
    $options        = array(
        "offset"    => false,
        "limit"     => 10,
        "offset_to" => false,
        "order"     => 'DESC'
    );

    $args           = array_merge($options, $args);
    $offset         = $args['offset'];
    $limit          = $args['limit'];
    $order          = $args['order'];
    $offset_to      = $args['offset_to'];
    $data           = array();
    $t_users        = T_USERS;
    $t_reqs         = T_WALLET_POUT;
    $sql            = cl_sqltepmlate('apps/cpanel/withdrawal_requests/sql/fetch_requests',array(
        'offset'    => $offset,
        't_users'   => $t_users,
        't_reqs'    => $t_reqs,
        'limit'     => $limit,
        'offset_to' => $offset_to,
        'order'     => $order
    ));

    $data     = array();
    $requests = $db->rawQuery($sql);

    if (cl_queryset($requests)) {
        foreach ($requests as $row) {
            $row['url'] = cl_link($row['username']);
            $row['avatar'] = cl_get_media($row['avatar']);
            $row['time'] = date('d M, Y h:m', $row['time']);
            $data[] = $row;
        }
    }

    return $data;
}

function cl_admin_get_withdrawal_req_data($req_id = false) {
	global $db;

	if (not_num($req_id)) {
		return array();
	}

    $data         = array();
    $t_users      = T_USERS;
    $t_reqs       = T_WALLET_POUT;
    $sql          = cl_sqltepmlate('apps/cpanel/withdrawal_requests/sql/fetch_request_data',array(
        't_users' => $t_users,
        't_reqs'  => $t_reqs,
        'req_id'  => $req_id,
    ));

    $data    = array();
    $request = $db->rawQueryOne($sql);

    if (cl_queryset($request)) {
        $request['url'] = cl_link(cl_strf('@%s', $request['username']));
        $request['avatar'] = cl_get_media($request['avatar']);
        $request['time'] = date('d M, Y h:m', $request['time']);
        $data = $request;
    }

    return $data;
}

function cl_admin_get_withdrawal_requests_total() {
	global $db;

	$qr = $db->getValue(T_WALLET_POUT, 'COUNT(*)');

	return (is_posnum($qr)) ? $qr : 0;
}