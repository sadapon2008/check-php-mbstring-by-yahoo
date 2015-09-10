<?php

if(count($argv) < 2) {
    exit(1);
}

$json = file_get_contents($argv[1]);
if($json === false) {
    exit(1);
}

$data = json_decode($json, true);
if($data === false) {
    exit(1);
}

// Yahooの校正支援を利用
$api = 'http://jlp.yahooapis.jp/KouseiService/V1/kousei';
// 環境変数からアプリケーションIDを取得
$appid = getenv('YAHOO_APPID');
$ch = curl_init($api);

$final_result = array();

foreach($data as $val) {
    if(!isset($val['value']) || !is_string($val['value']) || (strlen($val['value']) == 0)) {
        continue;
    }
    if(strlen($val['value']) == mb_strlen($val['value'])) {
        continue;
    }
    $params = array(
        'sentence' => $val['value'],
    );
    curl_setopt_array($ch, array(
        CURLOPT_POST           => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_USERAGENT      => "Yahoo AppID: $appid",
        CURLOPT_POSTFIELDS     => http_build_query($params),
    ));
    $result = curl_exec($ch);
    $xml = new SimpleXMLElement($result);
    if(!isset($xml->Result)) {
        continue;
    }
    $all = $xml->Result;
    if($all instanceof SimpleXMLElement) {
        $all = array($all);
    } elseif(!is_array($all)) {
        continue;
    }
    foreach($all as $detail) {
        if(!isset($detail->Surface) || !isset($detail->ShitekiInfo)) {
            continue;
        }
        $final_result[] = array(
            'filename' => $val['filename'],
            'line' => $val['line'],
            'surface' => $detail->Surface,
            'shiteki_word' => $detail->ShitekiWord,
            'shiteki_info' => $detail->ShitekiInfo,
        );
    }
}

curl_close($ch);
echo json_encode($final_result);
