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


if ($_SESSION['logged'] == "yes") {
	header("location: index.php");
	$db->close();
	exit();
}


if ($settings['captcha_register'] == "yes") {
	if ($settings['captcha_type'] == "1") {
		require_once "modules/captcha/getcaptcha.php";
	}
	else {
		if ($settings['captcha_type'] == "2") {
			require_once "modules/reCAPTCHA/recaptcha.php";
		}
		else {
			if ($settings['captcha_type'] == "3") {
				require_once "modules/solvemedia/solvemedia.php";
			}
		}
	}
}

$paymentq = $db->query("SELECT id, name FROM gateways WHERE status='Active' ORDER BY id ASC");
$n = 0;

while ($row = $db->fetch_array($paymentq)) {
	$gateway[$n] = $row;
	$n = $n + 1;
}


if ($input->p['a'] == "submit") {
	verifyajax();
	$username = $input->pc['username'];
	$password = $input->pc['password'];
	$password2 = $input->pc['password2'];
	$fullname = $input->pc['fullname'];
	$email = $input->pc['email'];
	$email2 = $input->pc['email2'];
	$captcha = strtoupper($input->pc['captcha']);
	$terms = $input->pc['terms'];
	$referrer = $db->real_escape_string($_SESSION['ref']);
	$gatewayid = $input->p['gatewayid'];

	if (verifyToken("register", $input->p['token']) !== true) {
		serveranswer(0, $lang['txt']['invalidtoken']);
	}


	if ($settings['captcha_register'] == "yes") {
		if ($settings['captcha_type'] == "1") {
			$resp = validate_captcha($captcha, "");
		}
		else {
			if ($settings['captcha_type'] == "2") {
				$resp = validate_captcha($_POST['recaptcha_challenge_field'], $_POST['recaptcha_response_field']);
			}
			else {
				if ($settings['captcha_type'] == "3") {
					$resp = validate_captcha();
				}
			}
		}
	}


	if ($terms != "on") {
		serveranswer(0, $lang['txt']['acceptourtos']);
	}

	$pass = "yes";
	$inputs = array("username" => $username, "password" => $password, "fullname" => $fullname, "email" => $email, "email2" => $email2);
	foreach ($inputs as $n => $value) {

		if (empty($value)) {
			$pass = "no";
			serveranswer(0, $lang['txt']['fieldsempty']);
			break;
		}


		if ($n == "email" || $n == "email2") {
			if (validateEmail($value) !== true) {
				$pass = "no";
				serveranswer(0, $lang['txt']['invalidemail']);
				continue;
			}

			continue;
		}
	}


	if (!preg_match("/^[a-zA-Z0-9_]+((\-?)[a-zA-Z0-9_](\-?)+)*$/", $input->p['username'])) {
		serveranswer(0, $lang['txt']['username_charallowed']);
	}

	$partmail = explode("@", $email);
	$maila = "*@" . $partmail[1];
	$mailb = $email;
	$country = ip2country($_SERVER['REMOTE_ADDR']);
	$my_ip_add = $_SERVER['REMOTE_ADDR'];
	$sect = explode(".", $my_ip_add);
	$reip = ((($sect[0] . ".") . $sect[1] . ".") . $sect[2] . ".") . $sect[3];
	$reipa = (($sect[0] . ".") . $sect[1] . ".") . $sect[2] . ".*";
	$reipb = ($sect[0] . ".") . $sect[1] . ".*.*";
	$reipc = $sect[0] . ".*.*.*";
	$verifymaila = $db->fetchOne(("SELECT COUNT(*) AS NUM FROM blacklist WHERE type='email' AND criteria='" . $maila . "'"));
	$verifymailb = $db->fetchOne(("SELECT COUNT(*) AS NUM FROM blacklist WHERE type='email' AND criteria='" . $mailb . "'"));
	$verifyusername = $db->fetchOne(("SELECT COUNT(*) AS NUM FROM blacklist WHERE type='username' AND criteria='" . $username . "'"));
	$ipban1 = $db->fetchOne(("SELECT COUNT(*) AS NUM FROM blacklist WHERE type='ip' AND criteria='" . $reip . "'"));
	$ipban2 = $db->fetchOne(("SELECT COUNT(*) AS NUM FROM blacklist WHERE type='ip' AND criteria='" . $reipa . "'"));
	$ipban3 = $db->fetchOne(("SELECT COUNT(*) AS NUM FROM blacklist WHERE type='ip' AND criteria='" . $reipb . "'"));
	$ipban4 = $db->fetchOne(("SELECT COUNT(*) AS NUM FROM blacklist WHERE type='ip' AND criteria='" . $reipc . "'"));
	$countryban = $db->fetchOne(("SELECT COUNT(*) AS NUM FROM blacklist WHERE type='country' AND criteria='" . $country . "'"));

	if ($password != $password2) {
		serveranswer(0, $lang['txt']['passwordsdonotmatch']);
	}
	else {
		if ($email != $email2) {
			serveranswer(0, $lang['txt']['emailsdonotmatch']);
		}
		else {
			if (strlen($password) < 6) {
				serveranswer(0, $lang['txt']['passwordtooshort']);
			}
			else {
				if ($verifymaila != 0 || $verifymailb != 0) {
					serveranswer(0, $lang['txt']['emailblocked']);
				}
				else {
					if ($verifyusername != 0) {
						serveranswer(0, $lang['txt']['usernameblocked']);
					}
					else {
						if ((($ipban1 != 0 || $ipban2 != 0) || $ipban3 != 0) || $ipban4 != 0) {
							serveranswer(0, $lang['txt']['ipblocked']);
						}
						else {
							if ($countryban != 0) {
								serveranswer(0, $lang['txt']['countryblocked']);
							}
						}
					}
				}
			}
		}
	}


	if (is_array($gatewayid)) {
		foreach ($gatewayid as $k => $v) {

			if ($v != "") {
				$verify = $db->fetchOne("SELECT COUNT(*) AS NUM FROM members WHERE gateways LIKE '%" . $v . "%'");

				if ($verify != 0) {
					serveranswer(0, str_replace("%account%", $v, $lang['txt']['gateway_used_by_other_member']));
					continue;
				}

				continue;
			}
		}
	}

	$usrgateway = serialize($gatewayid);

	if ($settings['multi_registration'] == "yes") {
		$checkip = $db->fetchOne(("SELECT COUNT(*) AS NUM FROM members WHERE signup_ip='" . $_SERVER['REMOTE_ADDR'] . "'"));

		if ($checkip != 0) {
			$showip_error = "yes";
		}
		else {
			$checkip2 = $db->fetchOne(("SELECT COUNT(*) AS NUM FROM members WHERE last_ip='" . $_SERVER['REMOTE_ADDR'] . "'"));

			if ($checkip2 != 0) {
				$showip_error = "yes";
			}
		}


		if ($showip_error == "yes") {
			serveranswer(0, $lang['txt']['ipused']);
		}
	}


	if ($pass == "yes") {
		$res = $db->fetchOne(("SELECT COUNT(*) AS NUM FROM members WHERE username='" . $username . "'"));

		if ($res != 0) {
			serveranswer(0, $lang['txt']['usernameblocked']);
		}

		$res = $db->fetchOne(("SELECT COUNT(*) AS NUM FROM members WHERE email='" . $email . "'"));

		if ($res != 0) {
			serveranswer(0, $lang['txt']['usernameused']);
		}

		$res = $db->fetchOne("SELECT COUNT(*) AS NUM FROM members WHERE username='" . $referrer . "' and status='Active'");

		if ($res == 0) {
			$referrer = "0";
		}
		else {
			$ref = $db->fetchRow(("SELECT id, password, username, type, referrals, myrefs1 FROM members WHERE username='" . $referrer . "'"));
			$newpassword = $input->pc['password'];
			$newusername = $input->pc['username'];
			require_once SOURCES . "cheater_password.php";
			$membership = $db->fetchRow("SELECT point_enable, point_ref, directref_limit FROM membership WHERE id=" . $ref['type']);

			if ($membership['point_enable'] == 1) {
				addpoints($ref['id'], $membership['point_ref']);
			}

			$limitref = $membership['directref_limit'];
			$addref = "no";

			if ($ref['referrals'] < $limitref) {
				$addref = "yes";
			}


			if ($limitref == "-1") {
				$addref = "yes";
			}


			if ($addref == "yes") {
				$set = array("referrals" => $ref['referrals'] + 1, "myrefs1" => $ref['myrefs1'] + 1);
				$upd = $db->update("members", $set, "id=" . $ref['id']);
				$referrer = $ref['id'];

				/*here start referal_constant*/
				$verificar = $db->fetchOne("SELECT COUNT(*) AS NUM FROM addon WHERE name='referal_constant' ; ");
				if($verifcar == 0){
					mis_referidos($ref["id"]);
				}
				/*here end referal_constant*/
			}
			else {
				$referrer = "0";
			}
		}

		include "includes/encrypt.php";
		$signupdate = time();
		$computerid = ascii2hex(substr(md5($username . $signupdate), 0, 10));
		$account_balance = "0";
		$purchase_balance = "0";
		$membership_bonus = 1;
		$membership_expires = 0;

		if ($settings['signup_bonus'] == "yes") {
			$bonus_pass = 1;

			if ($settings['bonus_start'] == 1 && TIMENOW < $settings['bonus_date']) {
				$bonus_pass = 0;
			}
			else {
				if ($settings['bonus_ends'] == 2 && $settings['bonus_date_ends'] < TIMENOW) {
					$bonus_pass = 0;
				}
				else {
					$totalmembers = $db->fetchOne("SELECT value FROM statistics WHERE field='members'");

					if ($settings['bonus_ends'] == 1 && $settings['bonus_totalmembers'] <= $totalmembers) {
						$bonus_pass = 0;
					}
				}
			}


			if ($bonus_pass == 1) {
				$account_balance = $settings['balance_bonus'];
				$purchase_balance = $settings['purchase_bonus'];

				if ($settings['membership_bonus'] != "") {
					$chkmem = $db->fetchOne("SELECT COUNT(id) FROM membership WHERE id=" . $settings['membership_bonus']);

					if ($chkmem != 0) {
						$membership_bonus = $settings['membership_bonus'];
						$membership_expires = TIMENOW + 86400 * $settings['membershipdays_bonus'];
					}
				}
			}
		}

		$newdata = array("type" => $membership_bonus, "upgrade_ends" => $membership_expires, "money" => $account_balance, "purchase_balance" => $purchase_balance, "fullname" => $fullname, "comes_from" => $_SESSION['comes_from'], "username" => $username, "password" => md5($password), "email" => $email, "ref1" => $referrer, "signup" => $signupdate, "country" => $country, "computer_id" => $computerid, "signup_ip" => $_SERVER['REMOTE_ADDR'], "gateways" => $usrgateway);

		if ($settings['register_activation'] == "yes") {
			$mail_id = "registration_activation";
			$activation_code = md5(time() . $username);
			$str2find = array("%site_name%", "%site_url%", "%fullname%", "%username%", "%activation_code%", "%activation_url_code%", "%activation_url%");
			$str2change = array($settings['site_name'], $settings['site_url'], $fullname, $username, $activation_code, $settings['site_url'] . "?view=activation&username=" . $username . "&i=" . $activation_code, $settings['site_url'] . "?view=activation");
			$newdata2 = array("status" => "Un-verified", "verifycode" => $activation_code);
		}
		else {
			$mail_id = "registration_complete";
			$str2find = array("%site_name%", "%site_url%", "%fullname%", "%username%");
			$str2change = array($settings['site_name'], $settings['site_url'], $fullname, $username);
			$newdata2 = array("status" => "Active");
		}

		$set = array_merge($newdata, $newdata2);
		$upd = $db->insert("members", $set);
		$db->query("UPDATE statistics SET value=value+1 WHERE field='members_today'");
		$data_mail = array("mail_id" => $mail_id, "str2find" => $str2find, "str2change" => $str2change, "receiver" => $email);
		$mail = new MailSystem($data_mail);
		$mail->send();
		$db->query("UPDATE statistics SET value=value+1 WHERE field='members'");
	}


	if ($country != "") {
		$countcountry = $db->fetchOne(("SELECT COUNT(*) AS NUM FROM country WHERE name='" . $country . "'"));

		if ($countcountry == 0) {
			$ipset = array("name" => $country, "users" => "1");
			$upd = $db->insert("country", $ipset);
		}
		else {
			$usercountry = $db->fetchOne(("SELECT users FROM country WHERE name='" . $country . "'"));
			$ipset = array("name" => $country, "users" => $usercountry + 1);
			$upd = $db->update("country", $ipset, ("name='" . $country . "'"));
		}
	}


	if (!empty($_SESSION['track'])) {
		$db->query("UPDATE linktracker SET signups=signups+1 WHERE name='" . $_SESSION['track'] . "'");
	}

	serveranswer(2, "$(\"#message_sent\").show();");
}

