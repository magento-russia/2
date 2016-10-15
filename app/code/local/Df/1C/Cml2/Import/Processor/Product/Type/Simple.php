<?php
class Df_1C_Cml2_Import_Processor_Product_Type_Simple
	extends Df_1C_Cml2_Import_Processor_Product_Type_Simple_Abstract {
	/**
	 * @override
	 * @return void
	 * @throws Exception|Df_Dataflow_Exception_Import_RequiredValueIsAbsent
	 */
	public function process() {
		if (!$this->getEntityOffer()->isTypeSimple()) {
			df_1c_log(
				'Пропускаем товарное предложение «%s» как не являющееся простым товаром.'
				,$this->getEntityOffer()->getName()
			);
		}
		else {
			try {
				$this->getImporter()->import();
			}
			catch (Df_Dataflow_Exception_Import_RequiredValueIsAbsent $e) {
				df_assert(!$this->getExistingMagentoProduct());
				/** @var string $name */
				$name = $this->getEntityOffer()->getName();
				if (!$name) {
					$name = $e->getRow()->getFieldValue('sku');
				}
				if (!$name) {
					$name = $e->getRow()->getFieldValue('df_1c_id');
				}
				$name = $name ? sprintf(' «%s»', $name) : '';
				df_error(
					'При импорте в интернет-магазин нового товара%s'
					. "\nсистема не смогла узнать для этого товара"
					. ' требуемое значение свойства «%s».'
					. "\nОбратитесь к разработчику."
					. "\nЗначения всех импортировавшихся свойств:\n%s"
					, $name
					, $e->getFieldName()
					, df_tab_multiline(df_print_params($e->getRow()->getAsArray()))
				);
			}
			catch (Exception $e) {
				throw $e;
			}
			/** @var Df_Catalog_Model_Product $product */
			$product = $this->getImporter()->getProduct();
			df_1c_reindex_product($product);
			df_1c_log(
				'%s товар %s.'
				, $this->getExistingMagentoProduct() ? 'Обновлён' : 'Создан'
				, $product->getTitle()
			);
			df()->registry()->products()->addEntity($product);
			df_assert(!is_null($this->getExistingMagentoProduct()));
		}
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getSku() {
		if (!isset($this->{__METHOD__})) {
			/** @var string|null $result */
			$result = null;
 			if (!is_null($this->getExistingMagentoProduct())) {
				$result = $this->getExistingMagentoProduct()->getSku();
				df_assert_sku($result);
			}
			else {
				/**
				 * Данный товар ранее не импортировался
				 * из 1С:Управление торговлей в интернет-магазин.
				 * Сначала пробуем в качестве артикула товара в интернет-магазине
				 * взять артикул товара из 1С:Управление торговлей
				 */
				/** @var string|null $externalSku */
				$externalSku = $this->getEntityProduct()->getSku();
				// У товара в 1С:Управление торговлей может отсутствовать артикул
				if (!$externalSku) {
					// У этого товара нет артикула в 1С:Управление торговлей
					df_1c_log(
						'У товара «%s» в 1С отсутствует артикул.', $this->getEntityProduct()->getName()
					);
				}
				else {
					$externalSku = df_sku_adapt($externalSku);
					/**
					 * Убеждаемся, что ни один товар в интернет-магазине
					 * ещё не использует этот артикул
					 */
					if (!df_h()->catalog()->product()->isExist($externalSku)) {
						$result = $externalSku;
					}
					else {
						df_1c_log('Товар с артикулом «%s» уже присутствует в магазине.', $externalSku);
					}
				}
				if (!$result) {
					/**
					 * Итак, использовать артикул 1С:Управление торговлей
					 * в качестве артикула товара в интернет-магазине мы не можем,
					 * потому что этот артикул уже занят в интернет-магазине.
					 *
					 * Поэтому пытаемся в качестве артикула товара в магазине
					 * использовать идентификатор товара в 1С:Управление торговлей.
					 */
					/** @var string $externalId */
					$externalId = $this->getEntityOffer()->getExternalId();
					/** @var string $skuCandidate */
					$skuCandidate = df_sku_adapt($externalId);
					/** @var string|null $existingProductId */
					$existingProductId = df_h()->catalog()->product()->getIdBySku($skuCandidate);
					if (is_null($existingProductId)) {
						$result = $skuCandidate;
					}
					else {
						/**
						 * Наличие в интернет-магазине товара,
						 * который использует идентификатор импортируемого сейчас товара
						 * в качестве своего артикула явно говорит о некорректном состоянии программы.
						 * Идентификатор слишком длинен, чтобы случайно повторяться!
						 */
						/** @var Df_Catalog_Model_Product $existingProduct */
						$existingProduct = df_product($existingProductId);
						df_error(
							'1С:Управление торговлей пытается передать в интернет-магазин товар «%s» '
							.'и интернет-магазин намерен присвоить ему артикул «%s», '
							.'являющийся идентификатором данного товара в 1С:Управление торговлей,'
							.'однако в интернет-магазине уже присутствует товар %s,'
							.'который уже использует данный идентификатор в качестве своего артикула.'
							,$this->getEntityOffer()->getName()
							,$skuCandidate
							,$existingProduct->getTitle()
						);
					}
				}
			}
			df_result_sku($result);
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @used-by Df_1C_Cml2_Action_Catalog_Import::importProductsSimple()
	 * @static
	 * @param Df_1C_Cml2_Import_Data_Entity_Offer $offer
	 * @return Df_1C_Cml2_Import_Processor_Product_Type_Simple
	 */
	public static function i(Df_1C_Cml2_Import_Data_Entity_Offer $offer) {
		return self::ic(__CLASS__, $offer);
	}
}