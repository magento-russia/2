<?php
abstract class Df_Dataflow_Model_Importer_Product_Categories_Parser
	extends Df_Core_Model {
	/**
	 * @abstract
	 * @return array
	 */
	abstract public function getPaths();

	/** @return string */
	protected function getImportedValue() {
		/** @var string $result */
		$result =
			df_trim(
				$this->cfg(self::P__IMPORTED_VALUE)
			)
		;
		df_result_string($result);
		return $result;
	}

	const P__IMPORTED_VALUE = 'importedValue';

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__IMPORTED_VALUE, new Zend_Validate_NotEmpty());
	}

}