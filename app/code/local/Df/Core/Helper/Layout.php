<?php
class Df_Core_Helper_Layout extends Mage_Core_Helper_Abstract {
	/**
	 * Блок «head» может отсутствовать на некоторых страницах 
	 * (например, при импорте товаров).
	 * @return Mage_Page_Block_Html_Head|null
	 */
	public function getBlockHead() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = rm_n_set(rm_empty_to_null(rm_layout()->getBlock('head')));
		}
		return rm_n_get($this->{__METHOD__});
	}
	
	/**
	 * @param string|Mage_Core_Block_Abstract $block
	 * @return Df_Core_Helper_Layout
	 */
	public function removeBlock($block) {
		/**
		 * Что интересно, мы можем эффективно (быстро) удалять блоки и по их типу.
		 *
		 * Layout, при создании блока, устанавливает блоку его тип в формате Magento:
		 * $block->setType($type);
		 *
		 * Поэтому, по событию controller_action_layout_generate_blocks_after
		 * мы можем сформировать единократно карту тип блока => блок,
		 * и затем быстро находить блоки по их типу.
		 */
		if (is_string($block)) {
			$block = rm_layout()->getBlock($block);
		}
		if ($block instanceof Mage_Core_Block_Abstract) {
			rm_layout()->unsetBlock($block->getNameInLayout());
			/** @var Mage_Core_Block_Abstract|null $parent */
			$parent = $block->getParentBlock();
			/**
			 * Как ни странно — родительского блока может не быть.
			 * Такое происходит, когда в файлах layout уже вызвали unsetChild.
			 * Непонятно, почему после unset блок всё-таки доходит сюда.
			 */
			if ($parent) {
				$parent->unsetChild($block->getBlockAlias());
				/**
				 * Заплатка для Magento CE 1.6.2.0 и более ранних версий
				 * Метод unsetChild в этих версиях дефектен
				 */
				/** @var bool $le_1_6_0_2 */
				static $le_1_6_0_2;
				if (!isset($le_1_6_0_2)) {
					$le_1_6_0_2 = df_magento_version('1.6.2.0', '<=');
				}
				if ($le_1_6_0_2) {
					$parent->unsetChild($block->getNameInLayout());
				}
				/**
				 * После unsetChild надо обязательно вызывать sortChildren,
				 * иначе блоки дублируются: http://magento-forum.ru/topic/1491/
				 *
				 * Дублирование происходит, потому что unsetChild приводит к вызову PHP unset,
				 * а unset не обновляет натуральные индексы массива:
				 * 'It should be noted that unset() will keep indexes untouched,
				 * which is what you'd expect when using string indexes (array as hashtable),
				 * but can be quite surprising when dealing with integer indexed arrays:'
				 *
				 * Это приводить потом к неправильному поведению sortChildren
				 * (и array_splice внутри него)
				 * при отображении страницы
				 */
				/**
				 * НАЧАЛО НОВОГО АЛГОРИТМА
				 * 2014-12-08
				 * Оказалось, что прежний алгоритм не всегда решает проблему задваивания блока
				 * при использовании некоторых тем:
				 * @link http://magento-forum.ru/topic/2617/
				 * @link http://magento-forum.ru/topic/3374/
				 * @link http://magento-forum.ru/topic/3586/
				 * @link http://magento-forum.ru/topic/3861/
				 * @link http://magento-forum.ru/topic/4142/
				 * Поэтому добавил к прежнему алгоритму дополнение.
				 * Теперь вроде всё правильно работает.
				 */
				/** @var array(string => Mage_Core_Block_Abstract) $orderedMapFromNamesToBlocks */
				$orderedMapFromNamesToBlocks = $parent->getSortedChildBlocks();
				$parent->unsetChildren();
				foreach ($orderedMapFromNamesToBlocks as $childName => $child) {
					/** @var string $childName */
					/** @var Mage_Core_Block_Abstract $child */
					/** @var string $alias */
					/**
					 * 2015-03-18
					 * В очень коряво разработанном магазине autosp.kz
					 * в качестве $child попало значение типа «boolean».
					 */
					if (is_object($child)) {
						$alias = $child->getBlockAlias();
						if (!$alias) {
							$alias = $childName;
						}
						$parent->append($child, $alias);
					}
				}
				/**
				 * КОНЕЦ НОВОГО АЛГОРИТМА
				 */
				/**
				 * На всякий случай оставляем и вызов @see Mage_Core_Block_Abstract::sortChildren
				 */
				if (
					/**
					 * К сожалению, нельзя здесь для проверки публичности метода
					 * использовать is_callable,
					 * потому что наличие Varien_Object::__call
					 * приводит к тому, что is_callable всегда возвращает true.
					 * В Magento 1.4 - 1.5
					 * метод @see Mage_Core_Block_Abstract::sortChildren отсутствует
					 */
					method_exists($parent, 'sortChildren')
				) {
					call_user_func(array($parent, 'sortChildren'));
				}
			}
		}
		return $this;
	}

	/** @return Df_Core_Helper_Layout */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}