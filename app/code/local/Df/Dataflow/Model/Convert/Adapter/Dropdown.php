<?php
class Df_Dataflow_Model_Convert_Adapter_Dropdown
	extends Mage_Dataflow_Model_Convert_Adapter_Abstract {
	/**
	 * @override
	 * @return Df_Dataflow_Model_Convert_Adapter_Dropdown
	 */
	public function load() {return $this;}

	/**
	 * @override
	 * @return Df_Dataflow_Model_Convert_Adapter_Dropdown
	 */
	public function save() {return $this;}

	/**
	 * @override
	 * @param array $importData
	 * @return Df_Dataflow_Model_Convert_Adapter_Dropdown
	 */
	public function saveRow(array $importData) {
		$this->_rowData = $importData;
		if (!$this->getRowIndex()) {
			$this->deleteAllOptions();
		}
		$this
			->getAttribute()
			->addData(
				array(
					'option' =>
						array(
							'value' =>
								array(
									'option_0' =>
										array(
											$this->storeId() => $this->getOption()
										)
								)
							,'order' =>
								array(
									'option_0' => $this->getRowIndex()
								)

						)
				)
			)
			->save()
		;
		$this->setSessionParam('rowIndex', 1 + $this->getRowIndex());
		return $this;
	}
	/** @var int */
	private $_rowIndex;

	/** @return void */
	private function deleteAllOptions() {
		/** @var array(array(string => string|int)) $allOptions */
		$allOptions = $this->getAttribute()->getSource()->getAllOptions(false);
		$this->getAttribute()->addData(array('option' => array(
			'value' => array_column($allOptions, 'label', 'value')
			, 'delete' => array_fill_keys(array_column($allOptions, 'value'), 1)
		)));
		$this->getAttribute()->save();
	}

	/** @return Df_Catalog_Model_Resource_Eav_Attribute */
	private function getAttribute() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->getAttributeByCode($this->getAttributeCode());
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param string $code
	 * @return Df_Catalog_Model_Resource_Eav_Attribute
	 */
	private function getAttributeByCode($code) {
		$attributes = $this->getSessionParam('attributes', array());
		if (!isset($attributes[$code])) {
			$attributes[$code] =
				Df_Catalog_Model_Resource_Eav_Attribute::i()->loadByCode(df_eav_id_product(), $code)
			;
			$this->setSessionParam('attributes', $attributes);
		}
		return $attributes[$code];
	}
	/** @var Df_Catalog_Model_Resource_Eav_Attribute[] */
	private $_rowData = array();

	/** @return string */
	private function getAttributeCode() {
		return $this->getRowData('attribute', $this->getBatchParams('attribute'));
	}

	/** @return string */
	private function getOption() {return $this->getRowData('option');}

	/**
	 * @param string $key
	 * @param null|mixed $default
	 * @return string
	 */
	private function getRowData($key, $default = null) {
		return isset($this->_rowData[$key]) ? $this->_rowData[$key] : $default;
	}

	/** @return int */
	private function getRowIndex() {
		if (!isset($this->_rowIndex)) {
			$this->_rowIndex = $this->getSessionParam('rowIndex', 0);
		}
		return $this->_rowIndex;
	}

	/** @return string */
	private function getSessionKey() {
		return df_sprintf('%s_%s', get_class($this), $_REQUEST['batch_id']);
	}

	/**
	 * @param string $key
	 * @param mixed $default
	 * @return mixed
	 */
	private function getSessionParam($key, $default = null) {
		$storage = $this->getSessionStorage();
		return isset($storage[$key]) ? $storage[$key] : $default;
	}

	/** @return mixed[] */
	private function getSessionStorage() {
		$result = df_session_core()->getData($this->getSessionKey());
		if (!$result) {
			$result = array();
		}
		return $result;
	}

	/**
	 * @param array $storage
	 * @return Df_Dataflow_Model_Convert_Adapter_Dropdown
	 */
	private function setSessionStorage(array $storage) {
		df_session_core()->setData($this->getSessionKey(), $storage);
		return $this;
	}

	/**
	 * @param string $key
	 * @param mixed $value
	 * @return Df_Dataflow_Model_Convert_Adapter_Dropdown
	 */
	private function setSessionParam($key, $value) {
		$storage = $this->getSessionStorage();
		$storage[$key] = $value;
		$this->setSessionStorage($storage);
		return $this;
	}

	/** @return string */
	private function storeId() {return $this->getRowData('store', $this->getBatchParams('store'));}
}