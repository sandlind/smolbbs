<html>
<head>
<style>

body { background-image: url('bg.gif'); font-family: sans-serif; color: #000;}
h1 { font-family: serif; color: #DD0000; font-size: 16pt;}
a { color: #0000FF; text-decoration-line: underline;}
a:hover { color: #FF00FF; text-decoration: bold; }
div.main { width: 640px; padding: 0 10px 10px; text-align: left;}
div.post { display: table; padding: 5px; margin: 15px; background:#EEEEFF; border: 4px #000 double;}
div.top { display: table; padding: 5px; margin: 15px; background:#CCFFCC; border: 4px #000 double;}
.newpost table, th, td { background: #CCFFCC; border: 1px #000 solid; padding: 4px; text-align: left; font-weight: bold;}
.deleted { text-align: left; color: #800000; font-size: 12pt; font-weight: bold;}
.info { color: #DD0000; font-size: 12pt; }
.poster { padding: 0 5px; border-radius: 3px; text-shadow: 2px 2px #000000; color:#FFFFFF}

</style>
</head>
<?php
$setting_board_name = 'smolBBS';			# Name of this textboard
$setting_board_title = "<div class=top><center><h1>smolBBS</h1></center>This is an installation of smolBBS. Please personalize your <b>config.php</b>.</div>";	# In-line HTML that shows on top of the board
$setting_admin_pass = 'CHANGEME';	 		# Admin pass (CHANGE THIS)
$setting_admin_key = 'CHANGEME';			# Encryption key for sensitive data (CHANGE THIS)
$setting_time_zone = 'America/New_York';	# Time zone
$setting_board_flow = '1';					# 0 = Oldest posts on top, read top to bottom - 1 = Newest posts on top, read bottom to top
$setting_usercodes = '1'; 					# Allow users to register their names per IP address
$setting_maxchar = '2000';					# Maximum post length permitted
$setting_minchar = '16';					# Minimum post length permitted
$setting_post_wait = '120';					# Seconds a poster must wait before posting again
$setting_wrong_penalty = '600';				# Seconds a poster must wait after getting math wrong
$setting_deleted_msg = "<span class=deleted>THIS POST WAS DELETED.</span>";
$setting_static_div = "top"; 				# Name of the CSS div for static content (never moves or changes)
$setting_footer_content = "</div><a href='https://github.com/sandlind/smolbbs'>smolbbs</a> 2022";

# Crypt stuff
# Want to modify this? Read more about this stuff here:
# https://www.php.net/manual/en/function.openssl-encrypt.php
$crypt_cipher = "AES-128-CTR"; 
$crypt_iv_length = openssl_cipher_iv_length($crypt_cipher); 
$crypt_options = 0;  
$crypt_iv = 'CHANGEME'; ## CHANGE THIS!!! (Should be exactly 16 bytes long, may need to be in hexadecimal (contains only 0123456789abcdef) )

?>
