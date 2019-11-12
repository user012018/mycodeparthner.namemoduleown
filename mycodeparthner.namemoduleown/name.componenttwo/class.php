<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

class CTestComponent extends CBitrixComponent{

	public function generateArOrder(){
		$this->arParams["AR_ORDER"] = Array(
			"SORT" => "ASC"
		);
	}

	public function generateArSelect(){
		$this->arParams["AR_SELECT"] = Array();
	}

	public function generateArFilter(){
		$this->arParams["AR_FILTER"] = Array();
		$this->arParams["AR_FILTER"]["IBLOCK_ID"] = $this->arParams["IBLOCK_ID"];
	}

	public function getSections(){
		if (!CModule::IncludeModule("iblock")){
			$this->AbortResultCache();
			ShowError(GetMessage("IBLOCK_MODULE_NOT_INSTALLED"));

			return;
		}

		$dbSections = CIBlockSection::GetList(
			Array(
				"SORT" => "ASC"
			),
			Array(
				'IBLOCK_ID' => $this->arParams["IBLOCK_ID"],
				'ACTIVE'    => 'Y',
			)
		);
		while ($arSection = $dbSections->GetNext()){
			$this->arResult["SECTIONS"][$arSection["ID"]] = $arSection;
		}
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
			$arFields["PROPS"] = $ob->GetProperties();

			$this->arResult["ITEMS"][$arFields["ID"]] = $arFields;
		}
	}

	public function groupItemsBySections(){
		foreach ($this->arResult["ITEMS"] as $id => $item){
			if ($item["IBLOCK_SECTION_ID"]){
				$this->arResult["SECTIONS"][$item["IBLOCK_SECTION_ID"]]["ITEMS"][$id] = $item;
				unset($this->arResult["ITEMS"][$id]);
			}
		}
	}

	public function executeComponent(){
		$this->generateArOrder();
		$this->generateArFilter();
		$this->generateArSelect();

		if ($this->startResultCache()){
			$this->getSections();
			$this->getItems();
			$this->groupItemsBySections();

			$this->includeComponentTemplate();
		}
	}
}

?>