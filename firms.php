<?php

$arFirms = [
    [
        'firm_id' => 'mysql:dbname=firm;host=firm1',
        'subject' => 's1',
        'body'    => 'body1',
        'from'    => 'from1',
        'to'      => 'to1'
    ],
    [
        'firm_id' => 'mysql:dbname=firm;host=firm2',
        'subject' => 's2',
        'body'    => 'body2',
        'from'    => 'from2',
        'to'      => 'to2'
    ],
];

foreach ($arFirms as $arFirm) {
    $arConnects[$arFirm['firm_id']] = [
        'subject' => $arFirm['subject'],
        'body'    => $arFirm['body'],
        'from'    => $arFirm['from'],
        'to'      => $arFirm['to'],
    ];
}

function execute($array)
{
    foreach ($array as $firmId => $item) {
        $conn = new PDO($firmId, 'username', 'password');
        $sql = "INSERT INTO firm (subject, body, from, to) VALUES (:subject, :body, :from, :to)";
        $conn->prepare($sql)->execute($item);
    }
}