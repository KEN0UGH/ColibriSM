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
    $profile_id       = fetch_or_get($_GET["profile_id"], false);
	$profile_id       = cl_text_secure($profile_id);
	$creator_data = cl_user_data($profile_id);

	if (not_empty($creator_data)) {
		$cl['creator_data'] = $creator_data;

	    $data['status'] = 200;
        $data['html'] = cl_template("main/modals/subscription_form");
	}
}
else if ($action == 'subscription_pay') {
	$data['status'] = 400;
    $data['err_code'] = 0;
    $creator_id = fetch_or_get($_POST["creator_id"], false);

    if ($cl["config"]["user_wallet_status"] == "on") {
	    if (is_posnum($creator_id) && $creator_id != $me["id"]) {
	    	$creator_data = cl_raw_user_data($creator_id);

	    	if (not_empty($creator_data)) {

	    		$subscription_price = $creator_data["subscription_price"];

	    		if ($me["wallet"] >= $subscription_price) {
		    		if (cl_has_subscribed($me["id"], $creator_id) != true) {
			    		$insert_id = $db->insert(T_SUBSCRIPTIONS, array(
			    			"subscriber_id" => $me["id"],
			    			"creator_id" => $creator_id,
			    			"subscription_start" => time(),
			    			"subscription_end" => (time() + (86400 * 30))
			    		));
		    		}
		    		else{
		    			$db = $db->where("subscriber_id", $me["id"]);
		    			$db = $db->where("creator_id", $creator_id);
		    			$qr = $db->update(T_SUBSCRIPTIONS, array(
			    			"subscription_start" => time(),
			    			"subscription_end" => (time() + (86400 * 30))
			    		));
		    		}


		    		$trans_id = cl_strf("TID_%s", sha1(microtime()));
		            $data['status'] = 200;

		            $creator_money = $subscription_price;

		            if (not_empty($cl["config"]["cont_sales_comrate"]) && is_numeric($cl["config"]["cont_sales_comrate"])) {
		            	$comm_amount = (($creator_money * $cl["config"]["cont_sales_comrate"]) / 100);
		            	$creator_money = $creator_money - $comm_amount;
		            }

		            cl_update_user_data($creator_id, array(
		                "wallet" => ($creator_data["wallet"] += $creator_money)
		            ));

		            cl_update_user_data($me["id"], array(
		                "wallet" => ($me["wallet"] -= $subscription_price)
		            ));

		            $db->insert(T_WALLET_HISTORY, array(
		                "user_id" => $me["id"],
		                "operation" => "wallet_local_transfer",
		                "amount" => $subscription_price,
		                "time" => time(),
		                "status" => "success",  
		                "trans_id" => $trans_id,
		                "json_data" => cl_minify_js(json(array("username" => cl_strf("%s %s", $creator_data["fname"], $creator_data["lname"])), true))
		            ));

		            $db->insert(T_WALLET_HISTORY, array(
		                "user_id" => $creator_id,
		                "operation" => "wallet_local_receipt",
		                "amount" => $subscription_price,
		                "time" => time(),
		                "status" => "success",  
		                "trans_id" => $trans_id,
		                "json_data" => cl_minify_js(json(array("username" => $me["name"]), true))
		            ));

		            cl_notify_user(array(
		                'subject'  => 'content_subscription',
		                'user_id'  => $creator_id,
		                'entry_id' => $me["id"]
		            ));

		            if (cl_is_following($me["id"], $creator_id) != true) {
		            	cl_follow($me["id"], $creator_id);

		            	cl_follow_increase($me["id"], $creator_id);
		            }
	            }

	            else{
	            	$data['status'] = 410;
	            }
	    	}
	    }
    }
    else{
    	$data['status'] = 415;
    }
}
