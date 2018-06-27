<?php
$text = '2你好吗';

echo strlen($text) . PHP_EOL;

echo mb_strlen($text) . PHP_EOL;

echo iconv('UTF-8', 'GBK', substr($text, 0, 6));