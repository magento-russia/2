<?php
abstract class Df_Directory_Model_Setup_Processor_InstallRegions extends Df_Core_Model_Abstract {
	/**
	 * @abstract
	 * @return string
	 */
	abstract protected function getCountryIso2Code();

	/**
	 * @abstract
	 * @return string
	 */
	abstract protected function getLocaleCode();

	/**
	 * @abstract
	 * @return array
	 */
	abstract protected function getRegionsDataRaw();

	/** @return void */
	public function process() {
		$this->regionsDelete();
		$this->regionsInsert();
		rm_cache_clean();
	}

	/** @return Df_Core_Model_Resource_Setup */
	private function getInstaller() {return $this->cfg(self::P__INSTALLER);}

	/** @return array(array(string => string|int)) */
	private function getRegionsData() {
		/** @var array $result */
		$result = array();
		/** @var int $ordering */
		$ordering = 0;
		foreach ($this->getRegionsDataRaw() as $regionDataRaw) {
			/** @var array $regionDataRaw */
			df_assert_array($regionDataRaw);
			/** @var array $region */
			$region = array(
				self::REGION__NAME__RUSSIAN => df_a($regionDataRaw, 0)
				,self::REGION__NAME__LOCAL => df_a($regionDataRaw, 1)
				,self::REGION__CENTER__RUSSIAN => df_a($regionDataRaw, 2)
				,self::REGION__CENTER__LOCAL => df_a($regionDataRaw, 3)
				,self::REGION__CODE =>
					df_a(
						$regionDataRaw
						, 4
						, rm_sprintf('%s-%02d', $this->getCountryIso2Code(), ++$ordering)
					)
				,self::REGION__TYPE => 0
			);
			$result[]= $region;
		}
		return $result;
	}

	/**
	 * Удаляем уже имеющиеся в БД регионы данной страны перед записью новых регионов
	 * @return Df_Directory_Model_Setup_Processor_InstallRegions
	 */
	private function regionsDelete() {
		rm_conn()->delete(
			rm_table(Df_Directory_Model_Resource_Region::TABLE__PRIMARY)
			,array('? = country_id' => $this->getCountryIso2Code())
		);
		return $this;
	}

	/**
	 * @param array $regionData
	 * @return Df_Directory_Model_Setup_Processor_InstallRegions
	 */
	private function regionInsert(array $regionData) {
		rm_conn()->insert(
			rm_table(Df_Directory_Model_Resource_Region::TABLE__PRIMARY)
			,array(
				Df_Directory_Model_Region::P__COUNTRY_ID => $this->getCountryIso2Code()
				,Df_Directory_Model_Region::P__CODE => df_a($regionData, self::REGION__CODE)
				,Df_Directory_Model_Region::P__DEFAULT_NAME => df_a($regionData, self::REGION__NAME__RUSSIAN)
				,Df_Directory_Model_Region::P__DF_TYPE => 0
				,Df_Directory_Model_Region::P__DF_CAPITAL => df_a($regionData, self::REGION__CENTER__RUSSIAN)
			)
		);
		/** @var int $regionId */
		$regionId = rm_nat(rm_conn()->lastInsertId());
		rm_conn()->insert(
			rm_table(Df_Directory_Model_Resource_Region::TABLE__NAME)
			,array(
				'locale' => $this->getLocaleCode()
				,'region_id' => $regionId
				,'name' => df_a($regionData, self::REGION__NAME__LOCAL)
			)
		);
		return $this;
	}

	/** @return Df_Directory_Model_Setup_Processor_InstallRegions */
	private function regionsInsert() {
		foreach ($this->getRegionsData() as $regionData) {
			/** @var array $regionData */
			df_assert_array($regionData);
			$this->regionInsert($regionData);
		}
		return $this;
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__INSTALLER, Df_Core_Model_Resource_Setup::_CLASS);
	}
	const _CLASS = __CLASS__;
	const P__INSTALLER = 'installer';
	const REGION__CENTER__LOCAL = 'center_local';
	const REGION__CENTER__RUSSIAN = 'center_russian';
	const REGION__CODE = 'code';
	const REGION__NAME__LOCAL = 'name_local';
	const REGION__NAME__RUSSIAN = 'name_russian';
	const REGION__TYPE = 'type';
}