<?php
class Df_Dataflow_Model_Importer_Product_Categories
	extends Df_Dataflow_Model_Importer_Product_Specialized {
	/**
	 * @param string[] $path
	 * @return Df_Catalog_Model_Category[]
	 */
	public function getCategoriesByPath($path) {
		return Df_Dataflow_Model_Category_Path::i($path, $this->store())->getCategories();
	}

	/**
	 * @override
	 * @throws Exception
	 * @return Df_Dataflow_Model_Importer_Product_Categories
	 */
	public function process() {
		try {
			if ($this->getImportedValue() && $this->getCategoryIds()) {
				$this->getProduct()->setCategoryIds($this->getCategoryIds());
			}
		}
		catch (Exception $e) {
			df_handle_entry_point_exception($e);
		}
		return $this;
	}

	/** @return string[] */
	private function getCategories() {
		if (!isset($this->{__METHOD__})) {
			/** @var string[] $result */
			$result = array();
			foreach ($this->getPaths() as $path) {
				/** @var string[] $path */
				$result = array_merge($result, $this->getCategoriesByPath($path));
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return int[] */
	private function getCategoryIds() {
		if (!isset($this->{__METHOD__})) {
			/**
			 * С @see rm_array_unique_fast() постоянно возникакает проблема
			 * array_flip(): Can only flip STRING and INTEGER values
			 * http://magento-forum.ru/topic/4695/
			 * Лучше верну-ка старую добрую функцию @see array_unique()
			 *
			 * 2015-02-11
			 * Функцию @see rm_array_unique_fast() доработал и ввёл в действите снова.
			 */
			$this->{__METHOD__} = rm_array_unique_fast(df_each($this->getCategories(), 'getId'));
		}
		return $this->{__METHOD__};
	}

	/** @return string|null */
	private function getImportedValue() {return df_a($this->getImportedRow(), self::IMPORTED_KEY);}

	/** @return Df_Dataflow_Model_Importer_Product_Categories_Parser[] */
	private function getParsers() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_Dataflow_Model_Importer_Product_Categories_Parser[] $result */
			$result =
				array(
					Df_Dataflow_Model_Importer_Product_Categories_Format_Simple::i()
					,Df_Dataflow_Model_Importer_Product_Categories_Format_Json::i()
					,Df_Dataflow_Model_Importer_Product_Categories_Format_Null::i()
				)
			;
			foreach ($result as $parser) {
				/** @var Df_Dataflow_Model_Importer_Product_Categories_Parser $parser */
				$parser->setData(
					Df_Dataflow_Model_Importer_Product_Categories_Parser::P__IMPORTED_VALUE
					,$this->getImportedValue()
				);
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return string[] */
	private function getPaths() {
		if (!isset($this->{__METHOD__})) {
			/** @var string[] $result */
			$result = array();
			foreach ($this->getParsers() as $parser) {
				/** @var Df_Dataflow_Model_Importer_Product_Categories_Parser $parser */
				$result = $parser->getPaths();
				if ($result) {
					break;
				}
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Core_Model_StoreM */
	private function store() {return $this->cfg(self::P__STORE);}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__STORE, Df_Core_Model_StoreM::_C);
	}
	const _C = __CLASS__;
	const IMPORTED_KEY = 'df_categories';
	const P__STORE = 'store';
	/**
	 * @static
	 * @param Df_Catalog_Model_Product $product
	 * @param array(string => mixed) $row
	 * @param Df_Core_Model_StoreM $store
	 * @return Df_Dataflow_Model_Importer_Product_Categories
	 */
	public static function i(Df_Catalog_Model_Product $product, array $row, Df_Core_Model_StoreM $store) {
		return new self(array(
			self::P__PRODUCT => $product
			, self::P__IMPORTED_ROW => $row
			, self::P__STORE => $store
		));
	}
}