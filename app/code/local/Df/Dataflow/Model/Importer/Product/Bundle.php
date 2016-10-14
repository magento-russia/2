<?php
class Df_Dataflow_Model_Importer_Product_Bundle extends Df_Dataflow_Model_Importer_Product_Specialized {
	/**
	 * @override
	 * @return Df_Dataflow_Model_Importer_Product_Bundle
	 */
	public function process() {
		if ('bundle' === $this->getProduct()->getTypeId()) {
			if ($this->getProduct()->getId()) {
				Df_Bundle_Model_Resource_Bundle::s()->deleteAllOptions($this->getProduct()->getId());
			}
			/**
			 * Не знаю, зачем это, но так рекомендуют на Stack Overflow
			 * http://stackoverflow.com/questions/3108775/programmatically-add-bundle-products-in-magento-using-the-sku-id-of-simple-ite
			 */
			Mage::register('product', $this->getProduct());
			Mage::register('current_product', $this->getProduct());
			$this->getProduct()
				->addData(
					array(
						'can_save_configurable_attributes' => false
						,'can_save_custom_options' => true
						,'can_save_bundle_selections' => true
						,'has_options' => 1
						/**
						 * Обратите внимание, что с связь между bundle_options и bundle_selections
						 * при импорте осуществляется вовсе не через option_id,
						 * а через порядок следования данных.
						 * Элементу с порядковым номером N в массиве bundle_options_data
						 * соответствует элемент с порядковым номером N в массиве bundle_selections_data.
						 * Учитывая, что элементы массива bundle_selections_data — это массивы
						 * (группы подэлементов),
						 * между bundle_options и bundle_selections получается связь «один ко многим».
						 */
						,'bundle_options_data' => $this->getBundleOptions()
						,'bundle_selections_data' => $this->getBundleSelections()
					)
				)
			;
		}
		return $this;
	}

	/** @return array(array(string => string|int|null)) */
	private function getBundleOptions() {
		if (!isset($this->{__METHOD__})) {
			/** @var array(array(string => string|int|null)) $result */
			$result = array();
			/**
			 * Порядковый номер внутреннего товара в сборном товаре
			 * @var int $innerProductOrdering
			 */
			$innerProductOrdering = 0;
			foreach ($this->getInnerProducts() as $innerProduct) {
				/** @var array $innerProduct */
				df_assert_array($innerProduct);
				$innerProductOrdering++;
				$result[]= $this->createBundleOption($innerProduct, $innerProductOrdering);
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return array(array(array(string => string|int|null))) */
	private function getBundleSelections() {
		if (!isset($this->{__METHOD__})) {
			/** @var array(array(array(string => string|int|null))) $result */
			$result = array();
			foreach ($this->getInnerProducts() as $innerProduct) {
				/** @var array $innerProduct */
				df_assert_array($innerProduct);
				// Всего один элемент в массиве,
				// потому что мы не даём покупателю выбор составных частей сборного товара
				$result[]= array($this->createBundleSelection($innerProduct));
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param array $innerProduct
	 * @return array
	 */
	private function createBundleSelection(array $innerProduct) {
		df_param_array($innerProduct, 0);
		/** @var string $innerProductSku */
		$innerProductSku = dfa($innerProduct, 'material');
		df_assert_string($innerProductSku);
		/** @var int|null $innerProductId */
		$innerProductId = df_h()->catalog()->product()->getIdBySku($innerProductSku);
		df_assert(
			!is_null($innerProductId)
			,df_sprintf(
				'Перед импортом сборного товара «%s» '
				. 'в системе уже должна пристутствовать его составная часть: простой товар «%s».'
				,$this->getBundleOriginalSku()
				,$innerProductSku
			)
		);
		/** @var array $result */
		$result =
			array(
				'product_id' => $innerProductId
				,'selection_price_value' => 0
				,'selection_price_type' => 0
				,'selection_qty' => dfa($innerProduct, 'Count')
				,'selection_can_change_qty' => 0
				/**
				 * Всегда 0, потому что других вариантов выбора у пользователя нет
				 */
				,'position' => 0
				/**
				 * Иначе «undefined index»
				 */
				,'delete' => null
			)
		;
		return $result;
	}

	/**
	 * @param array $innerProduct
	 * @param int $innerProductOrdering
	 * @return array
	 */
	private function createBundleOption(array $innerProduct, $innerProductOrdering) {
		df_param_array($innerProduct, 0);
		df_param_integer($innerProductOrdering, 1);
		/** @var string $innerProductSku */
		$innerProductSku = dfa($innerProduct, 'material');
		df_assert_string($innerProductSku);
		/** @var int|null $innerProductId */
		$innerProductId = df_h()->catalog()->product()->getIdBySku($innerProductSku);
		df_assert(
			!is_null($innerProductId)
			,df_sprintf(
				'Перед импортом сборного товара «%s» '
				. 'в системе уже должна пристутствовать его составная часть: простой товар «%s».'
				,$this->getBundleOriginalSku()
				,$innerProductSku
			)
		)
		;
		/** @var array $result */
		$result =
			array(
				/**
				 * По собственному желанию используем артикул внутреннего товара как заголовок опции.
				 * Система этого не требует
				 */
				'title' => $innerProductSku
				,'type' => 'select'
				,'required' => 1
				,'position' => $innerProductOrdering

				/**
				 * Иначе «undefined index»
				 */
				,'delete' => null
			)
		;
		return $result;
	}

	/**
	 * Это тот артикул сборного товара, который известен администратору.
	 * Обратите внимание, что данный артикул в тестовых данных не является уникальным,
	 * поэтому система присвоит сборному товару новый артикул, * добавив к оригинальному пртикулу суффикс  «-bundle».
	 * @return string
	 */
	private function getBundleOriginalSku() {return dfa($this->getImportedRow(), 'vichy_original_sku');}

	/** @return array */
	private function getInnerProducts() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_json_decode(
				dfa($this->getImportedRow(), Df_Dataflow_Model_Import_Product_Row::FIELD__BUNDLE)
			);
			df_result_array($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}

	const _C = __CLASS__;
	/**
	 * @static
	 * @param Df_Catalog_Model_Product $product
	 * @param array(string => mixed) $row
	 * @return Df_Dataflow_Model_Importer_Product_Bundle
	 */
	public static function i(Df_Catalog_Model_Product $product, array $row) {return new self(array(
		self::P__PRODUCT => $product, self::P__IMPORTED_ROW => $row
	));}
}