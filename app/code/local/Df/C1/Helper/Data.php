<?php
class Df_C1_Helper_Data extends Mage_Core_Helper_Abstract implements Df_Dataflow_Logger {
	/**
	 * @param int $attributeSetId
	 * @return void
	 */
	public function create1CAttributeGroupIfNeeded($attributeSetId) {
		df_param_integer($attributeSetId, 0);
		df_param_between($attributeSetId, 0, 1);
		df_h()->catalog()->product()->addGroupToAttributeSetIfNeeded(
			$attributeSetId
			,Df_C1_Const::PRODUCT_ATTRIBUTE_GROUP_NAME
			,$sortOrder = 2
		);
	}

	/**
	 * @param string $attributeLabel
	 * @param string|null $prefix [optional]
	 * @return string
	 */
	public function generateAttributeCode($attributeLabel, $prefix = null) {
		df_param_string_not_empty($attributeLabel, 0);
		return Df_Eav_Model_Entity_Attribute_Namer::i(
			$attributeLabel, array_filter(array('df_1c', $prefix))
		)->getResult();
	}

	/**
	 * @see Df_Dataflow_Logger::log()
	 * @override
	 * @param string $message
	 * @return void
	 */
	public function log($message) {
		if (df_c1_cfg()->general()->needLogging()) {
			/** @var mixed[] $arguments */
			$arguments = func_get_args();
			self::logger()->log(df_format($arguments));
		}
	}
	
	/**
	 * @param string|array(string|int => string) $message
	 * @return void
	 */
	public function logRaw($message) {
		if (df_c1_cfg()->general()->needLogging()) {
			/** @var mixed[] $arguments */
			$arguments = func_get_args();
			self::logger()->logRaw(df_format($arguments));
		}
	}

	/**
	 * @used-by Df_C1_Cml2_Action_Orders_Export::processFinish()
	 * @param string $path
	 * @param string $value
	 * @return void
	 */
	public function saveConfigValue($path, $value) {
		Mage::getConfig()->saveConfig(
			$path, $value, $scope = 'stores', $scopeId = df_state()->getStoreProcessed()->getId()
		);
		df_store()->setConfig($path, $value);
	}

	/** @return Df_C1_Helper_Data */
	public static function s() {static $r; return $r ? $r : $r = new self;}

	/**
	 * @used-by log()
	 * @used-by logRaw()
	 * @return Df_Core_Model_Logger
	 */
	private static function logger() {
		/** @var Df_Core_Model_Logger $result */
		static $result;
		if (!$result) {
			/** @var string $fileName */
			$filePath = Df_C1_Cml2_Session_ByCookie_1C::s()->getFileName_Log();
			if (!$filePath) {
				$filePath = df_file_name(
					df_cc_path(
						Mage::getBaseDir('var'), 'log'
						, df_c1_cfg()->general()->getLogFileNameTemplatePath()
					)
					, df_c1_cfg()->general()->getLogFileNameTemplateBaseName()
				);
				Df_C1_Cml2_Session_ByCookie_1C::s()->setFileName_Log($filePath);
			}
			$result = Df_Core_Model_Logger::s($filePath);
		}
		return $result;
	}
}