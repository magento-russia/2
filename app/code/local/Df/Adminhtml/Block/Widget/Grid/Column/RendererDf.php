<?php
class Df_Adminhtml_Block_Widget_Grid_Column_RendererDf extends Df_Core_Block_Admin {
	/** @return Varien_Object */
	protected function getColumn() {return $this->getOriginalRenderer()->getColumn();}

	/**
	 * @param string $paramName
	 * @param mixed $defaultValue
	 * @return mixed
	 */
	protected function getColumnParam($paramName, $defaultValue = null) {
		return df_o($this->getColumn(), $paramName, $defaultValue);
	}

	/** @return Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract */
	protected function getOriginalRenderer() {return $this->_getData(self::P__ORIGINAL_RENDERER);}

	/**
	 * @param string $paramName
	 * @param mixed $defaultValue
	 * @return mixed
	 */
	protected function getRowParam($paramName, $defaultValue = null) {
		return df_o($this->getRow(), $paramName, $defaultValue);
	}

	/** @return Varien_Object */
	protected function getRow() {return $this->cfg(self::P__ROW);}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::P__ROW, 'Varien_Object')
			->_prop(
				self::P__ORIGINAL_RENDERER
				,'Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract'
			)
		;
	}
	const _CLASS = __CLASS__;
	const P__ORIGINAL_RENDERER = 'original_renderer';
	const P__ROW = 'row';
}