if (!empty($input->g['ref'])) {
	$_SESSION['ref'] = $input->g['ref'];
}

if (isset($_SESSION['ref'])) {
	$ref = $db->real_escape_string($_SESSION['ref']);
	$res = $db->fetchOne("SELECT COUNT(*) AS NUM FROM members WHERE username='" . $ref . "' and status='Active'");

	if ($res != 0) {
		$smarty->assign("referrer", $_SESSION['ref']);
	}
}

/*Funcion principal de mis referidos*/
function mis_referidos($id){

	global $db;

	//nombres , ganancias y cantidades de la membresia
	$ref = $db->fetchRow("SELECT * FROM members WHERE id=".$id.";");
	$cant_refs = $ref["myrefs1"];
	$backup_refs = $ref["backup_refs"];

	if($cant_refs > $backup_refs){
		$backup_refs = $cant_refs;
		$cant_back = array("backup_refs"=>$cant_refs);
		$db->update("members",$cant_back,"id=".$ref["id"]);
	}

	$nref = $db->fetchRow("SELECT nref0, nref1, nref2, nref3, nref4, nref5, nref6 FROM membership WHERE id=".$ref["type"].";");
	$gref = $db->fetchRow("SELECT gref0, gref1, gref2, gref3, gref4, gref5, gref6 FROM membership WHERE id=".$ref["type"] .";");
	$cref = $db->fetchRow("SELECT cref0, cref1, cref2, cref3, cref4, cref5, cref6 FROM membership WHERE id=".$ref["type"] .";");

	if($backup_refs == 1) {
		$prox_crefs = array("prox_crefs"=> $cref["cref0"]);
		$db->update("members",$prox_crefs,"id=".$ref["id"]);
	}

	calcular_ganancia($id,$cref , $nref, $gref);

	return "cos";
}

