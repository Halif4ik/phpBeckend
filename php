<?php
function readHttpLikeInput() {
    // ну это уже написано за вас
}

$contents = readHttpLikeInput();



function parseTcpStringAsHttpRequest($string) {
    // ну это вы уже написали
}

$http = parseTcpStringAsHttpRequest($contents);
processHttpRequest($http["method"], $http["uri"], $http["headers"], $http["body"]);

