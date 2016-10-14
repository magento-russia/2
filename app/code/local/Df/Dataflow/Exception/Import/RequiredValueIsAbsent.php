<?php
class Df_Dataflow_Exception_Import_RequiredValueIsAbsent extends Df_Dataflow_Exception_Import {
	/**
	 * @param string $fieldName
	 * @param int $rowOrdering
	 * @return Df_Dataflow_Exception_Import_RequiredValueIsAbsent
	 */
	public function __construct($fieldName, $rowOrdering) {
		$this->_fieldName = $fieldName;
		$this->_rowOrdering = $rowOrdering;
	}

	/**
	 * @used-by Df_1C_Cml2_Import_Processor_Product_Type_Simple::process()
	 * @used-by getMessageRm()
	 * @return string
	 */
	public function getFieldName() {return $this->_fieldName;}

	/**
	 * @override
	 * @return string
	 */
	public function getMessageRm() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = sprintf(
				'В строке импортируемых данных №%d требуется (и сейчас отсутствует) поле «%s».%s'
				,$this->_rowOrdering
				,$this->getFieldName()
				,parent::getMessageRm()
			);
		}
		return $this->{__METHOD__};
	}

	/** @var string */
	private $_fieldName;
	/** @var int */
	private $_rowOrdering;
}

