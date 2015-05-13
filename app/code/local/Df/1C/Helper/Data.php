<?php
class Df_1C_Helper_Data extends Mage_Core_Helper_Abstract implements Df_Dataflow_Logger {
	/**
	 * @param int $attributeSetId
	 * @return void
	 */
	public function create1CAttributeGroupIfNeeded($attributeSetId) {
		df_param_integer($attributeSetId, 0);
		df_param_between($attributeSetId, 0, 1);
		df_h()->catalog()->product()->addGroupToAttributeSetIfNeeded(
			$attributeSetId
			,Df_1C_Const::PRODUCT_ATTRIBUTE_GROUP_NAME
			,$sortOrder = 2
		);
	}

	/** @return Df_1C_Helper_Cml2 */
	public function cml2() {return Df_1C_Helper_Cml2::s();}

	/**
	 * @param string|float|int $money
	 * @return string
	 */
	public function formatMoney($money) {return rm_sprintf(rm_float($money), '.2f');}

	/**
	 * @param string $attributeLabel
	 * @param string|null $prefix [optional]
	 * @return string
	 */
	public function generateAttributeCode($attributeLabel, $prefix = null) {
		df_param_string_not_empty($attributeLabel, 0);
		return Df_Eav_Model_Entity_Attribute_Namer::i($attributeLabel, df_clean(array('rm_1c', $prefix)))
			->getResult()
		;
	}

	/**
	 * @param string $message
	 * @return Df_1C_Helper_Data
	 */
	public function log($message) {
		if (df_cfg()->_1c()->general()->needLogging()) {
			/** @var mixed[] $arguments */
			$arguments = func_get_args();
			Df_1C_Model_Logger::s2()->log(rm_sprintf($arguments));
		}
		return $this;
	}
	
	/**
	 * @param string|array(string|int => string) $message
	 * @return Df_1C_Helper_Data
	 */
	public function logRaw($message) {
		if (df_cfg()->_1c()->general()->needLogging()) {
			/** @var mixed[] $arguments */
			$arguments = func_get_args();
			Df_1C_Model_Logger::s2()->logRaw(rm_sprintf($arguments));
		}
		return $this;
	}

	/** @return Df_1C_Helper_Data */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}