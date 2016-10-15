<?php
abstract class Df_Admin_Block_Column extends Df_Core_Block_Admin {
	/**
	 * Этот метод предназначен для перекрытия потомками.
	 * @see Df_Admin_Block_Column_Select::getAdditionalCssClass()
	 * @used-by getHtmlAttributes()
	 * @return string
	 */
	protected function getAdditionalCssClass() {return '';}

	/**
	 * @used-by getAttributes()
	 * @used-by getInputName()
	 * @used-by rm/default/template/df/admin/column/select.phtml
	 * @return Df_Admin_Config_DynamicTable_Column
	 */
	protected function getColumn() {return $this->cfg(self::$P__COLUMN);}

	/**
	 * Этот метод предназначен для перекрытия потомками.
	 * @see Df_Admin_Block_Column_Select::getDefaultRenderOptions()
	 * @used-by getRenderOptions()
	 * @return array(string => mixed)
	 */
	protected function getDefaultRenderOptions() {return array();}

	/** @return Varien_Data_Form_Element_Abstract */
	private function getField() {return $this->cfg(self::$P__FIELD);}

	/** @return array(string => string) */
	protected function getHtmlAttributes() {
		if (!isset($this->{__METHOD__})) {
			/** @var array(string => string) $attributes */
			$attributes = $this->getColumn()->getHtmlAttributes();
			$attributes['class'] = implode(' ', array_filter(array(
				/**
				 * Этот класс затем используется в шаблоне.
				 * @used-by df/admin/column/select.phtml
						var $select = $('.<?php echo $columnName; ?>', $row);
				 */
				$this->getColumn()->getName()
				, dfa($attributes, 'class')
				, $this->getAdditionalCssClass()
			)));
			$this->{__METHOD__} = array('name' => $this->getInputName()) + $attributes;
		}
		return $this->{__METHOD__};
	}

	/** @return array(string => mixed) */
	protected function getRenderOptions() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_extend(
				$this->getDefaultRenderOptions(), $this->getColumn()->getRenderOptions()
			);
		}
		return $this->{__METHOD__};
	}

	/**
	 * @used-by getAttributes()
	 * @return string
	 */
	private function getInputName() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = strtr('{elementName}[#{_id}][{columnName}]', array(
				'{elementName}' => $this->getField()->getName()
				,'{columnName}' => $this->getColumn()->getName()
			));
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::$P__COLUMN, Df_Admin_Config_DynamicTable_Column::class)
			->_prop(self::$P__FIELD, 'Varien_Data_Form_Element_Abstract')
		;
	}
	/**
	 * @used-by Df_Admin_Block_Column_Select::_construct()
	 * @var string
	 */
	protected static $P__COLUMN = 'object';
	/** @var string */
	private static $P__FIELD = 'field';

	/**
	 * @param string $class
	 * @param Df_Admin_Config_DynamicTable_Column $column
	 * @param Varien_Data_Form_Element_Abstract $field
	 * @return string
	 */
	public static function render(
		$class
		, Df_Admin_Config_DynamicTable_Column $column
		, Varien_Data_Form_Element_Abstract $field
	) {
		/** @var Df_Admin_Block_Column $block */
		$block = new $class(array(self::$P__COLUMN => $column, self::$P__FIELD => $field));
		df_assert($block instanceof Df_Admin_Block_Column);
		return df_render($block);
	}
}