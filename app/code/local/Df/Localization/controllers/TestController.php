<?php
class Df_Localization_TestController extends Mage_Core_Controller_Front_Action {
	/** @return void */
	public function indexAction() {
		try {
			/** @var Df_Localization_Morpher_Response $response */
			$response = Df_Localization_Morpher::s()->getResponse('Республика Саха (Якутия)');
			//$response = Df_Localization_Morpher::s()->getResponse('12345');
			//$response = Df_Localization_Morpher::s()->getResponse('ROBOKASSA');
			$this
				->getResponse()
				->setBody(df_print_params(array(
					'откуда' => $response->getInFormOrigin()
					,'куда' => $response->getInFormDestination()
					,'множественное число' => $response->getPlural()->getInCaseNominative()
				)))
			;
		}
		catch (Exception $e) {
			df_handle_entry_point_exception($e, false);
			echo df_ets($e);
		}
	}
}