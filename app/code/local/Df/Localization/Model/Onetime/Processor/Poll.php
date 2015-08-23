<?php
/**
 * @method Df_Poll_Model_Poll getEntity()
 */
class Df_Localization_Model_Onetime_Processor_Poll
	extends Df_Localization_Model_Onetime_Processor_Entity {
	/**
	 * @override
	 * @return string
	 */
	protected function getTitlePropertyName() {return 'poll_title';}

	/**
	 * @override
	 * @param Df_Localization_Model_Onetime_Dictionary_Term $term
	 * @return void
	 */
	protected function processTerm(Df_Localization_Model_Onetime_Dictionary_Term $term) {
		foreach ($this->getEntity()->getAnswers() as $answer) {
			/** @var Df_Poll_Model_Poll_Answer $answer */
			/** @var string|null $textProcessed */
			$textProcessed = $term->translate($answer->getAnswerTitle());
			if (!is_null($textProcessed)) {
				$answer->setAnswerTitle($textProcessed);
			}
		}
	}
}


 