<?php
class Df_PageCache_Model_Adapter_Factory
{
	/**
	 * Retrieves http curl adapter instance
	 *
	 * @return Varien_Http_Adapter_Curl
	 */
	public function getHttpCurlAdapter()
	{
		return new Varien_Http_Adapter_Curl();
	}
}
