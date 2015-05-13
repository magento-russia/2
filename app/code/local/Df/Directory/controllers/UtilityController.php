<?php
class Df_Directory_UtilityController extends Mage_Core_Controller_Front_Action {
	/** @return void */
	public function indexAction() {
		try {
			/** @var string $filePath */
			$filePath = df_concat_path(Mage::getBaseDir('var'), 'log');
			/** @var string $fileContents */
			$fileContents = file_get_contents(df_concat_path($filePath, 'countries.csv'));
			/** @var string[] $fileContentsAsRows */
			$fileContentsAsRows = explode("\r\n", $fileContents);
			/** @var array(string => array(string => string)) $fileContentsAsAssocArray */
			$fileContentsAsAssocArray = array();
			foreach ($fileContentsAsRows as $fileContentsAsRow) {
				/** @var string[] $fileContentsAsRow */
				$rowAsColumns = explode(';', $fileContentsAsRow);
				Mage::log($rowAsColumns);
				/** @var string $countryCode */
				$countryCode = df_a($rowAsColumns, 0);
				df_assert_string_not_empty($countryCode, -1);
				/** @var string $countryNameInNomicalCase */
				$countryNameInNomicalCase = df_a($rowAsColumns, 1);
				df_assert_string_not_empty($countryNameInNomicalCase);
				$countryNameInGenitiveCase = df_a($rowAsColumns, 2);
				/** @var string $countryNameInGenitiveCase */
				df_assert_string($countryNameInGenitiveCase);
				/** @var string $countryNameInCaseDative */
				$countryNameInCaseDative = df_a($rowAsColumns, 3);
				df_assert_string($countryNameInCaseDative);
				$fileContentsAsAssocArray[$countryCode] = array(
					'nominative' => $countryNameInNomicalCase
					,'genitive' => $countryNameInGenitiveCase
					,'dative' => ''
					,'accusative' => $countryNameInCaseDative
				);
			}
			$resultAsJson = json_encode($fileContentsAsAssocArray, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT |JSON_FORCE_OBJECT);
			rm_file_put_contents(df_concat_path($filePath, 'countries.json'), $resultAsJson);
			$this->getResponse()->setBody('OK');
		}
		catch(Exception $e) {
			df_notify_exception($e);
			//df_handle_entry_point_exception($e, false);
			echo rm_ets($e);
		}
	}
}