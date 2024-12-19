<html>

<head>
    <title>金山铁路</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
</head>
<style>
    body {
        font-family: 'SimSun';
        font-size: 18px;
        -webkit-text-stroke-width: 0.2px;
    }

    .weekdayOnlyTrains {
        background-color: #ffff00;
    }

    .normalTrains {
        background-color: #99cdf9;
    }

    .trainCode {
        color: #041f9b;
        height: 38px;
    }

    .terminus {
        color: #b9273a;
    }

    .notice {
        height: 32px;
    }

    table {
        border: 5px double black;
        border-collapse: collapse;
        margin: 12px;
        font-weight: bold;
        display: inline-block;
    }

    .smallStationName {
        font-size: 14px;
    }

    .trainCode,
    .arriveTime,
    .departTime,
    .stationName {
        width: 64px;
        padding: 0px;
        text-align: center;
        border-left: 2px solid black;
        border-right: 2px solid black;
    }

    .trainCode,
    .arriveTime {
        border-top: 2px solid black;
        border-bottom: none;
    }

    .departTime {
        border-top: none;
        border-bottom: none;
    }

    .notice {
        border-top: none;
        border-bottom: 5px double black;
    }

    table tr:last-child {
        border-bottom: none;
    }

    tr td:first-child,
    tr th:first-child {
        border-left: none;
    }

    tr td:last-child,
    tr th:last-child {
        border-right: none;
    }
</style>

<body>

    <?php

    error_reporting(0);
    $data = json_decode(file_get_contents('jsr.json'), true);

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

    foreach([1, 0] as $direction) {
        $cols = count($stationLists) + 1;
        echo "<table><tr class=\"weekdayOnlyTrains notice\"><th colspan=\"{$cols}\">";
        echo implode('、', array_map(fn($train) => $train['trainCode'], array_filter($data[$direction], fn($train) => $train['weekday'])));
        echo '逢周一至周五开行</th></tr>';
        echo '<tr><th class="normalTrains trainCode">车次</th>';
        foreach($stationLists as $station) {
            $class = 'stationName';
            if(mb_strlen($station[0]) >= 4) {
                $class .= ' smallStationName';
            }
            if($station[0] == $stationLists[0][0] || $station[0] == $stationLists[count($stationLists) - 1][0]) {
                $class .= ' terminus"';
            }
            echo "<th class=\"{$class}\">{$station[0]}</th>";
        }
        echo '</tr>';

        foreach($data[$direction] as $train) {
            echo '<tr>';
            $class = $train['weekday'] ? 'weekdayOnlyTrains' : 'normalTrains';
            echo "<td class=\"{$class} trainCode\" rowspan=\"2\">{$train['trainCode']}</td>";
            foreach($stationLists as $station) {
                if($train[$station[0]][0] && $train[$station[0]][0] != $train[$station[0]][1]) {
                    echo "<td class=\"arriveTime\">{$train[$station[0]][1]}</td>";
                } else {
                    echo "<td rowspan=\"2\" class=\"terminus arriveTime\">{$train[$station[0]][1]}</td>";
                }
            }
            echo '</tr><tr>';
            foreach($stationLists as $station) {
                if($train[$station[0]][0] && $train[$station[0]][0] != $train[$station[0]][1]) {
                    echo "<td class=\"departTime\">{$train[$station[0]][1]}</td>";
                }
            }
            echo '</tr>';
        }
        echo '</table>';

        $stationLists = array_reverse($stationLists);
    }

    ?>

</body>

</html>