<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\Entity;
use Bitrix\Iblock;

if (empty($arParams["CACHE_TIME"])){
    $arParams["CACHE_TIME"] = 36000000;
}
if (empty($arParams["PRODUCTS_IBLOCK_ID"])){
    $arParams["PRODUCTS_IBLOCK_ID"] = 2;
}
if (empty($arParams["NEWS_IBLOCK_ID"])){
    $arParams["NEWS_IBLOCK_ID"] = 1;
}
if (empty($arParams["PRODUCTS_IBLOCK_ID_PROPERTY"])){
    $arParams["PRODUCTS_IBLOCK_ID_PROPERTY"] = "UF_NEWS_LINK";
}

if ($this->startResultCache()) {
    $arNews = [];
    $arNewsID = [];
    
    $res = CIBlockElement::GetList(
        array(),
        array("IBLOCK_ID" => $arParams["NEWS_IBLOCK_ID"], "ACTIVE" => "Y"),
        false,
        array(),
        array("ID", "NAME", "DATE_ACTIVE_FROM")
    );
        
    while ($newsElements = $res->Fetch()) {
        $arNewsID[] = $newsElements["ID"];
        $arNews[$newsElements["ID"]] = $newsElements;
    }
    
    $arSections = array();
    $arSectionsID = array();
    
    // Получаем список активных разделов с привязкой к активным новостям
    $obSection = CIBlockSection::GetList(
        array(),
        array(
            "IBLOCK_ID" => $arParams["PRODUCTS_IBLOCK_ID"],
            "ACTIVE",
            $arParams["PRODUCTS_IBLOCK_ID_PROPERTY"] => $arNewsID
        ),
        true,
        array(
            "IBLOCK_ID",
            "ID",
            "NAME",
            $arParams["PRODUCTS_IBLOCK_ID_PROPERTY"]
        ),
        false
    );
    
    while ($arSectionCatalog = $obSection->Fetch()) {
        $arSectionsID[] = $arSectionCatalog["ID"];
        $arSections[$arSectionCatalog["ID"]] = $arSectionCatalog;
    }
    
    
    $arFilterElements = array(
        "IBLOCK_ID" => $arParams["PRODUCTS_IBLOCK_ID"],
        "ACTIVE" => "Y",
        "SECTION_ID" => $arSectionsID
    );
    
    //Получаем список активных товаров из разделов
    $obProduct = CIBlockElement::GetList(
        array(),
        $arFilterElements,
        false,
        array(),
        array("NAME", "IBLOCK_SECTION_ID", "ID", "IBLOCK_ID", "PROPERTY_ARTNUMBER", "PROPERTY_MATERIAL")
    );
    
    $arResult["PRODUCT_CNT"] = 0;
    while ($arProduct = $obProduct->Fetch()) {
        $arResult["PRODUCT_CNT"] ++;
        foreach ($arSections[$arProduct["IBLOCK_SECTION_ID"]][$arParams["PRODUCTS_IBLOCK_ID_PROPERTY"]] as $newsId) {

            $arNews[$newsId]["PRODUCTS"][] = $arProduct;    
            
        }
    }
    
    // Распределяем разделы по новостям
    foreach ($arSections as $arSection) {

        foreach ($arSection[$arParams["PRODUCTS_IBLOCK_ID_PROPERTY"]] as $newId) {
            if (isset($arNews[$newId])){
                $arNews[$newId]['SECTIONS'][] = $arSection["NAME"];
            }
        }        
    }
    
    $arResult["NEWS"] = $arNews;
    
    $this->SetResultCacheKeys(array("PRODUCT_CNT"));
    $this->includeComponentTemplate();
} else {
    $this->abortResultCache();
}
?>