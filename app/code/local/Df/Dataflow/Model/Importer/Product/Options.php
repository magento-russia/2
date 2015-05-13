<?php
class Df_Dataflow_Model_Importer_Product_Options extends Df_Dataflow_Model_Importer_Product_Specialized {
	/**
	 * @override
	 * @return Df_Dataflow_Model_Importer_Product_Options
	 */
	public function process() {
		foreach ($this->getImportedRow() as $key => $value) {
			/** @var string $key */
			/** @var mixed|null $value */
			df_assert_string($key);
			// Обратите внимание, что значение может отсутствовать (пустая ячейка в таблице)
			if (!is_null($value)) {
				$value = strval($value);
			}
			foreach ($this->getImporters() as $importer) {
				/** @var Df_Dataflow_Model_Importer_Product_Options_Format_Abstract $importer */
				if ($importer->canProcess($key)) {
					df_assert(is_object($importer));
					/** @var string $importerClass */
					$importerClass = get_class($importer);
					df_assert_string($importerClass);
					/** @var Df_Dataflow_Model_Importer_Product_Options_Format_Abstract $freshImporter */
					$freshImporter = new $importerClass;
					df_assert($freshImporter instanceof Df_Dataflow_Model_Importer_Product_Options_Format_Abstract);
					$freshImporter->setData(array(
						Df_Dataflow_Model_Importer_Product_Options_Format_Abstract
							::P__PRODUCT => $this->getProduct()
						,Df_Dataflow_Model_Importer_Product_Options_Format_Abstract
							::P__IMPORTED_KEY => df_trim($key)
						,Df_Dataflow_Model_Importer_Product_Options_Format_Abstract
							::P__IMPORTED_VALUE => $value
					));
					$freshImporter->process();
					$this->getProduct()->setData(Df_Catalog_Model_Product::P__HAS_OPTIONS, true);
					$this->getProduct()->save();
				}
			}
		}
		return $this;
	}

	/** @return Df_Dataflow_Model_Importer_Product_Options_Format_Abstract[] */
	private function getImporters() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = array(
				Df_Dataflow_Model_Importer_Product_Options_Format_Json::i()
				,Df_Dataflow_Model_Importer_Product_Options_Format_Simple::i()
			);
		}
		return $this->{__METHOD__};
	}

	const _CLASS = __CLASS__;
	/**
	 * @static
	 * @param Df_Catalog_Model_Product $product
	 * @param array(string => mixed) $row
	 * @return Df_Dataflow_Model_Importer_Product_Options
	 */
	public static function i(Df_Catalog_Model_Product $product, array $row) {return new self(array(
		self::P__PRODUCT => $product, self::P__IMPORTED_ROW => $row
	));}
}