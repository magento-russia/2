<?php
class Df_Checkout_Block_Cart_Sidebar extends Mage_Checkout_Block_Cart_Sidebar {
	/**
	 * 2015-03-12
	 * Обратите внимание, что родительский метод @uses Mage_Checkout_Block_Cart_Sidebar::getCacheKeyInfo()
	 * появился только в Magento CE 1.6.1.0.
	 * Но мы на него особо и не надеемся, потому что при наличии в корзине посетителя товаров
	 * он всё равно, похоже, кэширует некорректно (не учитывает содержимое корзины).
	 * Этот дефект родительского класса никак не проявляется в Magento CE
	 * в силу действия другого дефекта: родительский класс, несмотря на реализацию метода
	 * @uses Mage_Checkout_Block_Cart_Sidebar::getCacheKeyInfo()
	 * вообще не кэшируется, потому что он не задаёт значение параметра «cache_lifetime»:
	 * смотрите коммертарий к методу @see _construct().
	 * Учитывая, что мы устранили один дефект (включили возможность кэширования),
	 * мы должны устранить и другой — должны правильно рассчитывать ключ для кэширования
	 * (учитывать наличие в корзине товаров).
	 * @override
	 * @see Mage_Checkout_Block_Cart_Sidebar::getCacheKeyInfo()
	 * @used-by Df_Core_Block_Abstract::getCacheKey()
	 * @return string[]
	 */
	public function getCacheKeyInfo() {
		/** @var string[] $result */
		$result = parent::getCacheKeyInfo();
		/**
		 * 2015-08-08
		 * У предзаказа может быть идентификатор, но не быть товаров.
		 * Я увидел такую ситуацию, когда тестировал авторизованным покупателем:
		 * сначала добавил товар в корзину, а потом удалил.
		 * Так вот, если у предзаказа нет товаров,
		 * то не нужно тратить ресурсы на вызов @uses getAdditionalKeys()
		 */
		if (df_quote()->getId() && df_quote()->getItemsCount()) {
			$result = array_merge($result, $this->getAdditionalKeys());
		}
		return $result;
	}

	/** @return string[] */
	private function getAdditionalKeys() {
		/** @var string[] $result */
		/**
		 * 2015-08-08
		 * Обратите внимание, что здесь происходит расчёт налогов и прочих вещей
		 * (похоже, что также скидок и доставки).
		 * Поэтому этот метод является ресурсоёмким,
		 * однако, как я понимаю, этих вычислений всё равно нам не избежать:
		 * ведь если отключить кэширование блока,
		 * то блоку всё равно придётся выполнять все эти расчёты.
		 * Тут вопрос лишь в том, насколько вообще полезно тогда кэширование этого блока,
		 * если для расчёта ключа кэширования приходится выполнять почти все те же вычисления,
		 * что и при рисовании блока.
		 */
		$result = array($this->getSummaryCount(), $this->getSubtotal());
		foreach ($this->getRecentItems() as $quoteItem) {
			/** @var Mage_Sales_Model_Quote_Item $quoteItem */
			$result[]= $quoteItem->getProductId();
			$result[]= $quoteItem->getQty();
		}
		return $result;
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		if (
				df_module_enabled(Df_Core_Module::SPEED)
			&&
				df_cfg()->speed()->blockCaching()->checkoutCartSidebar()
		) {
			/**
			 * Чтобы блок кэшировался стандартным, заложенным в @see Mage_Core_Block_Abstract способом,
			 * продолжительность хранения кэша надо указывать обязательно,
			 * потому что значением продолжительности по умолчанию является «null»,
			 * что в контексте @see Mage_Core_Block_Abstract
			 * (и в полную противоположность Zend Framework
			 * и всем остальным частям Magento, где используется кэширование)
			 * означает, что блок не будет кэшироваться вовсе!
			 * @used-by Mage_Core_Block_Abstract::_loadCache()
			 */
			$this->setData('cache_lifetime', Df_Core_Block_Template::CACHE_LIFETIME_STANDARD);
		}
	}
}