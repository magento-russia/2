<?php
class Df_Core_Model_Format_Html_List extends Df_Core_Model {
	/** @return string */
	private function _render() {
		return rm_tag($this->getTag(), $this->getAttributes(), $this->renderItems());
	}

	/** @return array(string => string) */
	private function getAttributes() {return array_filter(array('class' => $this->getCssClassForList()));}

	/** @return array(string => string) */
	private function getAttributesForItem() {
		return array_filter(array('class' => $this->getCssClassForItem()));
	}

	/** @return string|null */
	private function getCssClassForItem() {return $this->cfg(self::$P__CSS_CLASS_FOR_ITEM);}

	/** @return string|null */
	private function getCssClassForList() {return $this->cfg(self::$P__CSS_CLASS_FOR_LIST);}

	/** @return string[] */
	private function getItems() {return $this->cfg(self::$P__ITEMS);}

	/** @return string */
	private function getTag() {return $this->isOrdered() ? 'ol' : 'ul';}

	/** @return bool */
	private function isOrdered() {return $this->cfg(self::$P__IS_ORDERED, false);}

	/**
	 * @param string $item
	 * @return string
	 */
	private function renderItem($item) {return rm_tag('li', $this->getAttributesForItem(), $item);}

	/** @return string */
	private function renderItems() {
		return df_cc_n(array_map(array($this, 'renderItem'), $this->getItems()));
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::$P__CSS_CLASS_FOR_ITEM, RM_V_STRING, false)
			->_prop(self::$P__CSS_CLASS_FOR_LIST, RM_V_STRING, false)
			->_prop(self::$P__IS_ORDERED, RM_V_BOOL, false)
			->_prop(self::$P__ITEMS, RM_V_ARRAY)
		;
	}
	const _C = __CLASS__;
	/** @var string */
	private static $P__CSS_CLASS_FOR_ITEM = 'css_class_for_item';
	/** @var string */
	private static $P__CSS_CLASS_FOR_LIST = 'css_class_for_list';
	/** @var string */
	private static $P__IS_ORDERED = 'is_ordered';
	/** @var string */
	private static $P__ITEMS = 'items';

	/**
	 * @used-by rm_tag_list()
	 * @param string[] $items
	 * @param bool $isOrdered [optional]
	 * @param string|null $cssClassForList [optional]
	 * @param string|null $cssClassForItem [optional]
	 * @return string
	 */
	public static function render(
		array $items, $isOrdered = false, $cssClassForList = null, $cssClassForItem = null
	) {
		/** @var Df_Core_Model_Format_Html_List $i */
		$i = new self(array(
			self::$P__ITEMS => $items
			,self::$P__IS_ORDERED => $isOrdered
			,self::$P__CSS_CLASS_FOR_LIST => $cssClassForList
			,self::$P__CSS_CLASS_FOR_ITEM => $cssClassForItem
		));
		return $i->_render();
	}
}