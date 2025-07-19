<?php
if (headers_sent($file, $line)) {
    echo "Headers already sent in $file on line $line";
    exit;
} 