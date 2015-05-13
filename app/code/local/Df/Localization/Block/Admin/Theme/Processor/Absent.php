<?php
class Df_Localization_Block_Admin_Theme_Processor_Absent
	extends Df_Localization_Block_Admin_Theme_Processor {
	/**
	 * @override
	 * @return string
	 */
	public function getActionTitle() {return 'тема отсутствует';}

	/**
	 * @override
	 * @return string
	 */
	public function getLink() {return '';}

	const _CLASS = __CLASS__;
}