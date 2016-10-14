<?php
class Df_PageCache_Model_Config_Backend_Cron extends Df_Admin_Config_Backend_Cron {
	/**
	 * @override
	 * @return string
	 */
	protected function getCronJobName() {return self::RM__CRON_JOB_NAME;}

	/**
	 * @override
	 * @return string
	 */
	protected function getFrequencyConfigFieldName() {return self::RM__FREQUENCY_CONFIG_FIELD_NAME;}

	/**
	 * @override
	 * @return string
	 */
	protected function getFrequencyConfigGroupName() {return self::RM__FREQUENCY_CONFIG_GROUP_NAME;}

	/**
	 * @override
	 * @return string
	 */
	protected function getTimeConfigFieldName() {return self::RM__TIME_CONFIG_FIELD_NAME;}

	/**
	 * @override
	 * @return string
	 */
	protected function getTimeConfigGroupName() {return self::RM__TIME_CONFIG_GROUP_NAME;}

	const RM__CRON_JOB_NAME = 'df_page_cache_crawler';
	const RM__FREQUENCY_CONFIG_FIELD_NAME = 'auto_crawling__frequency';
	const RM__FREQUENCY_CONFIG_GROUP_NAME = 'page_cache';
	const RM__TIME_CONFIG_FIELD_NAME = 'auto_crawling__time';
	const RM__TIME_CONFIG_GROUP_NAME = 'page_cache';
}