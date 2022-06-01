<?php
include "config.php";
date_default_timezone_set($setting_time_zone);

if (isset($_POST['adminpass'])){
	$password = $_POST['adminpass'];
} else {
	die("<div class=post><h1>No password was set.</h1></div>");
}

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

# Toggle ban for IP
if ($action == 'ban' || $action == 'unban'){
	if ($action == 'ban'){
		$blacklist_set = '1';
		$blacklist_word = "banned";
	} else {
		$blacklist_set = '0';
		$blacklist_word = "unbanned";
	}

	$user_json = file_get_contents("users.json");
	$user_list = json_decode($user_json, true);
	$user_id_list = array_column($user_list['users'], 'id');
	$user_id = openssl_encrypt($text, $crypt_cipher, $setting_admin_key, $crypt_options, $crypt_iv); 

	if (in_array($user_id, $user_id_list)){
		# Ban user that already exists in user data
		$user_place = array_search($user_id, $user_id_list); 
		$user_list['users'][$user_place]['blacklist'] = "$blacklist_set";
		$new_user_json = json_encode($user_list, true);
	} else {
		# Add IP to user data for future blacklisting
		$new_user = array("id" => $user_id, "lastpost" => time(), "blacklist" => "$blacklist_set");
		array_push($user_list['users'], $new_user);
		$new_user_json = json_encode($user_list, true);
	}
	# Write the ban changes out
	file_put_contents("users.json", $new_user_json);
	die("<div class=post><h1>IP banned</h1>The IP has been $blacklist_word.</div>");
}

#Delete post
if ($action == 'delpost'){
	$board_json = file_get_contents("board.json");
	$board_list = json_decode($board_json, true);
	$board_id_list = array_column($board_list['posts'], 'id');
	$post_place = array_search($text, $board_id_list); 
	$board_list['posts'][$post_place]['text'] = $setting_deleted_msg;
	$new_board_json = json_encode($board_list, true);
	file_put_contents("board.json", $new_board_json);
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
