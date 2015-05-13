<?php
class Df_Localization_Model_Onetime_Processor_Filesystem extends Df_Core_Model_Abstract {
	/** @return void */
	public function process() {
		try {
			/**
			 * Обратите внимание, что мы не проверяем,
			 * существует ли уже файл destination,
			 * потому что мы всё равно не знаем,
			 * является ли этот файл перезаписанным нами ранее,
			 * либо тем, который только предстоит перезаписать.
			 */
			if (
					$this->getOperation()->getDestinationFull()
				&&
					$this->getOperation()->getSourceFull()
				&&
					is_file($this->getOperation()->getSourceFull())
			) {
				df_path()->prepareForWriting($this->getOperation()->getDestinationDir());
				if ($this->getOperation()->isItMove()) {
					rename(
						$this->getOperation()->getSourceFull()
						, $this->getOperation()->getDestinationFull()
					);
				}
				else {
					copy(
						$this->getOperation()->getSourceFull()
						, $this->getOperation()->getDestinationFull()
					);
				}
			}
		}
		catch (Exception $e) {
			df_handle_entry_point_exception($e, false);
		}
	}

	/** @return Df_Localization_Model_Onetime_Dictionary_Filesystem_Operation */
	private function getOperation() {return $this->cfg(self::$P__OPERATION);}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(
			self::$P__OPERATION
			, Df_Localization_Model_Onetime_Dictionary_Filesystem_Operation::_CLASS
		);
	}
	/** @var string */
	protected static $P__OPERATION = 'operation';

	/**
	 * @param Df_Localization_Model_Onetime_Dictionary_Filesystem_Operation $operation
	 * @return Df_Localization_Model_Onetime_Processor_Filesystem
	 */
	public static function i(
		Df_Localization_Model_Onetime_Dictionary_Filesystem_Operation $operation
	) {
		return new self(array(self::$P__OPERATION => $operation));
	}
}