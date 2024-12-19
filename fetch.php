<?php

error_reporting(E_ERROR);

$stationLists = [
    ['上海南', 'SNH'],
    ['莘庄', 'XZH'],
    ['春申', 'CWH'],
    ['新桥', 'XQH'],
    ['车墩', 'MIH'],
    ['叶榭', 'YOH'],
    ['亭林', 'TVH'],
    ['金山园区', 'REH'],
    ['金山卫', 'BGH'],
];
$dates = ['20250106', '20250105']; // [工作日, 周末]

$api = 'https://wifi.12306.cn/wifiapps/ticket/api/stoptime/queryByStationCodeAndDate';
$data = [[], []];

foreach($stationLists as [$station, $code]) {
    $stationData = json_decode(file_get_contents("{$api}?stationCode={$code}&trainDate={$dates[0]}"), true);
    foreach($stationData['data'] as $train) {
        if(!preg_match('/^S(\d+)$/', $train['trainCode'], $matches)) {
            continue;
        }
        $type = intval($matches[1]) % 2;
        if(!$data[$type][$train['trainCode']]) {
            $data[$type][$train['trainCode']] = ['weekday' => 1, 'trainCode' => $train['trainCode']];
        }
        $data[$type][$train['trainCode']][$station] = [
            $train['arriveTime'] ? substr($train['arriveTime'], 0, 2).':'.substr($train['arriveTime'], 2, 2) : null,
            $train['departTime'] ? substr($train['departTime'], 0, 2).':'.substr($train['departTime'], 2, 2) : null,
        ];
    }
    $stationData = json_decode(file_get_contents("{$api}?stationCode={$code}&trainDate={$dates[1]}"), true);
    foreach($stationData['data'] as $train) {
        if(!preg_match('/^S(\d+)$/', $train['trainCode'], $matches)) {
            continue;
        }
        $type = intval($matches[1]) % 2;
        $data[$type][$train['trainCode']]['weekday'] = 0;
    }
}

usort($data[0], function ($a, $b) {
    return ($a['金山卫'][1] ?? '07:20') <=> ($b['金山卫'][1] ?? '07:20');
});
usort($data[1], function ($a, $b) {
    return ($a['金山卫'][1] ?? '07:30') <=> ($b['金山卫'][1] ?? '07:30');
});
file_put_contents('jsr.json', json_encode($data, JSON_UNESCAPED_UNICODE));
