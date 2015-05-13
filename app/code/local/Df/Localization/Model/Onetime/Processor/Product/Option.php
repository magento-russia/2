<?php
/**
 * @method Df_Catalog_Model_Product_Option_Title getEntity()
 */
class Df_Localization_Model_Onetime_Processor_Product_Option
	extends Df_Localization_Model_Onetime_Processor_Entity {
	/**
	 * @override
	 * @return string
	 */
	protected function getTitlePropertyName() {return 'title';}

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


 