<?php
class Df_PageCache_Model_Processor_Noroute extends Df_PageCache_Model_Processor_Default
{
	/**
	 * Page id for 404 page
	 */
	const NOT_FOUND_PAGE_ID = 'not_found';

	/**
	 * Returns id for 404 page. This value doesn't change for all 404 pages across the store.
	 *
	 * @param Df_PageCache_Model_Processor $processor
	 * @return string
	 */
	public function getPageIdWithoutApp(Df_PageCache_Model_Processor $processor)
	{
		return self::NOT_FOUND_PAGE_ID;
	}
}
