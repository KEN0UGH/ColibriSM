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

function cl_admin_get_tags($args = array()) {
    global $db;

    $args        = (is_array($args)) ? $args : array();
    $options     = array(
        "offset"    => false,
        "limit"     => 10,
        "offset_to" => false,
        "order"     => 'DESC',
        "keyword"   => false,
    );

    $args        = array_merge($options, $args);
    $offset      = $args['offset'];
    $limit       = $args['limit'];
    $order       = $args['order'];
    $keyword     = $args['keyword'];
    $offset_to   = $args['offset_to'];
    $data        = array();
    $t_htags     = T_HTAGS;
    $sql         = cl_sqltepmlate('apps/cpanel/tags/sql/fetch_tags', array(
        'offset'    => $offset,
        't_htags'   => $t_htags,
        'limit'     => $limit,
        'offset_to' => $offset_to,
        'order'     => $order,
        'keyword'   => $keyword,
    ));

    $tags = $db->rawQuery($sql);

    if (cl_queryset($tags)) {
        foreach ($tags as $row) {
            $row['posts'] = cl_number($row['posts']);
            $row['time']  = date('d M, Y h:m', $row['time']);
            $data[]      = $row;
        }
    }

    return $data;
}
