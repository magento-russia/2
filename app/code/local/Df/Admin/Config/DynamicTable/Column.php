<?php
/**
 * 2015-02-13
 * Системные пользователи:
 * @used-by Df_Admin_Block_Field_DynamicTable::addColumn()
 * @used-by Df_Admin_Block_Field_DynamicTable::_renderCellTemplate()
 * Прикладные пользователи:
 * @used-by \Df\C1\Config\Block\MapFromCustomerGroupToPriceType::_construct()
 * @used-by \Df\C1\Config\Block\NonStandardCurrencyCodes::_construct()
 * @used-by Df_Directory_Block_Field_CountriesOrdered::_construct()
 */
abstract class Df_Admin_Config_DynamicTable_Column extends Df_Core_Model {
	/** @return string */
	abstract protected function getRendererClass();

	/**
	 * @used-by Df_Admin_Block_Column::getHtmlAttributes()
	 * @return array(string => string)
	 */
	public function getHtmlAttributes() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				array('name' => $this->getName()) + $this->cfg(self::$P__HTML_ATTRIBUTES, array())
			;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @used-by Df_Admin_Block_Column::getInputName()
	 * @return mixed
	 */
	public function getName() {return $this->cfg(self::$P__NAME);}

	/**
	 * @used-by df/admin/column/select.phtml
	 * @return array(string => mixed)
	 */
	public function getRenderOptions() {return $this->cfg(self::$P__RENDER_OPTIONS, array());}

	/**
	 * @used-by Df_Admin_Block_Field_DynamicTable::_renderCellTemplate()
	 * @param Varien_Data_Form_Element_Abstract $element
	 * @return string
	 */
	public function renderTemplate(Varien_Data_Form_Element_Abstract $element) {
		return df_ejs(Df_Admin_Block_Column::render($this->getRendererClass(), $this, $element));
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		/**
		 * 2015-02-17
		 * Обратите внимание, что у объектов данного класса
		 * свойство «label» обязательно должно быть доступно
		 * в качестве ключа массива @see _data,
		 * потому что оно используется в шаблоне
		 * @used-by adminhtml/default/default/template/system/config/form/field/array.phtml
		 * посредством синтаксиса $column['label']
		 * Читайте комментарий к методу
		 * @see Df_Admin_Block_Field_DynamicTable::addColumnRm()
		 */
		$this
			->_prop(self::$P__HTML_ATTRIBUTES, DF_V_ARRAY, false)
			->_prop(self::$P__LABEL, DF_V_STRING_NE)
			->_prop(self::$P__NAME, DF_V_STRING_NE)
			->_prop(self::$P__RENDER_OPTIONS, DF_V_ARRAY, false)
		;
	}
	/** @used-by Df_Admin_Block_Column::_construct() */

	/** @var string */
	protected static $P__HTML_ATTRIBUTES = 'html_attributes';
	/** @var string */
	protected static $P__LABEL = 'label';
	/** @var string */
	protected static $P__NAME = 'string';
	/** @var string */
	protected static $P__RENDER_OPTIONS = 'render_options';
}