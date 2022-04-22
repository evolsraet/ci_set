<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// SENDMAIL - mailpath 필요
$config['protocol'] = 'sendmail';
$config['mailpath'] = '/usr/sbin/sendmail';

// PHP MAIL
// $config['protocol'] = 'mail';

// SMTP
// $config['protocol'] = 'smtp';
// $config['smtp_crypto'] = 'ssl';
// $config['smtp_host'] = 'smtp.daum.net';
// $config['smtp_user'] = '';
// $config['smtp_pass'] = '';
// $config['smtp_port'] = '465';


// 메일 타입
$config['mailtype'] = 'html';

?>