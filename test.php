<?php
$nick = 'hi中国';
echo PHP_EOL . substr($nick, 0, 3);
echo PHP_EOL . mb_substr($nick, 0, 3, 'utf8');
echo PHP_EOL . mb_substr($nick, 0, 3);
echo PHP_EOL . mb_substr($nick, 0, 3, 'gbk');