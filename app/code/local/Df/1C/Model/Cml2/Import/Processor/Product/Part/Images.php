<?php
class Df_1C_Model_Cml2_Import_Processor_Product_Part_Images
	extends Df_1C_Model_Cml2_Import_Processor_Product {
	/**
	 * @override
	 * @return void
	 */
	public function process() {
		if (is_null($this->getExistingMagentoProduct())) {
			df_error(
				'Попытка импорта картинок для отсутствующего в системе товара «%s»'
				,$this->getEntityOffer()->getExternalId()
			);
		}
		/**
		 * Если нет картинок для импорта, то текущие картинки не удаляем,
		 * потому что отсутствие картинок для импорта может означать,
		 * что все картинки были импортированы при прошлом сеансе обмена,
		 * и их файлы уже удалены из загрузочной папки.
		 */
		if (0 < $this->getEntityProduct()->getImages()->count()) {
			Df_Dataflow_Model_Importer_Product_Images::i(
				$this->getExistingMagentoProduct()
				, $this->getEntityProduct()->getImages()->getFullPaths()
				, df_h()->_1c()
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

	const _CLASS = __CLASS__;
	/**
	 * @static
	 * @param Df_1C_Model_Cml2_Import_Data_Entity_Offer $offer
	 * @return Df_1C_Model_Cml2_Import_Processor_Product_Part_Images
	 */
	public static function i(Df_1C_Model_Cml2_Import_Data_Entity_Offer $offer) {
		return new self(array(self::P__ENTITY => $offer));
	}
}