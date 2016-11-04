<?php
namespace Df\C1\Cml2\Import\Processor;
abstract class Product extends \Df\C1\Cml2\Import\Processor {
	/** @return \Df\C1\Cml2\Import\Data\Entity\Offer */
	protected function getEntityOffer() {return $this->getEntity();}
	
	/** @return \Df\C1\Cml2\Import\Data\Entity\Product */
	protected function getEntityProduct() {
		/** @var \Df\C1\Cml2\Import\Data\Entity\Product $result */
		$result = $this->getEntityOffer()->getEntityProduct();
		/**
		 * Инициализируем как следует прикладной тип.
		 * Этот вызов был добавлен в версии 2.16.2
		 * для устранения дефекта отключенности опции used_in_product_listing
		 * для внешнего идентификатора,
		 * что приводило к незагрузке внешнего идентификатора в коллекцию
		 *
		 * После этого код создания внешнего идентификатора был поправлен
		 * used_in_product_listing = 1 в методе
		 * @see df_c1_add_external_id_attribute_to_set(),
		 * а $result->getAttributeSet() гарантирует его вызов.
		 */
		$result->getAttributeSet();
		return $result;
	}	

	/** @return \Df_Catalog_Model_Product|null */
	protected function getExistingMagentoProduct() {
		// Грязный хак.
		// Причём кэшировать результат метода нельзя :(
		// Надо переделать этот метод.
		$this->getEntityProduct();
		return df()->registry()->products()->findByExternalId($this->getEntityOffer()->getExternalId());
	}
	
	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__ENTITY, \Df\C1\Cml2\Import\Data\Entity\Offer::class);
	}

	/**
	 * @used-by \Df\C1\Cml2\Import\Processor\Product\Part\Images::i()
	 * @used-by \Df\C1\Cml2\Import\Processor\Product\Type\Configurable::i()
	 * @used-by \Df\C1\Cml2\Import\Processor\Product\Type\Simple::i()
	 * @used-by \Df\C1\Cml2\Import\Processor\Product\Type\Configurable::p()
	 * @used-by \Df\C1\Cml2\Import\Processor\Product\Type\Configurable\Child::p()
	 * @used-by \Df\C1\Cml2\Import\Processor\Product\Type\Configurable\NewT::p_new()
	 * @used-by \Df\C1\Cml2\Import\Processor\Product\Type\Configurable\Update::p_update()
	 * @param string $class
	 * @param \Df\C1\Cml2\Import\Data\Entity\Offer $offer
	 * @return \Df\C1\Cml2\Import\Processor\Product
	 */
	protected static function ic($class, \Df\C1\Cml2\Import\Data\Entity\Offer $offer) {
		return df_ic($class, __CLASS__, array(self::$P__ENTITY => $offer));
	}
}