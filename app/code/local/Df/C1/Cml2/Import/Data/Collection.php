<?php
namespace Df\C1\Cml2\Import\Data;
abstract class Collection extends \Df\Xml\Parser\Collection {
	/**
	 * @param string $externalId
	 * @return \Df\C1\Cml2\Import\Data\Entity|null
	 */
	public function findByExternalId($externalId) {return $this->findById($externalId);}

	/**
	 * Данный метод никак не связан данным с классом,
	 * однако включён в класс для удобного доступа объектов класса к реестру
	 * (чтобы писать $this->getState() вместо \Df\C1\Cml2\State::s())
	 * @return \Df\C1\Cml2\State
	 */
	protected function getState() {return \Df\C1\Cml2\State::s();}
}