<?php
namespace Df\C1\Cml2\Import\Data\Collection;
class Attributes extends \Df\C1\Cml2\Import\Data\Collection {
	/**
	 * @override
	 * @return \Df\Xml\X[]
	 */
	protected function getImportEntitiesAsSimpleXMLElementArray() {
		if (!isset($this->{__METHOD__})) {
			/** @var \Df\Xml\X[] $result */
			$result = parent::getImportEntitiesAsSimpleXMLElementArray();
			/** @var \Df\Xml\X[] $entitiesFromAdditionalPath */
			$entitiesFromAdditionalPath = $this->e()->xpath($this->itemPath2());
			if (is_array($entitiesFromAdditionalPath)) {
				$result = array_merge($result, $entitiesFromAdditionalPath);
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2015-08-15
	 * @override
	 * @see \Df\Xml\Parser\Collection::itemClassAdvanced()
	 * @param \Df\Xml\X $e
	 * @return string
	 */
	protected function itemClassAdvanced(\Df\Xml\X $e) {
		return \Df\C1\Cml2\Import\Data\Entity\Attribute::getClass($e);
	}

	/**
	 * @override
	 * @see \Df\Xml\Parser\Collection::itemPath()
	 * @return string|string[]
	 */
	protected function itemPath() {return '/КоммерческаяИнформация/Классификатор/Свойства/Свойство';}

	/**
	 * 11 июля 2013 года в магазине belle.com.ua
	 * («Управление торговлей для Украины» редации 2.3
	 * ,редакция платформы 1С:Предприятие — 10.3) заметил,
	 * что одно свойство описано в import.xml следующим образом:
		<Классификатор>
			(...)
			<Свойства>
				<СвойствоНоменклатуры>
					<Ид>dd6bfa58-d7e9-11d9-bfbc-00112f3000a2</Ид>
					<Наименование>Канал сбыта</Наименование>
					<Обязательное>false</Обязательное>
					<Множественное>false</Множественное>
					<ИспользованиеСвойства>true</ИспользованиеСвойства>
				</СвойствоНоменклатуры>
			</Свойства>
		</Классификатор>
	 * Обратите внимание на использование тега «СвойствоНоменклатуры»
	 * вместо стандартного тега «Свойство».
	 * Причём это происходит в типовой конфигурации
	 * (смотрел программный код той же конфигурации другого магазина).
	 * @return string[]
	 */
	private function itemPath2() {
		return '/КоммерческаяИнформация/Классификатор/Свойства/СвойствоНоменклатуры';
	}

	/**
	 * @used-by \Df\C1\Cml2\State\Import\Collections::getAttributes()
	 * @static
	 * @param \Df\Xml\X $xml
	 * @return \Df\C1\Cml2\Import\Data\Collection\Attributes
	 */
	public static function i(\Df\Xml\X $xml) {return new self(array(self::$P__E => $xml));}
}