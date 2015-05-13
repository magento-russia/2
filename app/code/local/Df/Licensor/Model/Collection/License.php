<?php
/**
 * Перечень всех установленных в системе лицензий
 */
class Df_Licensor_Model_Collection_License extends Df_Varien_Data_Collection {
	/** @return Df_Licensor_Model_Collection_License */
	public function loadAll() {
		$this->clear();
		$this->_setIsLoaded(true);
		foreach ($this->getFiles() as $file) {
			/** @var Df_Licensor_Model_File $file */
			/** @var Df_Licensor_Model_License $license */
			$license = $file->getLicense();
			if (!$license->validateSignature()) {
				Mage::log(rm_sprintf('Лицензия «%s» подписана фальшиво.', basename($file->getName())));
			}
			else if ($license->validateDate()) {
				$this->addItem($license);
			}
		}
		return $this;
	}

	/** @return Df_Licensor_Model_Collection_File */
	private function getFiles() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Licensor_Model_Collection_File::i();
			$this->{__METHOD__}->loadAll();
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getItemClass() {return Df_Licensor_Model_License::_CLASS;}

	const _CLASS = __CLASS__;
	/**
	 * @static
	 * @return Df_Licensor_Model_Collection_License
	 */
	public static function s() {
		/** @var Df_Licensor_Model_Collection_License $result */
		static $result;
		if (!isset($result)) {
			$result = new Df_Licensor_Model_Collection_License();
			$result->loadAll();
		}
		return $result;
	}
}