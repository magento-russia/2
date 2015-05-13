<?php
class Df_PageCache_Model_Config_Backend_Cron extends Df_Admin_Model_Config_Backend_Cron {
	/**
	 * @override
	 * @return string
	 */
	protected function getCronJobName() {return 'df_page_cache_crawler';}

	/**
	 * @override
	 * @return string
	 */
	protected function getFrequencyConfigFieldName() {return 'auto_crawling__frequency';}

	/**
	 * @override
	 * @return string
	 */
	protected function getFrequencyConfigGroupName() {return 'page_cache';}

	/**
	 * @override
	 * @return string
	 */
	protected function getTimeConfigFieldName() {return 'auto_crawling__time';}

	/**
	 * @override
	 * @return string
	 */
	protected function getTimeConfigGroupName() {return 'page_cache';}
}