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
    $data['status'] = 400;
    $data['error']  = 'Invalid access token';
}

else if ($action == 'get_form') {
	$data['status']   = 400;
    $data['err_code'] = 0;
    $post_id          = fetch_or_get($_GET["id"], false);
	$post_id          = cl_text_secure($post_id);
	$cl['post_data']  = cl_raw_post_data($post_id);

	if (not_empty($cl['post_data'])) {
		$cl['post_data'] = cl_post_data($cl['post_data']);

	    $data['status'] = 200;
        $data['html']   = cl_template('timeline/modals/donate');
	}
}

else if ($action == 'send_donation') {
	$data['status']   = 400;
    $data['err_code'] = 0;
    $post_id          = fetch_or_get($_POST["id"], false);
	$post_id          = cl_text_secure($post_id);
	$post_data        = cl_raw_post_data($post_id);
	$donation_amount  = fetch_or_get($_POST["donate_amount"], false);

    if ($cl["config"]["user_wallet_status"] == "on") {
    	if (not_empty($post_data) && is_posnum($donation_amount) && $donation_amount > 0 && $donation_amount < 20000) {
    		
    		$recipient_data = cl_raw_user_data($post_data["user_id"]);

    		if ($donation_amount <= $me["wallet"] && not_empty($recipient_data)) {

    			cl_update_user_data($recipient_data["id"], array(
                    "wallet" => ($recipient_data["wallet"] += $donation_amount)
                ));

                cl_update_user_data($me["id"], array(
                    "wallet" => ($me["wallet"] -= $donation_amount)
                ));

                $db->insert(T_WALLET_HISTORY, array(
                    "user_id" => $me["id"],
                    "operation" => "wallet_local_transfer",
                    "amount" => $donation_amount,
                    "time" => time(),
                    "status" => "success"
                ));

                $db->insert(T_WALLET_HISTORY, array(
                    "user_id" => $recipient_data["id"],
                    "operation" => "wallet_local_receipt",
                    "amount" => $donation_amount,
                    "time" => time(),
                    "status" => "success"
                ));

                $donations_total = ($post_data["donations_total"] += 1);
                $donation_raised = ($post_data["donation_raised"] += $donation_amount);
                $donation_raised_percent = cl_percentage_of($donation_raised, $post_data["donation_amount"]);

                if ($donation_raised_percent > 100) {
                	$donation_raised_percent = 100;
                }

                cl_update_post_data($post_data["id"], array(
                	"donations_total" => $donations_total,
                	"donation_raised" => $donation_raised,
                	"donation_raised_percent" => $donation_raised_percent
                ));

                $data['status'] = 200;
                $data['stats'] = array(
                	"donation_raised" => cl_money($donation_raised),
                	"donations_total" => $donations_total,
                	"donation_raised_percent" => $donation_raised_percent,
                	"donations_left_amount" => cl_money($post_data["donation_amount"] - $donation_raised)
                );

                if ($donation_raised >= $post_data["donation_amount"]) {
                	$data['stats']["donations_left_amount"] = cl_money("0.00");
                }
    		}
    	}
    }
    else{
        $data['status'] = 415;
    }
}

