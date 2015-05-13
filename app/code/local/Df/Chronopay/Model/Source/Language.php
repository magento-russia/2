<?php
class Df_Chronopay_Model_Source_Language
{
	/** @return array */
	public function toOptionArray()
	{
		return array(
			array('value' => 'EN', 'label' => df_h()->chronopay()->__('English')),array('value' => 'RU', 'label' => df_h()->chronopay()->__('Russian')),array('value' => 'NL', 'label' => df_h()->chronopay()->__('Dutch')),array('value' => 'DE', 'label' => df_h()->chronopay()->__('German')),);
	}
}