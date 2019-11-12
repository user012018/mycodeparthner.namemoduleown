<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

class CTestComponent extends CBitrixComponent{

	public function generateArOrder(){
		$this->arParams["AR_ORDER"] = Array();
	}

	public function generateArSelect(){
		$this->arParams["AR_SELECT"] = Array();
	}

	public function generateArFilter(){
		$this->arParams["AR_FILTER"] = Array();
		$this->arParams["AR_FILTER"]["IBLOCK_ID"] = $this->arParams["IBLOCK_ID"];
	}

	public function getItems(){
		if (!CModule::IncludeModule("iblock")){
			$this->AbortResultCache();
			ShowError(GetMessage("IBLOCK_MODULE_NOT_INSTALLED"));

			return;
		}

		$arFilter = $this->arParams["AR_FILTER"];
		$arOrder = $this->arParams["AR_ORDER"];
		$arSelect = $this->arParams["AR_SELECT"];

		$this->arResult["ITEMS"] = Array();
		$rs = CIBlockElement::GetList($arOrder, $arFilter, false, false, $arSelect);
		while ($ob = $rs->GetNextElement()){
			$arFields = $ob->GetFields();

			$this->arResult["ITEMS"][] = $arFields;
		}
	}

	public function executeComponent(){
		$this->generateArOrder();
		$this->generateArFilter();
		$this->generateArSelect();

		if ($this->startResultCache()){
			$this->getItems();
			$this->includeComponentTemplate();
		}
	}
}
?>