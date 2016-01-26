<?php
/**
 *
 * @ EvolutionScript FULL DECODED & NULLED
 *
 * @ Version  : 5.1
 * @ Author   : MTIMER
 * @ Release on : 2014-12-28
 * @ Website  : http://www.mtimer.net
 *
 **/

if (!defined("EvolutionScript")) {
	exit("Hacking attempt...");
}


if ($_SESSION['logged'] != "yes") {
	header("location: ./");
	exit();
}

$pages = array("summary" => "summary.php", "upgrade" => "upgrade.php", "withdraw" => "withdraw.php", "buy" => "buy.php", "checkout" => "checkout.php", "banners" => "banners.php", "settings" => "settings.php", "referrals" => "referrals.php", "referrals" => "referrals.php", "rented_referrals" => "rented_referrals.php", "buyreferrals" => "buy_referrals.php", "addfunds" => "addfunds.php", "rentreferrals" => "rent_referrals.php", "history" => "history.php", "login" => "login_history.php", "manageads" => "manageads.php", "adcontrol" => "adcontrol.php", "validate" => "validate.php", "newad" => "newad.php", "createad" => "createad.php", "allocate" => "allocate_credits.php", "withdraw_history" => "withdraw_history.php", "deposit_history" => "deposit_history.php", "allocate_credits" => "allocate.php", "statistics" => "statistics.php", "thankyou" => "thankyou.php", "ptsu" => "ptsu.php", "ptsu_history" => "ptsu_history.php", "pending_ptsu" => "pending_ptsu.php", "profitcalculator" => "profit_calculator.php", "messages" => "messages.php", "forum_settings" => "forum_settings.php", "ptcmaxclicks" => "ptcmaxclicks.php", "convert_points" => "convert_points.php" , "referal_constant"=>"referal_constant.php");

if (!isset($_GET['page']) || !isset($pages[$_GET['page']])) {
	include "Sources/summary.php";
}
else {
	include "Sources/" . $pages[$_GET['page']];
}

$db->close();
exit();
?>