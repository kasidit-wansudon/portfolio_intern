<?php
// 192.168.1.106
$server_ip = gethostbyname(gethostname());
$pwd = realpath(__FILE__);
$cut_path = strpos($pwd, 'htdocs\\');
if ($cut_path !== false) {
    $pwd = substr($pwd, $cut_path);
}
// 192.168.1.106\GitHub\portfolio_intern
echo "$server_ip/$pwd";