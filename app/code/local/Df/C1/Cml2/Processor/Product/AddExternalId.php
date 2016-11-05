<?php
namespace Df\C1\Cml2\Processor\Product;
use Df_Catalog_Model_Product as P;
class AddExternalId extends \Df_Core_Model {
	/** @return string */
	private function getExternalId() {return $this->cfg(self::$P__EXTERNAL_ID);}

	/** @return P */
	private function getProduct() {return $this->cfg(self::$P__PRODUCT);}

	/** @return void */
	private function process() {
		/** @var P $product */
		$product = $this->getProduct();
		if (!$product->get1CId()) {
			// Данный товар не был импортирован из 1С:Управление торговлей,
			// а был создан администратором магазина вручную.
			// Назначаем этому товару внешний идентификатор.
			df_c1_log(
				"У товара %s отсутствует внешний идентификатор.\nНазначаем идентификатор «%s»."
				,$product->getTitle(), $this->getExternalId()
			);
			// Добавляем к прикладному типу товаров
			// свойство для учёта внешнего идентификатора товара в 1С:Управление торговлей
			df_c1_add_external_id_attribute_to_set($product->getAttributeSet());
			$product->saveAttributes(
				array(\Df\C1\C::ENTITY_EXTERNAL_ID => $this->getExternalId())
				// Единое значение для всех витрин
				,$storeId = null
			);
			$product->set1CId($this->getExternalId());
			/** @var P $testProduct */
			$testProduct = df_product($product->getId());
			if ($this->getExternalId() !== $testProduct->get1CId()) {
				df_error('Не удалось добавить внешний идентификатор к товару %s.', $product->getTitle());
			}
			else {
				df_c1_log('Товару %s назначен внешний идентификатор.', $product->getTitle());
			}
		}
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::$P__EXTERNAL_ID, DF_V_STRING_NE)
			->_prop(self::$P__PRODUCT, P::class)
		;                              
	}
	/** @var string */
	private static $P__EXTERNAL_ID = 'external_id';
	/** @var string */
	private static $P__PRODUCT = 'product';
	/**
	 * @used-by \Df\C1\Cml2\Export\Processor\Catalog\Product::getExternalId()
	 * @used-by \Df\C1\Cml2\Import\Data\Entity\Order\Item::getProduct()
	 * @param P $product
	 * @param string $externalId
	 * @return void
	 */
	public static function p(P $product, $externalId) {
		(new self([self::$P__PRODUCT => $product, self::$P__EXTERNAL_ID => $externalId]))->process();
	}
}