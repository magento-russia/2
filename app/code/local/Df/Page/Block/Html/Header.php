<?php
class Df_Page_Block_Html_Header extends Mage_Page_Block_Html_Header {
	/**
	 * Цель перекрытия —
	 * предоставление администратору возможности скрывать сообщение «Добро пожаловать»
	 * либо показывать там только имя посетителя без фамилии.
	 *
	 * @override
	 * @return string
	 */
	public function getWelcome() {
		$result = parent::getWelcome();
		if (
				// Избегаем зависимости модуля Df_Page от наличия модуля Df_Tweaks
				df_module_enabled(Df_Core_Module::TWEAKS)
			&&
				df_installed()
			&&
				df_enabled(Df_Core_Feature::TWEAKS)
			&&
				rm_session_customer()->isLoggedIn()
		) {
			if (df_cfg()->tweaks()->header()->hideWelcomeFromLoggedIn()) {
				$result = '';
			}
			else {
				if (df_cfg()->tweaks()->header()->showOnlyFirstName()) {
					$result =
						$this->__(
							'Welcome, %s!'
							, $this->escapeHtml(
								df_h()->tweaks()->customer()->getFirstNameWithPrefix()
							)
						)
					;
				}
			}
		}
		return $result;
	}
}