<?php
include "config.php";
date_default_timezone_set($setting_time_zone);

if (($setting_admin_pass == "CHANGEME") || ($setting_admin_key == "CHANGEME") || ($crypt_iv == "CHANGEME")){
	die("<div class=post><h1>A FATAL MISTAKE HAS BEEN MADE!</h1>You need to go inside <b>config.php</b> and change one or more of the following settings to something valid. They currently are set to <b>CHANGEME</b>.<br><ul><li>setting_admin_pass</li><li>setting_admin_key</li><li>crypt_iv</li></ul></div>");
}

echo "<form action='admin.php' method='post'>
<input required type='password' name='adminpass' maxlength='32'><input type='submit' value='Admin'> 
</form>";

if (isset($_POST['mode'])){
	$mode = $_POST['mode'];
} else {
	$mode = 'view';
}

## Robot test
$math1 = rand(1,99);
$math2 = rand(1,25);
$mtype = rand(0,1);

if ($mtype == 0) {
	$mtype = '+';
} elseif ($mtype == 1) {
	$mtype = '-';
}	

# Posting mode

if ($mode == 'post'){
	$text = $_POST['text'];
	$name = $_POST['name'];
	$text = strip_tags($text);

	# Check if banned
	$your_ip = $_SERVER['REMOTE_ADDR'];

	$user_json = file_get_contents("users.json");
	$user_list = json_decode($user_json, true);
	$user_id_list = array_column($user_list['users'], 'id');
	$user_id = openssl_encrypt($your_ip, $crypt_cipher, $setting_admin_key, $crypt_options, $crypt_iv); 

	# Blacklist
	if (in_array($user_id, $user_id_list)){
		$user_place = array_search($user_id, $user_id_list); 
		if ($user_list['users'][$user_place]['blacklist'] == "1"){
			die("<div class='post'><h1>You're banned!</h1>You're not allowed to create new posts!</div>");
		}
	}

	## SPAM BLOCKERS
	if (preg_match('/^(.)\1*$/u ', $text)) {
		die ("<div class='post'><h1>Spam detected!</h1>Try posting something that's not spam.</div>");
	}

	$post_length = strlen($text);
	if ($post_length < $setting_minchar) {
		die ("<div class='post'><h1>Post too short!</h1>You wrote <b>$post_length characters</b>. The minimum is <b>$setting_minchar characters</b>.</div>");
	}

	if ($post_length > $setting_maxchar) {
		die ("<div class='post'><h1>Post too long!</h1>You wrote <b>$post_length characters</b>. The minimum is <b>$setting_maxchar characters</b>.</div>");
	}

	if (substr_count($text, ' ') === strlen($text)) {
		die ("<div class='post'><h1>Spam detected!</h1>Post contained only spaces!</div>");
	}	

	# Spam timer
	$spam_time = time() + $setting_post_wait;
	if (in_array($user_id, $user_id_list)){
		$user_place = array_search($user_id, $user_id_list); 
		$user_wait = $user_list['users'][$user_place]['lastpost'] - time();
		if ($user_list['users'][$user_place]['lastpost'] > time()){
			die("<div class='post'><h1>Spam timer</h1>Wait $user_wait seconds before posting!</div>");
		}
	}

	## See if user is verified
	if ($setting_usercodes == 1){
		if (isset($_POST['trip']) && $_POST['trip'] == 1){
			$name_verify = substr(sha1($user_id), 0, 8);
		}
	}

	if (!isset($name_verify)){
		$name_verify = 0;
	}
	## Check if math is correct

	$math1 = $_POST['math1'];
	$math2 = $_POST['math2'];
	$mtype = $_POST['mtype'];
	$manswer = $_POST['manswer'];

	if ($mtype == '+') {
		$answer = $math1 + $math2;
	} elseif ($mtype == '-') {
		$answer = $math1 - $math2;
	}

	if ($manswer <> $answer) {
		$spam_time = time() + $setting_wrong_penalty;
	}

	## Update the user's next permitted posting time
	if (in_array($user_id, $user_id_list)){
		$user_list['users'][$user_place]['lastpost'] = $spam_time; 
		$new_user_json = json_encode($user_list, true);
		file_put_contents("users.json", $new_user_json);
	} else {
		$new_user = array("id" => "$user_id", "lastpost" => $spam_time, "blacklist" => 0);
		array_push($user_list['users'], $new_user);
		$new_user_json = json_encode($user_list, true);
		file_put_contents("users.json", $new_user_json);
	}

	if ($manswer <> $answer) {
		die("<div class='post'><h1>Spam timer</h1><div class='post'>Your answer to the math question was incorrect!<br>Because of this... you must wait $setting_wrong_penalty seconds.</h1></div>");
	}
	
	if(isset($_POST['name']) && isset($_POST['text'])) {
		$board_json = file_get_contents("board.json");
		$board_list = json_decode($board_json, true);
		$board_list['id'] = $board_list['id'] + 1;
		$new_post = array("id" => $board_list['id'], "user" => "$user_id", "name" => "$name", "verified" => "$name_verify", "time" => time(), "text" => "$text");
		array_push($board_list['posts'], $new_post);
		$new_board_json = json_encode($board_list, true);
		file_put_contents("board.json", $new_board_json);
		echo "<div class='post'><h1>Post completed!</h1><a href='index.php'>Back</a></div>";
	}
}