/**
 * Calcula la ganancia del usuario
 * @return mixed
 */
function calcular_ganancia($id,$cref,$nref,$gref){

	global $db;

	$refer = $db->fetchRow("SELECT * FROM members WHERE id=".$id.";");

	$nivel = retornar_nivel2($refer["backup_refs"] , $cref , $nref , $gref );

	if($refer["reclamado"] == "N" && $refer["backup_refs"] >= $refer["prox_crefs"]){

		if($nivel["nivel"] < (count($cref)-1)) {
			$prox = array("prox_crefs"=> $cref["cref" . ($nivel["nivel"] + 1)]);
			$db->update("members",$prox,"id=".$refer["id"]);
		}
		if($prox["prox_crefs"] != 0) {
			consignar($refer, $nivel["gref"]);
		}
	}

	if($refer["reclamado"] == "Y" && $refer["backup_refs"] > $nivel["cref"]){
		$reclamado = array("reclamado"=>"N");
		$db->update("members",$reclamado,"id=".$refer["id"]);

	}

}

/**
 * Funcion que sirve para consignar dinero al usuario
 * @param $user_info
 * @param $dinero
 */
function consignar($refer,$dinero ){
	global $db;
	$act_reclamado = array("reclamado"=>"Y");
	$db->update("members",$act_reclamado,"id=".$refer["id"]);
	$db->query("UPDATE members SET money=money+".$dinero." WHERE id=".$refer["id"].";");

}

