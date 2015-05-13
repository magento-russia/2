<?php
class Df_Core_Block_Element_Input_Hidden extends Df_Core_Block_Element_Input {
	/**
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Core_Block_Element_Input_Hidden
	 */
	public static function i($parameters) {return df_block(__CLASS__, null, $parameters);}
}