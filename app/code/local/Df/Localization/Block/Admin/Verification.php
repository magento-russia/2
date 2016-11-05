<?php
class Df_Localization_Block_Admin_Verification extends Df_Core_Block_Admin {
	/**
	 * @override
	 * @return string
	 */
	public function getDetailsAsJson() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_json_encode($this->getDetails());
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Localization_Translation_File_Collection */
	public function getFiles() {return $this->getReport()->getFiles();}

	/** @return string */
	public function getTitle() {return 'Проверка качества перевода';}

	/**
	 * @param Df_Localization_Translation_File $file
	 * @return string
	 */
	public function renderFile(Df_Localization_Translation_File $file) {
		return Df_Localization_Block_Admin_Verification_File::r($file);
	}

	/**
	 * @override
	 * @see Df_Core_Block_Template::defaultTemplate()
	 * @used-by Df_Core_Block_Template::getTemplate()
	 * @return string
	 */
	protected function defaultTemplate() {return 'df/localization/verification.phtml';}

	/** @return array(string => array(string => string[])) */
	private function getDetails() {
		if (!isset($this->{__METHOD__})) {
			/** @var array $result */
			$result = [];
			foreach ($this->getFiles() as $file)  {
				/** @var Df_Localization_Translation_File $file */
				$result[$file->getName()]=
					array(
						/**
						 * Эти ключи дальше используются только в JavaScript,
						 * поэтому не заводим для них константы
						 */
						'absentEntries' => $file->getAbsentEntries()
						,'untranslatedEntries' => $file->getUntranslatedEntries()
					)
				;
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Localization_Report_Verification */
	private function getReport() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Localization_Report_Verification::i();
		}
		return $this->{__METHOD__};
	}


}