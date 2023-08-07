<?php // не обращайте на эту функцию внимания
// она нужна для того чтобы правильно считать входные данные

function readHttpLikeInput()
{
    $f = fopen('php://stdin', 'r');
    $store = "";
    $toread = 0;
    while ($line = fgets($f)) {
        $store .= preg_replace("/\r/", "", $line);
        if (preg_match('/Content-Length: (\d+)/', $line, $m))
            $toread = $m[1] * 1;
        if ($line == "\r\n")
            break;
    }
    if ($toread > 0)
        $store .= fread($f, $toread);
    return $store;

}

$contents = readHttpLikeInput();

function parseTcpStringAsHttpRequest($content)
{
    define("SPACE", " ");
    $findHtml = '.html';
    $HOST = "Host";
    $ACCEPT = "Accept:";
    $ACCEPT_L = "Accept-Language:";
    $ACCEPT_L = "Accept-Encoding:";
    $USER = "User-Agent:";
    $CONTENT_L = "Content-Length:";

    $fIndOfStart = strpos($content, SPACE);
    //    cut part as GET
    $method = substr($content, 0, $fIndOfStart);
    $fiofEnd = strpos($content, $findHtml);
    //    cut part as URI
    $uri = substr($content, $fIndOfStart + 1, $fiofEnd + 1);

    $fIndOfStart = strpos($content, $HOST) + strlen($HOST) + 2;
    $fiofEnd = strpos($content, $ACCEPT) - 1 - $fIndOfStart;


    $host = substr($content, $fIndOfStart, $fiofEnd);


    echo "Строка '$host' найдена в строке";
    echo "V позиции " . $fIndOfStart . " /\n";

    $result = ["method" => $method,
        "uri" => $uri,
        $HOST => $host,
    ];

    var_dump( $result);

    return array(
        // 01234567891
        /* GET /doc/t.html HTTP/1.1
Host: www.t.ua
Accept: image/gif, image/jpeg,
//Accept-Language: en-us
//Accept-Encoding: gzip, deflate
//User-Agent: Mozilla/4.0
//Content-Length: 35

bookId=12345&author=Tan+Ah+Teck */

    );
}

$http = parseTcpStringAsHttpRequest($contents);

//echo(json_encode($http, JSON_PRETTY_PRINT));
