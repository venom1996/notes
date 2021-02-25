<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
$sdelID = preg_replace('/[^0-9,]/', '', $_POST['PLACEMENT_OPTIONS']); ////////////// вывод с сделки
$arFilter = array("IBLOCK_ID" => "115", "PROPERTY_528_VALUE" => $sdelID);
$res = CIBlockElement::GetList(array(), $arFilter);
if ($ob = $res->GetNextElement()) {
    $arFields = $ob->GetFields(); // поля элемента
    $arProps = $ob->GetProperties(); // свойства элемента
}

$arFilter = array("IBLOCK_ID" => "115", "PROPERTY_527_VALUE" => $sdelID);   //вывод c контактов
$res = CIBlockElement::GetList(array(), $arFilter);
if ($ob = $res->GetNextElement()) {
    $arFields = $ob->GetFields(); // поля элемента
    $arProps = $ob->GetProperties(); // свойства элемента
}

$arFilter = array("IBLOCK_ID" => "115", "PROPERTY_529_VALUE" => $sdelID);   //вывод c лидов
$res = CIBlockElement::GetList(array(), $arFilter);
if ($ob = $res->GetNextElement()) {
    $arFields = $ob->GetFields(); // поля элемента
    $arProps = $ob->GetProperties(); // свойства элемента
//echo "<pre>";
//print_r($arProps);
//echo "</pre>";
//echo "<pre>";
//print_r($arFields);
//echo "</pre>";

}

$contact_id = $_POST['idContact'][0]['CONTACT_ID'];    // беру id контакта

//echo $contact_id;
$PROP = array();
$PROP[530] = $_POST['fio'];
$PROP[528] = $_POST['id'];
$PROP[532] = $_POST['adresregis'];
$PROP[533] = $_POST['adressproj'];


if (!isset($_POST['id_anketa']) and (isset($_POST['fio'])) and ($_POST['fio'] != '')) {     //Проверяю, если это поле не пустое то записываю в поля
    $el = new CIBlockElement;


    $arLoadArray = array(
        "MODIFIED_BY" => $USER->GetID(),
        "IBLOCK_SECTION_ID" => false,
        "IBLOCK_ID" => 115,
        "PROPERTY_VALUES" => $PROP,
        "NAME" => $_POST['fio'],
        "ACTIVE" => "Y",
    );

    if ($anketa_ID = $el->Add($arLoadArray))
        echo  $anketa_ID;

    else
        echo "Error: " . $el->LAST_ERROR;

    $wfId = CBPDocument::StartWorkflow(           //Запуск бп
        813,
        array('lists', 'BizprocDocument', $anketa_ID),
        array_merge(array(), array()),
        $arErrorsTmp
    );
}

if (isset($_POST['id_anketa'])) {             //Обновление полей

    $el = new CIBlockElement;
    $arLoadUpdateArray = Array(
        "MODIFIED_BY"    => $USER->GetID(), // элемент изменен текущим пользователем
        "IBLOCK_SECTION" => false,
        "PROPERTY_VALUES"=> $PROP,
        "NAME"           => $_POST['fio'],
        "ACTIVE"         => "Y",            // активен

    );

    $res = $el->Update($_POST['id_anketa'], $arLoadUpdateArray);

}
// echo "<pre>";
// print_r($arFields);
// echo "</pre>";

?>