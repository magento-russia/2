<?php
class Df_Page_Block_Html_Welcome extends Df_Page_Block_Html_WelcomeM {
	/**
	 * 2015-11-16
	 * Цель перекрытия —
	 * предоставление администратору возможности скрывать сообщение «Добро пожаловать»
	 * либо показывать там только имя посетителя без фамилии.
	 *
	 * Для Magento CE версий ранее 1.7.0.2 для этой же цели используется
	 * класс @see Df_Page_Block_Html_Head,
	 * однако перекрываемый им метод @see Mage_Page_Block_Html_Header::getWelcome()
	 * с Magento CE версии 1.7.0.2 стал устаревшим:
	 * https://github.com/OpenMage/magento-mirror/blob/1.9.2.2/app/code/core/Mage/Page/Block/Html/Header.php#L85
	 * и уже редко используется современными оформительскими темами.
	 *
	 * @override
	 * @see Mage_Page_Block_Html_Welcome::_toHtml()
	 * @return string
	 */
	protected function _toHtml() {
		/** @var string|null $result */
		$result = self::welcome();
		return !is_null($result) ? $result : parent::_toHtml();
	}

	/**
	 * 2015-11-16
	 * @used-by _toHtml()
	 * @used-by Df_Page_Block_Html_Header::getWelcome()
	 * @return string|null
	 */
	public static function welcome() {
		$result = null;
		if (
			// Избегаем зависимости модуля Df_Page от наличия модуля Df_Tweaks
			df_module_enabled(Df_Core_Module::TWEAKS)
			&& df_installed()
			&& rm_session_customer()->isLoggedIn()
		) {
			if (df_cfg()->tweaks()->header()->hideWelcomeFromLoggedIn()) {
				$result = '';
			}
			else {
				if (df_cfg()->tweaks()->header()->showOnlyFirstName()) {
					$result = sprintf(rm_translate('Welcome, %s!', 'Mage_Page'), df_e(
						df_h()->tweaks()->customer()->getFirstNameWithPrefix())
					);
				}
			}
		}
		return $result;
	}
}