<?php
namespace Df\C1\Cml2\Import\Processor\Product\Part;
class Images extends \Df\C1\Cml2\Import\Processor\Product {
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
			\Df_Dataflow_Model_Importer_Product_Images::i(
				$this->getExistingMagentoProduct()
				, $this->getEntityProduct()->getImages()->getFullPaths()
				, df_c1()
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
	 * @used-by \Df\C1\Cml2\Action\Catalog\Import::importProductsConfigurablePartImages()
	 * @used-by \Df\C1\Cml2\Action\Catalog\Import::importProductsSimplePartImages()
	 * @static
	 * @param \Df\C1\Cml2\Import\Data\Entity\Offer $offer
	 * @return \Df\C1\Cml2\Import\Processor\Product\Part\Images
	 */
	public static function i(\Df\C1\Cml2\Import\Data\Entity\Offer $offer) {
		return self::ic(__CLASS__, $offer);
	}
}