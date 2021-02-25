<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
$_SERVER["DOCUMENT_ROOT"] = "/home/bitrix/www";
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
require_once('../infotelephone/src/crest.php');


if (htmlspecialchars(isset($_POST['deal_id']))) {
    $result_deal = CRest::call('voximplant.statistic.get',
        [

            "SORT" => 'ID',
            "ORDER" => "DESC",
            "FILTER" => [
                'CRM_ENTITY_ID' => htmlspecialchars($_POST['deal_id']),
                'CRM_ENTITY_TYPE' => "DEAL"
            ]

        ]);
    $last_call_deal = $result_deal['result'][0]['CALL_START_DATE'];
}
if (htmlspecialchars(isset($_POST['lead_id']))) {
    $result_lead = CRest::call('voximplant.statistic.get',
        [

            "SORT" => 'ID',
            "ORDER" => "DESC",
            "FILTER" => [
                'CRM_ENTITY_ID' => htmlspecialchars($_POST['lead_id']),
                'CRM_ENTITY_TYPE' => "LEAD"
            ]

        ]);
    $last_call_lead = $result_lead['result'][0]['CALL_START_DATE'];
}
if (htmlspecialchars(isset($_POST['contact_id']))) {
    $result_contact = CRest::call('voximplant.statistic.get',
        [

            "SORT" => 'ID',
            "ORDER" => "DESC",
            "FILTER" => [
                'CRM_ENTITY_ID' => htmlspecialchars($_POST['contact_id']),
                'CRM_ENTITY_TYPE' => "CONTACT"
            ]

        ]);
    $last_call_contact = $result_contact['result'][0]['CALL_START_DATE'];
}
// выбор самого позднего события из всех сущностей
if ($last_call_contact >= $last_call_lead){

    if($last_call_contact >= $last_call_deal) {$result = $result_contact;} else $result = $result_deal;

} else if ($last_call_lead >= $last_call_deal) {$result = $result_lead;} else $result = $result_deal;


if ($result['result'][0]['CALL_FAILED_CODE'] == 200) {                    /////////////Статус звонка
    echo $succes = "Успешный";
} else if ($result['result'][0]['CALL_FAILED_CODE'] == 304) {
    echo $succes = "Пропущенный";
} else if ($result['result'][0]['CALL_FAILED_CODE'] == "603-S") {
    echo $succes = "Вызов отменен";
} else if ($result['result'][0]['CALL_FAILED_CODE'] == 403) {
    echo $succes = "Запрещено";
} else if ($result['result'][0]['CALL_FAILED_CODE'] == 404) {
    echo $succes = "Неверный номер";
} else if ($result['result'][0]['CALL_FAILED_CODE'] == 486) {
    echo $succes = "Занято";
} else if ($result['result'][0]['CALL_FAILED_CODE'] == 484) {
    echo $succes = "Данное направление не доступно";
} else if ($result['result'][0]['CALL_FAILED_CODE'] == 503) {
    echo $succes = "Данное направление не доступно";
} else if ($result['result'][0]['CALL_FAILED_CODE'] == 480) {
    echo $succes = "Временно не доступен";
} else if ($result['result'][0]['CALL_FAILED_CODE'] == 402) {
    echo $succes = "Недостаточно средств на счету";
} else if ($result['result'][0]['CALL_FAILED_CODE'] == 423) {
    echo $succes = "Заблокировано";
} else if ($result['result'][0]['CALL_FAILED_CODE'] == "OTHER") {
    echo $succes = "Не определен";
} else {
    false;
}

if ($result['result'][0]['CALL_TYPE'] == 2){
    echo $call_type ="входящий звонок";
} else if ($result['result'][0]['CALL_TYPE'] == 1){
    echo $call_type ="исходящий звонок";
}

$rsUser = CUser::GetByID($result['result'][0]['PORTAL_USER_ID']);
$arUser = $rsUser->Fetch();


CModule::IncludeModule("crm");
//$objDateTime = new DateTime();
//echo "Дата:  ".$result['result'][0]['CALL_START_DATE']."<br>"; //Дата
//echo "Юзер:  ".   $arUser['NAME']." ".$arUser['LAST_NAME'];
//$datezvonka = FormatDateFromDB($result['result'][0]['CALL_START_DATE'], 'SHORT', true);

$strtime = strtotime($result['result'][0]['CALL_START_DATE']) . "<br>";
$timeBitrix = date('d.m.Y H:i:s', $strtime);

//добавляем информацию в поля лида
if (htmlspecialchars(isset($_POST['lead_id']))) {
    $updateLead = CRest::call('crm.lead.update',
        [
            "id" => htmlspecialchars($_POST['lead_id']),
            "fields" => [
                'UF_CRM_1612879185342' => $succes." ".$call_type,                  /// Статус звонка
                'UF_CRM_1612879210893' => $timeBitrix,          //Дата звонка
                'UF_CRM_1612879227135' => $arUser['NAME'] . " " . $arUser['LAST_NAME']],                      // От кого юзер
            "params" => ['REGISTER_SONET_EVENT' => ['Y']]
        ]);
}

//    var_dump($result);
// "NAME" => $_POST['lists_name'],

//echo $datezvonka."<br>";
exit();

?>