If ($mode == 'view'){
	
## DISPLAY POSTS MODE

	print "<title>$setting_board_name</title><center><div class=main>";
	$mode = 0;
	$your_ip = $_SERVER['REMOTE_ADDR'];

	print "$setting_board_title";

	print "<center><h2>Compose a post</h2> <form action='index.php' method='post'>
	<table class='newpost'>
	<tr><td>Name</td><td><input required type='text' name='name' maxlength='64' value='Anonymous'><input type='submit' value='Submit'><br><input type='checkbox' name='trip' value='1'>Show tripcode?</td></tr>
	<tr><td>Text</td><td><textarea rows='4' cols='16' name='text' maxlength='$setting_maxchar' required placeholder='Type your text here...'></textarea></td></tr>";
	print "<tr><td>Math</td><td><input required type='hidden' name='math1' maxlength='32' value='$math1'><input required type='hidden' name='math2' maxlength='32' value='$math2'><input required type='hidden' name='mtype' maxlength='32' value='$mtype'><input required type='hidden' name='mode' maxlength='4' value='post'>";

	print "$math1 $mtype $math2" . ' <input type="text" required name="manswer" maxlength=32 placeholder="Answer"></td></form></table></center><hr>' . "\n \n";

	$board_json = file_get_contents("board.json");
	$board_posts = json_decode($board_json, true);
	if ($setting_board_flow == '1'){
		$board_posts['posts'] = array_reverse($board_posts['posts']);
	}
	$total_posts = count($board_posts['posts']);

	# Post amount limiter
	$post_limit = 10;

	if(isset($_GET['m'])) {
		$post_limit = $_GET['m'];
		if ($post_limit > 500) {
			die("<div class='post'><h1>Not cool!</h1><div class='post'>You aren't able to see more than 500 posts at once!</div>");
		}
	}

	## PRINT EACH POST

	$display_posts = array_slice($board_posts['posts'], 0, $post_limit);	

	foreach ($display_posts as $display_post){
		$post_date = date("d M y - H:i:s T",$display_post['time']);
		$post_id = $display_post['id'];
		$post_name = $display_post['name'];
		$post_text = $display_post['text'];
		$post_verify = $display_post['verified'];
		if($post_verify == "0"){
			$name_splash = '';
		} else {
			$verify_bg_color = substr($post_verify, 0, 6);
			$name_splash = "<span class=poster style='background-color:#$verify_bg_color;'>$post_verify</span>";
		}
		echo "<div class='post'><span class=info>#$post_id <b>$post_name</b> $name_splash : <i>$post_date</i></span><br>$post_text</div> \n \n";
	}

	print "<div class=$setting_static_div>$total_posts posts total<br><b>Amount of posts to display</b><br><a href='index.php'>10</a> - <a href='index.php?m=25'>25</a> - <a href='index.php?m=50'>50</a> - <a href='index.php?m=100'>100</a> - <a href='index.php?m=250'>250</a> - <a href='index.php?m=500'>500</a></div>";
	print "</div><footer>$setting_footer_content";
}

?>
</footer>
</html>
