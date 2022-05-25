# smolBBS
Lightweight single-threaded textboard made in PHP 7.4. **No SQL!**
Board posts saved to JSON, board dynamically loads in HTML/CSS from JSON data.

## Installation 

1. Put contents into a directory of a web server that is running PHP. 
2. Edit config.php, change $setting_admin_pass, $setting_admin_key and $crypt_iv.
3. Set permission to users.json and board.json to 777. 

You should now have a fully functioning single-threaded textboard.

## Why?
Before I made my own textboard software, I was looking for a simple PHP textboard that didn't require an SQL database for my very simple web server. As it turns out, none existed or they were from 20 years ago and could not work with modern PHP. So I made this software which stores posts and user data in JSON format. 

If you want a textboard that can handle a lot of traffic, smolBBS is not for you. Every time the site loads, the JSON is parsed. Very inefficient for large websites. I made this with very small websites in mind. smolBBS sees use in very small websites and works very well in this context.
