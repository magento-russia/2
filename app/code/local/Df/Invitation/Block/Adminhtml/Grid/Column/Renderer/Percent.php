<?php
class Df_Invitation_Block_Adminhtml_Grid_Column_Renderer_Percent
	extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Number {
	/**
	 * Renders grid column
	 *
	 * @param  Varien_Object $row
	 * @return  string
	 */
	public function render(Varien_Object $row)
	{
		if ($this->getColumn()->getEditable()) {
			return parent::render($row);
		}

		$value = $this->_getValue($row);
		$value = round($value, 2);
		return $value . ' %';
	}

	/** @return Df_Invitation_Block_Adminhtml_Grid_Column_Renderer_Percent */
	public static function i() {return df_block(__CLASS__);}
}