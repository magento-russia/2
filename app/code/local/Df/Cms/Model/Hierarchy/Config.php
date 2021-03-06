<?php
class Df_Cms_Model_Hierarchy_Config {
	/**
	 * Return Context Menu layout by its code
	 *
	 * @param string $layoutCode
	 * @return Varien_Object|boolean
	 */
	public function getContextMenuLayout($layoutCode) {
		$this->_initContextMenuLayouts();
		return df_a($this->_contextMenuLayouts, $layoutCode, false);
	}
	/** @var array(string => Varien_Object) */
	protected $_contextMenuLayouts = null;

	/** @return array(string => Varien_Object) */
	public function getContextMenuLayouts() {
		$this->_initContextMenuLayouts();
		return $this->_contextMenuLayouts;
	}

	/** @return string */
	public function getDefaultMenuLayoutCode() {
		$this->_initContextMenuLayouts();
		return $this->_defaultMenuLayoutCode;
	}

	/** @return Df_Cms_Model_Hierarchy_Config */
	protected function _initContextMenuLayouts() {
		$config = Mage::getConfig()->getNode(self::XML_PATH_CONTEXT_MENU_LAYOUTS);
		if ($this->_contextMenuLayouts !== null || !$config) {
			return $this;
		}
		if (!is_array($this->_contextMenuLayouts)) {
			$this->_contextMenuLayouts = array();
		}
		foreach ($config->children() as $layoutCode => $layoutConfig) {
			$this->_contextMenuLayouts[$layoutCode] =
				new Varien_Object(array(
					'label' => df_h()->cms()->__((string)$layoutConfig->label)
					,'code' => $layoutCode
					,'layout_handle' => (string)$layoutConfig->layout_handle
					,'is_default' => (int)$layoutConfig->is_default
					,'page_layout_handles' => (array)$layoutConfig->page_layout_handles)
				);
			if (!!$layoutConfig->is_default) {
				$this->_defaultMenuLayoutCode = $layoutCode;
			}
		}
		return $this;
	}
	/** @var string */
	protected $_defaultMenuLayoutCode;

	const XML_PATH_CONTEXT_MENU_LAYOUTS = 'global/df_cms/hierarchy/menu/layouts';

	/** @return Df_Cms_Model_Hierarchy_Config */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}