<?php
require_once('lib_array_and_objects.php');
require_once('lib_convert.php');

/* just very general functions, is included via config.php
 * so that it is available to all files */

/* when a group of checkboxes is transmitted via form
 * name="checkbox_group_root"
 * name="checkbox_group_users"
 * ... one wants to extract all this group info into one array/object
 */
function getREQUESTSstarting($with)
{
	$result = array();
	$count = strlen($with);
	foreach ($_REQUEST as $key => $value)
	{
		$substring = substr($key, 0, $count);
		if($substring == $with)
		{
			$result[$key] = $value;
		}
	}
	
	return $result;
}

/* define the format of the server answer/result/message
 *
 * ===== input:
 * 
 * a bunch of strings ($action,$resultType,$resultValue,$details)
 * 
 * $additionalResult -> is just any array with additional data, that the server wants to pass to the client.
 * 
 * ===== output:
 *
 * right now the format is like this:
 * 
 * action: what action/operation the client is doing with the server
 * resultType: success/failed, true/false - if it is a positive or a negative result
 * resultValue: the value/data of the result - in case of login it is simply "success" (same as resultType)
 * details: a message explaining the result
 * 
 * the string is JSON formatted, so it can be easily converted into a javascript/jquery-object on the clientside via jQuery.parseJSON.
 * 
 * example:
 * {action":"login","resultType":"success","resultValue":"success","details":"you have now access. live long and prosper! Login expires in 30 minutes."}
 * 
 * */
function answer($result = null,$action = "",$resultType = "",$resultValue = "",$details = "")
{
	if(!$result)
	{
		$result = Array();
	}
	
	$result["action"] = $action;
	$result["resultType"] = $resultType;
	$result["resultValue"] = $resultValue;
	$result["details"] = $details;
	
	// give answer to client
	echo json_encode($result);
}


/* this takes timestampt, md5-hashes it then cuts it down to 8 characters */
function salt()
{
	$salt = substr(md5(time()), 8); // date("F")
	return $salt;
}

/* send mail */
function sendMail($from,$to,$subjet,$text)
{
	// assemble header utf8
	$header = ('From: ' . $from . '\r\n');
	$header .= ('Reply-To: ' . $from . '\r\n');
	$header .= ('Bcc: '.$from.'\r\n');
	$header .= ('Return-Path: ' . $from . '\r\n');
	$header .= ('X-Mailer: PHP/' . phpversion() . '\r\n');
	$header .= ('X-Sender-IP: ' . $_SERVER['REMOTE_ADDR'] . '\r\n');
	$header .= ('Content-type: text/html\r\n');
	$header .= ("MIME-Version: 1.0\r\n");
	$header .= ("Content-Type: text/html; charset=utf-8\r\n");
	$header .= ("Content-Transfer-Encoding: 8bit\r\n\r\n");
	$valid_sender = '-f '.$settings_mail_activation;
		
	/* Verschicken der Mail */
	if(mail($to, $subject, $text, $header, $valid_sender))
	{
		// 	echo "Mail sent successfully!";
		exit ('type:success,id:registration successfull;sending activation mail successfull!,details:Thank you for registering :) You should receive an registration mail soon.');
		// sleep(3);
		// header("Location: servermessages/activation_send.php");
	}
	else
	{
		// echo"Mail not sent!";
		exit('type:success,id:registration successfull;sending activation mail failed!,details:Thank you for registering :) You should receive an registration mail soon.');
		// sleep(3);
		header("Location: servermessages/activation_mail_failed.php");
	}
}

/* generate a password and md5 hash it */
function generatePassword($length = 8) {
	
	$result = "";

    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $count = mb_strlen($chars);

    for ($i = 0, $result = ''; $i < $length; $i++) {
        $index = rand(0, $count - 1);
        $result .= mb_substr($chars, $index, 1);
    }
    
    return $result;
}

/* write the error to a log file */
function logError($error)
{
	file_put_contents($settings_errorLog, $error."\n", FILE_APPEND);
}

/* outputs a warning and if $settings_log_errors == true, outputs to error.log */
function error($message)
{
	trigger_error($message);

	global $settings_log_errors;
	global $worked;
	$worked = false;
	if(!empty($settings_log_errors)){
		log2file($settings_log_errors,$message);
	}
	
	return false;
}

/* outputs a warning and if $settings_log_errors == true, outputs to error.log */
function operation($operation)
{
	global $settings_log_operations;
	if(!empty($settings_log_operations)){
		log2file($settings_log_operations,$operation);
	}
}

/* write the error to a log file */
function log2file($file,$this)
{
	file_put_contents($file, time().": ".$this."\n", FILE_APPEND);
}