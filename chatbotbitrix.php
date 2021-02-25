<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
$_SERVER["DOCUMENT_ROOT"] = "/home/bitrix/www";
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
define('DEBUG_FILE_NAME', 'logbotchat.log');
define('CLIENT_ID', '');
define('CLIENT_SECRET', '');


if ($_REQUEST['data']['PARAMS']['FROM_USER_ID'] == 1  || $_REQUEST['data']['PARAMS']['FROM_USER_ID'] == 17 || $_REQUEST['data']['PARAMS']['FROM_USER_ID'] == 9) {
    if ($_REQUEST['event'] == 'ONIMBOTMESSAGEADD') {

        $result = restCommand('imbot.message.add', array(


            'DIALOG_ID' => 'chat1', // Идентификатор диалога, это либо USER_ID пользователя, либо chatXX - где XX идентификатор чата, передается в событии ONIMBOTMESSAGEADD и ONIMJOINCHAT
            'MESSAGE' => $_REQUEST['data']['PARAMS']['MESSAGE'], // Текст сообщения
            'ATTACH' => '', // Вложение, необязательное поле
            'KEYBOARD' => '', // Клавиатура, необязательное поле
            'MENU' => '', // Контекстное меню, необязательное поле
            'SYSTEM' => 'Н', // Отображать сообщения в виде системного сообщения, необязательное поле, по умолчанию 'N'
            'URL_PREVIEW' => 'Y' // Преобразовывать ссылки в rich-ссылки, необязательное поле, по умолчанию 'Y'

        ), $_REQUEST["auth"]);

    }
}else {
    $result = restCommand('imbot.message.add', array(


        'DIALOG_ID' =>  $_REQUEST['data']['PARAMS']['FROM_USER_ID'],
        'MESSAGE' => 'КЕК)0))0))',
        'ATTACH' => '',
        'KEYBOARD' => '',
        'MENU' => '',
        'SYSTEM' => 'Н',
        'URL_PREVIEW' => 'Y'

    ), $_REQUEST["auth"]);

}

/**
 * Save application configuration.
 * WARNING: this method is only created for demonstration, never store config like this
 *
 * @param $params
 * @return bool
 */
function saveParams($params)
{
    $config = "<?php\n";
    $config .= "\$appsConfig = ".var_export($params, true).";\n";
    $config .= "?>";

    file_put_contents(__DIR__."/config.php", $config);

    return true;
}

/**
 * Send rest query to Bitrix24.
 *
 * @param $method - Rest method, ex: methods
 * @param array $params - Method params, ex: Array()
 * @param array $auth - Authorize data, received from event
 * @param boolean $authRefresh - If authorize is expired, refresh token
 * @return mixed
 */
function restCommand($method, array $params = Array(), array $auth = Array(), $authRefresh = true)
{
    $queryUrl = $auth["client_endpoint"].$method;
    $queryData = http_build_query(array_merge($params, array("auth" => $auth["access_token"])));

    writeToLog(Array('URL' => $queryUrl, 'PARAMS' => array_merge($params, array("auth" => $auth["access_token"]))), 'ImBot send data');

    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_POST => 1,
        CURLOPT_HEADER => 0,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_SSL_VERIFYPEER => 1,
        CURLOPT_URL => $queryUrl,
        CURLOPT_POSTFIELDS => $queryData,
    ));

    $result = curl_exec($curl);
    curl_close($curl);

    $result = json_decode($result, 1);

    /* if ($authRefresh && isset($result['error']) && in_array($result['error'], array('expired_token', 'invalid_token')))
     {
         $auth = restAuth($auth);
         if ($auth)
         {
             $result = restCommand($method, $params, $auth, false);
         }
     } */

    return $result;
}

/**
 * Get new authorize data if you authorize is expire.
 *
 * @param array $auth - Authorize data, received from event
 * @return bool|mixed
 */
function restAuth($auth)
{
    if (!CLIENT_ID || !CLIENT_SECRET)
        return false;

    if(!isset($auth['refresh_token']))
        return false;

    $queryUrl = 'https://oauth.bitrix.info/oauth/token/';
    $queryData = http_build_query($queryParams = array(
        'grant_type' => 'refresh_token',
        'client_id' => CLIENT_ID,
        'client_secret' => CLIENT_SECRET,
        'refresh_token' => $auth['refresh_token'],
    ));

    writeToLog(Array('URL' => $queryUrl, 'PARAMS' => $queryParams), 'ImBot request auth data');

    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_HEADER => 0,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => $queryUrl.'?'.$queryData,
    ));

    $result = curl_exec($curl);
    curl_close($curl);

    $result = json_decode($result, 1);
    if (!isset($result['error']))
    {
        $appsConfig = Array();
        if (file_exists(__DIR__.'/config.php'))
            include(__DIR__.'/config.php');

        $result['application_token'] = $auth['application_token'];
        $appsConfig[$auth['application_token']]['AUTH'] = $result;
        saveParams($appsConfig);
    }
    else
    {
        $result = false;
    }

    return $result;
}

/**
 * Write data to log file. (by default disabled)
 * WARNING: this method is only created for demonstration, never store log file in public folder
 *
 * @param mixed $data
 * @param string $title
 * @return bool
 */
function writeToLog($data, $title = '')
{
    if (!DEBUG_FILE_NAME)
        return false;

    $log = "\n------------------------\n";
    $log .= date("Y.m.d G:i:s")."\n";
    $log .= (strlen($title) > 0 ? $title : 'DEBUG')."\n";
    $log .= print_r($data, 1);
    $log .= "\n------------------------\n";

    file_put_contents(__DIR__."/".DEBUG_FILE_NAME, $log, FILE_APPEND);

    return true;
}
?>