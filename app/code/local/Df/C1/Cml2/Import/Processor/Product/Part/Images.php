<?php
class Df_C1_Cml2_Import_Processor_Product_Part_Images
	extends Df_C1_Cml2_Import_Processor_Product {
	/**
	 * @override
	 * @return void
	 */
	public function process() {
		if (is_null($this->getExistingMagentoProduct())) {
			df_error(
				'Попытка импорта картинок для отсутствующего в системе товара «%s».'
				,$this->getEntityOffer()->getExternalId()
			);
		}
		/**
		 * Если нет картинок для импорта, то текущие картинки не удаляем,
		 * потому что отсутствие картинок для импорта может означать,
		 * что все картинки были импортированы при прошлом сеансе обмена,
		 * и их файлы уже удалены из загрузочной папки.
		 */
		if ($this->getEntityProduct()->getImages()->hasItems()) {
			Df_Dataflow_Model_Importer_Product_Images::i(
				$this->getExistingMagentoProduct()
				, $this->getEntityProduct()->getImages()->getFullPaths()
				, df_1c()
			)->process();
			/**
			 * Нет необходимости импортировать картинки при каждом сеансе обмена.
			 * Мы можем явно удалять файлы картинок сразу после их импорта,
			 * и при последующих сеансах импорта это будет говорить нам, что картинки уже импортированы
			 * и что их не надо импортировать повторно!
			 */
			$this->getEntityProduct()->getImages()->deleteFiles();
		}
	}

	/**
	 * @used-by Df_C1_Cml2_Action_Catalog_Import::importProductsConfigurablePartImages()
	 * @used-by Df_C1_Cml2_Action_Catalog_Import::importProductsSimplePartImages()
	 * @static
	 * @param Df_C1_Cml2_Import_Data_Entity_Offer $offer
	 * @return Df_C1_Cml2_Import_Processor_Product_Part_Images
	 */
	public static function i(Df_C1_Cml2_Import_Data_Entity_Offer $offer) {
		return self::ic(__CLASS__, $offer);
	}
}