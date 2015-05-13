<?php
class Df_Dataflow_Model_Importer_Product_Options_Format_Json
	extends Df_Dataflow_Model_Importer_Product_Options_Format_Abstract {
	/** @return Df_Dataflow_Model_Importer_Product_Options_Format_Json */
	public function process() {
		try {
			/** @var string|null $customOptionsAsJson */
			$customOptionsAsJson = $this->getImportedValue();
			if (!is_null($customOptionsAsJson)) {
				df_assert_string($customOptionsAsJson);
			}
			if (!is_null($customOptionsAsJson)) {
				$this->getProduct()->deleteOptions();
				/** @var array|null $customOptionsData */
				$customOptionsData =
					/**
					 * Zend_Json::decode использует json_decode при наличии расширения PHP JSON
					 * и свой внутренний кодировщик при отсутствии расширения PHP JSON.
					 * @see Zend_Json::decode
					 * @link http://stackoverflow.com/questions/4402426/json-encode-json-decode-vs-zend-jsonencode-zend-jsondecode
					 * Обратите внимание,
					 * что расширение PHP JSON не входит в системные требования Magento.
					 * @link http://www.magentocommerce.com/system-requirements
					 * Поэтому использование Zend_Json::decode выглядит более правильным, чем json_decode.
					 */
					Zend_Json::decode(
						$customOptionsAsJson
					)
				;
				if (!is_null($customOptionsData)) {
					df_assert_array($customOptionsData);
				}
				if (!is_null($customOptionsData)) {
					/** @var Df_Catalog_Model_Product_Option $aggregateOption */
					$aggregateOption = Df_Catalog_Model_Product_Option::i();
					$aggregateOption->setProduct($this->getProduct());
					foreach ($customOptionsData as $customOptionData) {
						/** @var array Mage_Catalog_Model_Product_Option */
						df_assert_array($customOptionData);
						/**
						 * If option has id then Magento assumes that option already exists in DB
						 * So, because we delete all options above, we unset option_id too.
						 *
						 * Если у опции присутствует идентификатор, то Magento считает, 
						 * что опция уже присутствует в базе данных, 
						 * и записывает опцию в базе данных алгоритмом UPDATE, а не INSERT.
						 * Однако, мы удалили все опции выше (Df_Catalog_Model_Product::deleteOptions()),
						 * поэтому убираем у импортируемой опции идентификатор,
						 * чтобы Magento считала опцию совершенно новой и использовала алгоритм INSERT.
						 */
						unset($customOptionData['option_id']);
						/** @var array|null $optionValuesData */
						$optionValuesData = df_a($customOptionData, '_values');
						if (!is_null($optionValuesData)) {
							df_assert_array($optionValuesData);
						}
						if (!is_null($optionValuesData)) {
							foreach ($optionValuesData as &$optionValueData) {
								/** @var array $optionValueData */
								df_assert_array($optionValueData);
								unset($optionValueData['option_type_id']);
							}

						}
						$customOptionData['values'] = $optionValuesData;
						unset($customOptionData['_values']);
						unset($customOptionData['_options']);
						$aggregateOption->addOption($customOptionData);
					}

					$aggregateOption->saveOptions();
				}
			}
		}
		catch(Exception $e) {
			df_handle_entry_point_exception($e, true);
		}
		return $this;
	}

	/** @return string */
	protected function getPattern() {
		/** @var string $result */
		$result = "#^\s*df_custom_options\s*$#";
		df_result_string($result);
		return $result;
	}

	/** @return Df_Dataflow_Model_Importer_Product_Options_Format_Json */
	public static function i() {return new self;}
}