<?php
/**
 * @method Df_Rating_Model_Rating getEntity()
 */
class Df_Localization_Model_Onetime_Processor_Rating
	extends Df_Localization_Model_Onetime_Processor_Entity {
	/**
	 * @override
	 * @return string
	 */
	protected function getTitlePropertyName() {return 'rating_code';}

	/**
	 * Перекрытие родительского метода позволяет переводить название магазина
	 * не только посредством тега new_title, но и посредством тега term,
	 * что даёт возможность переводить названия сразу нескольких магазинов одним правилом,
	 * например:
			<rule>
				<conditions>
					<type>rating</type>
				</conditions>
				<actions>
					<term>
						<from>Overall</from>
						<to>Общий</to>
					</term>
				</actions>
			</rule>
	 * @override
	 * @return string[]
	 */
	protected function getTranslatableProperties() {return array($this->getTitlePropertyName());}
}


 