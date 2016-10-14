<?php
class Df_Localization_Block_Admin_Verification_File extends Df_Core_Block_Admin {
	/** @return string */
	public function getName() {return $this->getFile()->getName();}

	/** @return int */
	public function getNumAbsentEntries() {return $this->getFile()->getNumAbsentEntries();}

	/** @return int */
	public function getNumEntries() {return $this->getFile()->getNumEntries();}

	/** @return int */
	public function getNumUntranslatedEntries() {return $this->getFile()->getNumUntranslatedEntries();}

	/** @return bool */
	public function isFullyTranslated() {return $this->getFile()->isFullyTranslated();}

	/**
	 * @override
	 * @see Df_Core_Block_Template::defaultTemplate()
	 * @used-by Df_Core_Block_Template::getTemplate()
	 * @return string
	 */
	protected function defaultTemplate() {return 'df/localization/verification/file.phtml';}

	/** @return Df_Localization_Translation_File */
	private function getFile() {return $this[self::$P__FILE];}

	/** @var string */
	private static $P__FILE = 'file';

	/**
	 * @used-by Df_Localization_Block_Admin_Verification::renderFile()
	 * @param Df_Localization_Translation_File $file
	 * @return Df_Localization_Block_Admin_Verification_File
	 */
	public static function r(Df_Localization_Translation_File $file) {
		return df_render(new self(array(self::$P__FILE => $file)));
	}
}