<?php
class Df_Dataflow_Exception_Import_RequiredValueIsAbsent extends Df_Dataflow_Exception_Import {
	/** @return string */
	public function getFieldName() {return $this->cfg(self::$P__FIELD_NAME);}

	/**
	 * @override
	 * @return string
	 */
	public function getMessageRm() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = sprintf(
				'В строке импортируемых данных №%d требуется (и сейчас отсутствует) поле «%s».%s'
				,$this->getRowOrdering()
				,$this->getFieldName()
				,parent::getMessageRm()
			);
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param string $fieldName
	 * @return Df_Dataflow_Exception_Import_RequiredValueIsAbsent
	 */
	public function setFieldName($fieldName) {
		$this->setData(self::$P__FIELD_NAME, $fieldName);
		return $this;
	}

	/**
	 * @param int $rowOrdering
	 * @return Df_Dataflow_Exception_Import_RequiredValueIsAbsent
	 */
	public function setRowOrdering($rowOrdering) {
		$this->setData(self::$P__ROW_ORDERING, $rowOrdering);
		return $this;
	}

	/** @return int */
	protected function getRowOrdering() {return $this->cfg(self::$P__ROW_ORDERING);}

	/** @var string */
	private static $P__FIELD_NAME = 'field_name';
	/** @var int */
	private static $P__ROW_ORDERING = 'row_ordering';
}

