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
    define("CONTENT", $content);
    define("SPACE", " ");
    define("METHOD", "method");
    define("URI", "uri");
    define("HEADERS", "headers");
    define("BODY", "body");
    $HOST = "Host";
    $ACCEPT = "Accept";
    $ACCEPT_E = "Accept-Encoding";
    $ACCEPT_L = "Accept-Language";
    $USER_A = "User-Agent";
    $CONTENT_L = "Content-Length";
    $BUKID = "bookId";

    // todo magic and get half result
    $result = [METHOD => "",
        URI => "",
        HEADERS => [],
        BODY => ""
    ];
    /**
     * find start ant stop index, first char  content cell host present in position firstindexOf Host +lenth host+ lenth ": "
     */
    function substrFromCurHederToNextHeder($nameCurHeader, $nameNextHeder)
    {
        $indOfStart = strpos(CONTENT, $nameCurHeader) + strlen($nameCurHeader) + 2;
        $lenSubStr = strpos(CONTENT, $nameNextHeder) - 1 - $indOfStart;
        return substr(CONTENT, $indOfStart, $lenSubStr);
    }

    /*all content cuted on pices */
    $arrContent = explode(" ", CONTENT);
    $result[METHOD] = $arrContent[0];
    $result[URI] = $arrContent[1];

    //    cut all lost part in [][]
    $result[HEADERS][0] = [$HOST, substrFromCurHederToNextHeder($HOST, $ACCEPT)];
    $result[HEADERS][1] = [$ACCEPT, substrFromCurHederToNextHeder($ACCEPT, $ACCEPT_L)];
    $result[HEADERS][2] = [$ACCEPT_L, substrFromCurHederToNextHeder($ACCEPT_L, $ACCEPT_E)];
    $result[HEADERS][3] = [$ACCEPT_E, substrFromCurHederToNextHeder($ACCEPT_E, $USER_A)];
    $result[HEADERS][4] = [$USER_A, substrFromCurHederToNextHeder($USER_A, $CONTENT_L)];

    /**
     * find start index CONTENT_Lenght ant stop index, first char  content cell host present in position firstindexOf Host +lenth host+ lenth ": "
     */
    $indOfStart = strpos(CONTENT, $CONTENT_L) + strlen($CONTENT_L) + 2;
    $indxEntrLast = strpos(CONTENT, "\n", $indOfStart);
    $lenSubStr = $indxEntrLast - $indOfStart;
    $result[HEADERS][5] = [$CONTENT_L, substr(CONTENT, $indOfStart, $lenSubStr)];


    $result[BODY] = $result[BODY] = substr(CONTENT, strpos(CONTENT, $BUKID),
        strpos(CONTENT, "Teck") + strlen("Teck") - strpos(CONTENT, $BUKID));;

    return $result;
}

$http = parseTcpStringAsHttpRequest($contents);

echo(json_encode($http, JSON_PRETTY_PRINT));

