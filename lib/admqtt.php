<?php

require '../vendor/autoload.php';

function queueCreateUpdateOperation(
    array &$queue,
    string $logonName,
    string $firstName,
    string $lastName,
    bool $wifiAccess,
    string $group = ""
) {
    $arr = array(
        "Action" => "CreateUpdate",
        "LogonName" => $logonName,
        "FirstName" => $firstName,
        "LastName" => $lastName,
        "WifiAccess" => $wifiAccess,
        "Group" => $group
    );
    array_push($queue, $arr);
}

function queueUpdatePasswordOperation(
    array &$queue,
    string $logonName,
    string $password
) {
    $arr = array(
        "Action" => "UpdatePassword",
        "LogonName" => $logonName,
        "Password" => $password
    );
    array_push($queue, $arr);
}

function queueDeleteOperation(
    array &$queue,
    string $logonName
) {
    $arr = array(
        "Action" => "Delete",
        "LogonName" => $logonName
    );
    array_push($queue, $arr);
}

function sendQueueToBroker(
    array &$queue,
    string $server,
    int $port,
    string $username, 
    string $password,
    string $topic
) {
    $broker = new \PhpMqtt\Client\MqttClient($server, $port, "lampschool");

    $settings = (new \PhpMqtt\Client\ConnectionSettings)
        ->setUsername($username)
        ->setPassword($password);

    $broker->connect($settings);

    $broker->publish(
        $topic, json_encode($queue), 0);

    $broker->disconnect();
}