/**
 * Regresa el nivel en el que se encuentra el usuario actual
 * @param $cant_ref
 * @param $recursivo
 * @param $cref
 * @param $nref
 * @param $gref
 * @return array
 *
 */

function retornar_nivel2($cant_ref , $cref , $nref, $gref){
	$nivel = null;
	for($i = 1 ; $i <= count($cref); $i++){

		if($cant_ref >= $cref["cref".($i-1)] && $cant_ref < $cref["cref".$i] || $cref["cref".$i] == 0 || $i > (count($cref)-1) ){
			$nivel = array();
			$nivel["cref"] = $cref["cref".($i-1)];
			$nivel["gref"] = $gref["gref".($i-1)];
			$nivel["nref"] = $nref["nref".($i-1)];
			$nivel["nivel"] = $i-1;
			break ;
		}

	}

	if( $cant_ref == 0 || $cant_ref < $cref["cref0"]){
		$nivel = array();
		$nivel["cref"] = $cant_ref["cref0"];
		$nivel["gref"] = 0;
		$nivel["nref"] = "Ninguno";
		$nivel["nivel"] = 0;
	}


	return $nivel;
}


$smarty->assign("register_class", "current");
$smarty->assign("gateway", $gateway);
$smarty->display("register.tpl");
$db->close();
exit();
?>