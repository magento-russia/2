<?php
/**
 * @method Df_1C_Cml2_Export_Document_Catalog getDocument()
 */
class Df_1C_Cml2_Export_Processor_Catalog_Category extends Df_Catalog_Model_XmlExport_Category {
	/**
	 * Структуру данных получил из анализа программного кода
	 * обработки «Б_ПомощникИмпортаТоваровБитрикс»
	 * (в частности, смотрите метод «ОбработатьЗначениеЭлемента»)
	 * @override
	 * @return array(string => mixed)
	 */
	public function getResult() {
		/** @var array(string => mixed) $result */
		$result = array(
			'Ид' => $this->getExternalId()
			,'Наименование' => df_cdata($this->getCategory()->getName())
		);
		if ($this->getChildren()) {
			$result['Группы'] = self::process($this->getChildren(), $this->getDocument());
		}
		return $result;
	}

	/**
	 * Не экспортируем системный (скрытый от администратора Magento)
	 * корневой товарный раздел «Root Category».
	 * @override
	 * @return bool
	 */
	public function isEligible() {return !!$this->getCategory()->getId();}

	/** @return string */
	private function getExternalId() {
		if (!isset($this->{__METHOD__})) {
			if (!$this->getCategory()->get1CId()) {
				$this->getCategory()->set1CId(df_t()->guid());
				$this->getCategory()->saveRm($this->getDocument()->store());
			}
			$this->{__METHOD__} = $this->getCategory()->get1CId();
		}
		return $this->{__METHOD__};
	}

	/**
	 * @used-by Df_1C_Cml2_Export_Document_Catalog::getКлассификатор_Группы()
	 * @param Df_Catalog_Model_Category[] $categories
	 * @param Df_1C_Cml2_Export_Document_Catalog $document
	 * @return array(array(string => mixed))
	 */
	public static function process(array $categories, Df_1C_Cml2_Export_Document_Catalog $document) {
		/** @var array(array(string => mixed)) $result */
		$result = array();
		if ($categories) {
			/** @var array(array(string => mixed)) $groups */
			$groups = array();
			foreach ($categories as $category) {
				/** @var Df_Catalog_Model_Category $category */
				/** @var Df_1C_Cml2_Export_Processor_Catalog_Category $processor */
				$processor = self::ic(__CLASS__, $category, $document);
				if ($processor->isEligible()) {
					$groups[]= $processor->getResult();
				}
			}
			$result['Группа'] = $groups;
		}
		return $result;
	}
}