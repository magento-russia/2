<?php
/**
 * @method Df_1C_Cml2_Export_Document_Catalog getDocument()
 */
class Df_1C_Cml2_Export_Processor_Catalog_CustomerGroup
	extends Df_Core_Xml_Generator_Part {
	/**
	 * @override
	 * @return array(string => string|mixed)
	 */
	public function getResult() {
		return df_clean_xml(array(
			'Ид' => $this->getИд()
			,'Наименование' => df_cdata($this->getGroup()->getCustomerGroupCode())
			,'Валюта' => $this->getDocument()->getExportCurrency()->getCode()
			/**
			 * Ветку «Налог» пока не добавляем.
			 * Согласно CommerceML 2.08 она должна выглядеть так:
					'Налог' => array(
						'Наименование' => ''
						,'УчтеноВСумме' => ''
					)
			 * http://v8.1c.ru/edi/edi_stnd/90/CML208.XSD
			 */

		));
	}

	/** @return Df_1C_Cml2_Export_Entry */
	private function entry() {return Df_1C_Cml2_Export_Entry::s();}

	/** @return Df_Customer_Model_Group */
	private function getGroup() {return $this->cfg(self::$P__GROUP);}

	/**
	 * @override
	 * @return string
	 */
	private function getИд() {
		if (!isset($this->{__METHOD__})) {
			if (!$this->getGroup()->get1CId()) {
				$this->getGroup()->set1CId(df_t()->guid());
				$this->getGroup()->save();
			}
			$this->{__METHOD__} = $this->getGroup()->get1CId();
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::$P__DOCUMENT, Df_1C_Cml2_Export_Document_Catalog::_C)
			->_prop(self::$P__GROUP, Df_Customer_Model_Group::_C)
		;
	}
	/** @var string */
	private static $P__GROUP = 'group';
	/**
	 * @used-by Df_1C_Cml2_Export_Document_Catalog::getКлассификатор_ТипыЦен_ТипЦены()
	 * @static
	 * @param Df_Customer_Model_Group $group
	 * @param Df_Core_Xml_Generator_Document $document
	 * @return Df_1C_Cml2_Export_Processor_Catalog_CustomerGroup
	 */
	public static function i(
		Df_Customer_Model_Group $group, Df_Core_Xml_Generator_Document $document
	) {
		return new self(array(self::$P__DOCUMENT => $document, self::$P__GROUP => $group));
	}
}