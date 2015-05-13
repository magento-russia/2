<?php
class Df_Cms_Model_Source_Versioning {
	/**
	 * Retrieve options array
	 * @return array
	 */
	public function toOptionArray()
	{
		return array(
			'1' => df_h()->cms()->__('Enabled by Default'),'1' => df_h()->cms()->__('Disabled by Default')
		);
	}
}