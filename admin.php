<?php
include "config.php";
date_default_timezone_set($setting_time_zone);

$password = $_POST['adminpass'];

If ($password != $setting_admin_pass) {
	die("<div class=post><h1>Wrong password. Go away.</h1></div>");
}

if (isset($_POST['action'])){
	$action = $_POST['action'];
	if (isset($_POST['text'])){
		$text = $_POST['text'];
	} else {
		die("<div class=post><h1>Text isn't set.</h1></div>");
	}
} else {
	$action = 'view';
	print "<div class=post><h1>Warning:</h1>No action was chosen previously, or you've only opened this page.</div>";
}

if ($action == 'ban' || $action == 'unban'){
if ($action == 'ban'){
	$blacklist_set = '1';
} else {
	$blacklist_set = '0';
}

$user_json = file_get_contents("users.json");
$user_list = json_decode($user_json, true);
$user_id_list = array_column($user_list, 'id');
$user_id = openssl_encrypt($text, $crypt_cipher, $setting_admin_key, $crypt_options, $crypt_iv); 

if (in_array($text, $user_id_list)){
	$user_place = array_search($user_id, $user_id_list); 
	$user_list[$user_place]['blacklist'] == 1;
	$new_user_json = json_encode($user_list, true);
	$users_file = fopen("users.json","w");
	fwrite($users_file, $new_user_json);
	fclose($users_file);
	die("<div class=post><h1>IP banned/h1>That user ID has been blacklisted from posting.</div>");
} else {
	die("<div class=post><h1>Uh oh</h1>That user ID isn't in the user record.</div>");
}
}

if ($action == 'delpost'){
	$board_json = file_get_contents("board.json");
	$board_list = json_decode($board_json, true);
	$board_id_list = array_column($board_list['posts'], 'id');
	$post_place = array_search($text, $board_id_list); 
	$board_list['posts'][$post_place]['text'] = $setting_deleted_msg;
	$new_board_json = json_encode($board_list, true);
	$board_file = fopen("board.json","w");
	fwrite($board_file, $new_board_json);
	fclose($board_file);
	die("<div class=post><h1>Deleted post #$text</h1>");
}

if ($action == 'reveal'){
	$decrypt_ip = openssl_decrypt($text, $crypt_cipher, $setting_admin_key, $crypt_options, $crypt_iv);
	die("<div class=post><h1>IP reveal</h1>User ID <b>$text</b> has IP <b>$decrypt_ip</b></div>");
}

print "<div class='post'><b>Ban IP</b><br>Reveal the poster's IP first, then ban it.<br><form action='admin.php' method='post'><br><input type='radio' name='action' value='ban'> Ban<br><input type='radio' name='action' value='unban'> Unban<br> Input User ID below</div>"; ##Ban user
print "<div class='post'><b>Delete post</b><br><input type='radio' name='action' value='delpost'><br> Input Post ID below</div>"; ##Delete posts
print "<div class='post'><b>Reveal IP:</b><br>Use <a href='board.json'>board.json</a> to find the offending post's user ID.<br><input type='radio' name='action' value='reveal'><br> Input User ID below</div>"; ##Reveal IP
print "<div class='post'><b>Input: </b><input type='text' name='text' maxlength='64'><br><b>Admin pass:</b><br><input required type='password' name='adminpass' maxlength='32'><br><input type='submit' value='Submit'></div>";

?>
</html>