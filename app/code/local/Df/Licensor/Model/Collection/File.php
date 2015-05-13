<?php
class Df_Licensor_Model_Collection_File extends Df_Varien_Data_Collection {
	/** @return Df_Licensor_Model_Collection_File */
	public function loadAll() {
		$this->clear();
		$this->_setIsLoaded(true);
		if (Mage::isInstalled()) {
			if (!is_dir($this->getLicensesFolder())) {
				rm_session()->addError(
					"В системе должна присутствовать папка с лицензиями: app/etc/licenses."
					."\r\nЧитайте внимательно <a href='http://magento-forum.ru/topic/783/'>инструкцию</a>."
				);
			}
			foreach (Df_Spl_Iterator_FilesByExtension::i($this->getLicensesFolder(), 'xml') as $file) {
				/** @var DirectoryIterator $file */
				/** @var Df_Licensor_Model_File $licensorFile */
				$licensorFile = Df_Licensor_Model_File::i($file->getPathname());
				if ($licensorFile->isForbidden()) {
					df_notify_me('Пиратская лицензия', $doLog = false);
					df()->forbid();
				}
				else {
					$this->addItem($licensorFile);
				}
			}
			if (0 === count($this)) {
				rm_session()->addError(
					"Положите лицензию в папку app/etc/licenses."
					."\r\nЧитайте внимательно <a href='http://magento-forum.ru/topic/783/'>"
					."инструкцию</a>."
				);
			}
		}
		return $this;
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getItemClass() {return Df_Licensor_Model_File::_CLASS;}

	/** @return string */
	private function getLicensesFolder() {return BP . DS . self::LICENSES_FOLDER;}

	const _CLASS = __CLASS__;
	const LICENSES_FOLDER = 'app/etc/licenses';

	/** @return Df_Licensor_Model_Collection_File */
	public static function i() {return new self;}
}