<?php
class Df_Adminhtml_Block_Widget_Button extends Mage_Adminhtml_Block_Widget_Button {
	const P__CLASS = 'class';
	const P__LABEL = 'label';
	const P__ONCLICK = 'onclick';
	/**
	 * @param string $label
	 * @param string $class
	 * @param string $location
	 * @return Df_Adminhtml_Block_Widget_Button
	 */
	public static function i($label, $class, $location) {
		return df_block(__CLASS__, null, array(
			self::P__LABEL => $label
			, self::P__CLASS => $class
			, self::P__ONCLICK => rm_sprintf('setLocation(%s)', df_quote_single($location))
		));
	}
}