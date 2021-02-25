<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
$_SERVER["DOCUMENT_ROOT"] = "/home/bitrix/www";
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
require_once('../infotelephone/src/crest.php');
$token = "";
$secret = "";

$headers = array(
    'Content-type: application/json',
    'Accept: application/json',
    'Authorization: Token '.$token
);

$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, 'https://suggestions.dadata.ru/suggestions/api/4_1/rs/suggest/address');
curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

$query = json_encode(array('query' => $_POST['adres']));
curl_setopt($curl, CURLOPT_POSTFIELDS, $query);
$out = json_decode(curl_exec($curl));
echo $out->suggestions[0]->unrestricted_value;

function updateleadAdres($lead_id)
{
    global $out;
    $updateLead = CRest::call('crm.lead.update',
        [
            "id" => $lead_id,
            "fields" => [
                'UF_CRM_1551264449218' => $out->suggestions[0]->unrestricted_value],     // Адрес по паспорту лид
            "params" => ['REGISTER_SONET_EVENT' => ['Y']]
        ]);

}

function updateSdelAdres($sdelId) {
    global $out;
    $updateLead = CRest::call('crm.deal.update',
        [
            "id" => $sdelId,
            "fields" => [
                'UF_CRM_5C76AF094B303' => $out->suggestions[0]->unrestricted_value],     // Адрес по паспорту сделка
            "params" => ['REGISTER_SONET_EVENT' => ['Y']]
        ]);
}

if (!empty($_POST['id_lead'])) {
    updateleadAdres($_POST['id_lead']);
} else if ($out->suggestions[0]->unrestricted_value == 'null') {
    return false;
}

if (!empty($_POST['id_sdelk'])) {
    updateSdelAdres($_POST['id_sdelk']);
} else if ($out->suggestions[0]->unrestricted_value == 'null') {
    return false;
}


curl_close($curl);
exit();
?>