<?php
namespace Df\C1\Cml2\Import\Data;
abstract class Df_C1_Cml2_Import_Data_Collection
	extends \Df\Xml\Parser\Collection {
	/**
	 * @param string $externalId
	 * @return Df_C1_Cml2_Import_Data_Entity|null
	 */
	public function findByExternalId($externalId) {return $this->findById($externalId);}

	/**
	 * Данный метод никак не связан данным с классом,
	 * однако включён в класс для удобного доступа объектов класса к реестру
	 * (чтобы писать $this->getState() вместо Df_C1_Cml2_State::s())
	 * @return Df_C1_Cml2_State
	 */
	protected function getState() {return Df_C1_Cml2_State::s();}
}