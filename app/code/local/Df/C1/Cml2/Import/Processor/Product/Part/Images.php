<?php
namespace Df\C1\Cml2\Import\Processor\Product\Part;
use Df\C1\Cml2\Import\Data\Entity\Offer;
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
	 * @param Offer $offer
	 * @return self
	 */
	public static function i(Offer $offer) {return self::ic(__CLASS__, $offer);}
}