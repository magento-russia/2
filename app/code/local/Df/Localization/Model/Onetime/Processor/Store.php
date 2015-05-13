<?php
class Df_Localization_Model_Onetime_Processor_Store
	extends Df_Localization_Model_Onetime_Processor_Entity {
	/**
	 * @override
	 * @return string
	 */
	protected function getTitlePropertyName() {return 'name';}

	/**
	 * Перекрытие родительского метода позволяет переводить название магазина
	 * не только посредством тега new_title, но и посредством тега term,
	 * что даёт возможность переводить названия сразу нескольких магазинов одним правилом,
	 * например:
			<rule>
				<conditions>
					<type>store</type>
				</conditions>
				<actions>
					<term>
						<from>English</from>
						<to>Русский</to>
					</term>
				</actions>
			</rule>
	 * @override
	 * @return string[]
	 */
	protected function getTranslatableProperties() {return array($this->getTitlePropertyName());}
}