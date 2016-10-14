<?php
class Df_Directory_UtilityController extends Mage_Core_Controller_Front_Action {
	/** @return void */
	public function indexAction() {
		try {
			/** @var string $filePath */
			$filePath = df_cc_path(Mage::getBaseDir('var'), 'log');
			/** @var string $fileContents */
			$fileContents = file_get_contents(df_cc_path($filePath, 'countries.csv'));
			/** @var string[] $fileContentsAsRows */
			$fileContentsAsRows = df_explode_n($fileContents);
			/** @var array(string => array(string => string)) $fileContentsAsAssocArray */
			$fileContentsAsAssocArray = array();
			foreach ($fileContentsAsRows as $fileContentsAsRow) {
				/** @var string[] $fileContentsAsRow */
				$rowAsColumns = explode(';', $fileContentsAsRow);
				/** @var string $countryCode */
				$countryCode = dfa($rowAsColumns, 0);
				df_assert_string_not_empty($countryCode, -1);
				/** @var string $countryNameInNomicalCase */
				$countryNameInNomicalCase = dfa($rowAsColumns, 1);
				df_assert_string_not_empty($countryNameInNomicalCase);
				$countryNameInGenitiveCase = dfa($rowAsColumns, 2);
				/** @var string $countryNameInGenitiveCase */
				df_assert_string($countryNameInGenitiveCase);
				/** @var string $countryNameInCaseDative */
				$countryNameInCaseDative = dfa($rowAsColumns, 3);
				df_assert_string($countryNameInCaseDative);
				$fileContentsAsAssocArray[$countryCode] = array(
					'nominative' => $countryNameInNomicalCase
					,'genitive' => $countryNameInGenitiveCase
					,'dative' => ''
					,'accusative' => $countryNameInCaseDative
				);
			}
			$resultAsJson = json_encode($fileContentsAsAssocArray, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT |JSON_FORCE_OBJECT);
			df_file_put_contents(df_cc_path($filePath, 'countries.json'), $resultAsJson);
			$this->getResponse()->setBody('OK');
		}
		catch (Exception $e) {
			df_notify_exception($e);
			//df_handle_entry_point_exception($e, false);
			echo df_ets($e);
		}
	}
}