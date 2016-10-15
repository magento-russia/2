<?php
class Df_Localization_Onetime_Processor_Filesystem extends Df_Core_Model {
	/** @return void */
	public function process() {
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
			df_path()->createAndMakeWritable($this->getOperation()->getDestinationFull());
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

	/** @return Df_Localization_Onetime_Dictionary_Filesystem_Operation */
	private function getOperation() {return $this->cfg(self::$P__OPERATION);}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__OPERATION, Df_Localization_Onetime_Dictionary_Filesystem_Operation::class);
	}
	/** @var string */
	protected static $P__OPERATION = 'operation';

	/**
	 * @param Df_Localization_Onetime_Dictionary_Filesystem_Operation $operation
	 * @return Df_Localization_Onetime_Processor_Filesystem
	 */
	public static function i(
		Df_Localization_Onetime_Dictionary_Filesystem_Operation $operation
	) {
		return new self(array(self::$P__OPERATION => $operation));
	}
}