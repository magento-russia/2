<?php
class Df_Page_Block_Html_Header extends Mage_Page_Block_Html_Header {
	/**
	 * Цель перекрытия —
	 * предоставление администратору возможности скрывать сообщение «Добро пожаловать»
	 * либо показывать там только имя посетителя без фамилии.
	 *
	 * 2015-11-16
	 * Начиная с Magento CE 1.7.0.2
	 * метод @uses Mage_Page_Block_Html_Header::getWelcome() стал устаревшим:
	 * https://github.com/OpenMage/magento-mirror/blob/1.9.2.2/app/code/core/Mage/Page/Block/Html/Header.php#L85
	 * и уже редко используется современными оформительскими темами.
	 * Для современных версий Magento CE
	 * аналогичное перекрытие делает мой класс @see Df_Page_Block_Html_Welcome
	 *
	 * @override
	 * @return string
	 */
	public function getWelcome() {
		/** @var string|null $result */
		$result = Df_Page_Block_Html_Welcome::welcome();
		/** @noinspection PhpDeprecationInspection */
		return !is_null($result) ? $result : parent::getWelcome();
	}
}