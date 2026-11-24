<?php
require_once '../inc/db.php';
session_set_cookie_params(['httponly'=>true,'samesite'=>'Lax','secure'=>!empty($_SERVER['HTTPS'])&&$_SERVER['HTTPS']!=='off']);session_start();
$home='../index.php';$fail=static function()use($home){header('Location: '.$home.'?signup_error=1#newsletter');exit;};
if($_SERVER['REQUEST_METHOD']!=='POST'){$fail();}
if(!empty($_POST['website'])){header('Location: '.$home.'?joined=1#newsletter');exit;}
if(!hash_equals($_SESSION['public_csrf']??'',$_POST['csrf']??'')||empty($_POST['consent']))$fail();
if(!rate_allowed('newsletter-signup:'.($_SERVER['REMOTE_ADDR']??'unknown'),5,3600,3600))$fail();
$email=strtolower(trim((string)($_POST['email']??'')));if(!filter_var($email,FILTER_VALIDATE_EMAIL)||!filter_var(ADMIN_RESET_FROM,FILTER_VALIDATE_EMAIL))$fail();
$db=db();$s=$db->prepare('SELECT id,confirmed_at,unsubscribed_at FROM subscribers WHERE email=? LIMIT 1');$s->execute([$email]);$old=$s->fetch();
if($old&&$old['confirmed_at']&&!$old['unsubscribed_at']){header('Location: '.$home.'?joined=1#newsletter');exit;}
$confirm=bin2hex(random_bytes(32));$unsubscribe=bin2hex(random_bytes(32));
$db->prepare('INSERT INTO subscribers(email,consent_at,confirmation_token,unsubscribe_token,unsubscribed_at,confirmed_at) VALUES(?,NOW(),?,?,NULL,NULL) ON DUPLICATE KEY UPDATE consent_at=NOW(),confirmation_token=VALUES(confirmation_token),unsubscribe_token=VALUES(unsubscribe_token),unsubscribed_at=NULL,confirmed_at=NULL')->execute([$email,hash('sha256',$confirm),hash('sha256',$unsubscribe)]);
$base=site_base();$confirmUrl=$base.'subscribe-confirm.php?token='.rawurlencode($confirm);$unsubscribeUrl=$base.'unsubscribe.php?token='.rawurlencode($unsubscribe);
$subject='Confirm Skye African Supermarket emails';$body="Please confirm that you agree to receive store emails by opening this link:\n$confirmUrl\n\nYou can unsubscribe at any time here:\n$unsubscribeUrl\n\nIf you did not request this, ignore this email.";$headers='From: Skye African Supermarket <'.ADMIN_RESET_FROM.">\r\nContent-Type: text/plain; charset=UTF-8\r\n";
if(!@mail($email,$subject,$body,$headers,'-f'.ADMIN_RESET_FROM))$fail();
header('Location: '.$home.'?joined=1#newsletter');
