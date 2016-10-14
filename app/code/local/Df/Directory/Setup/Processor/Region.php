<?php
class Df_Directory_Setup_Processor_Region extends Df_Core_Model {
	/** @return Df_Directory_Setup_Processor_Region */
	public function process() {
		/** @var Mage_Directory_Model_Region $region */
		$region = $this->getLegacyRussianRegionByNamePart($this->getRegion()->getName());
		if (is_null($region)) {
			$region = Df_Directory_Model_Region::i();
		}
		$region->addData(array(
			Df_Directory_Model_Region::P__COUNTRY_ID => Df_Directory_Helper_Country::ISO_2_CODE__RUSSIA
			,Df_Directory_Model_Region::P__CODE => $this->getRegion()->getCode()
			,Df_Directory_Model_Region::P__DEFAULT_NAME => $this->getRegion()->getName()
			,Df_Directory_Model_Region::P__DF_CAPITAL => $this->getRegion()->getCapital()
			,Df_Directory_Model_Region::P__DF_TYPE => $this->getRegion()->getType()
		));
		$region->save();
		rm_nat($region->getId());
		$this
			//->addRegionLocaleNameToDb($region, Mage_Core_Model_Locale::DEFAULT_LOCALE)
			->addRegionLocaleNameToDb($region, 'ru_RU')
		;
		return $this;
	}

	/**
	 * @param Mage_Directory_Model_Region $region
	 * @param string $localeCode
	 * @return Df_Directory_Setup_Processor_Region
	 */
	private function addRegionLocaleNameToDb(Mage_Directory_Model_Region $region, $localeCode) {
		df_param_string($localeCode, 1);
		/** @var Zend_Db_Select $select */
		$select = rm_select()
			->from(
				array('maintable' => $this->getTableRegionName())
				, Df_Directory_Model_Resource_Region::s()->getIdFieldName()
			)
			->where('? = maintable.locale', $localeCode)
			->where('? = maintable.region_id', $region->getId())
		;
		/** @var Zend_Db_Statement_Pdo $query */
		$query = rm_conn()->query($select);
		if (false === $query->fetch()) {
			rm_conn()->insert($this->getTableRegionName(), array(
				Df_Directory_Model_Region::P__LOCALE =>	$localeCode
				,Df_Directory_Model_Region::P__REGION_ID =>	$region->getId()
				,Df_Directory_Model_Region::P__NAME => $this->getRegion()->getName()
			));
		}
		else {
			rm_conn()->update(
				$this->getTableRegionName()
				,array(Df_Directory_Model_Region::P__NAME => $this->getRegion()->getName())
				,array('? = locale' => $localeCode, '? = region_id' => $region->getId())
			);
		}
		return $this;
	}

	/** @return Df_Core_Model_Resource_Setup */
	private function getInstaller() {return $this->cfg(self::$P__INSTALLER);}

	/**
	 * @param string $namePart
	 * @return Mage_Directory_Model_Region|null
	 */
	private function getLegacyRussianRegionByNamePart($namePart) {
		/** @var Mage_Directory_Model_Region $result */
		$result = null;
		$mapFromMyToCifrum =
			array(
				'Северная Осетия — Алания' => 'Сев.Осетия-Алания'
				,'Тыва (Тува)' => 'Тыва'
				,'Алтай' => 'Республика Алтай'
				,'Алтайский' => 'Алтайский край'
				,'Омская' => 'Омская область'
			)
		;
		if (!is_null(df_a($mapFromMyToCifrum, $namePart))) {
			$namePart = df_a($mapFromMyToCifrum, $namePart);
		}
		foreach (self::getRussianRegionsFromLegacyModules() as $region) {
			/** @var Mage_Directory_Model_Region $region */
			if (false !== mb_stripos ($region->getDefaultName(), $namePart)) {
				$result = $region;
				break;
			}
			if ($result) {
				break;
			}
		}
		return $result;
	}

	/** @return Df_Directory_Setup_Entity_Region */
	private function getRegion() {return $this->cfg(self::$P__REGION);}

	/** @return string */
	private function getTableRegionName() {
		return rm_table(Df_Directory_Model_Resource_Region::TABLE__NAME);
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::$P__REGION, Df_Directory_Setup_Entity_Region::_C)
			->_prop(self::$P__INSTALLER, Df_Core_Model_Resource_Setup::_C)
		;
	}
	const _C = __CLASS__;
	/** @var string */
	private static $P__INSTALLER = 'installer';
	/** @var string */
	private static $P__REGION = 'region';

	/**
	 * Обратите внимание, что кэшировать результат крайне важно,
	 * потому что ядро Magento этого не делает
	 * @return Mage_Directory_Model_Resource_Region_Collection
	 */
	private static function getRussianRegionsFromLegacyModules() {
		/** @var Mage_Directory_Model_Resource_Region_Collection $cache */
		static $cache;
		if (is_null($cache)) {
			$cache = df_h()->directory()->country()->getRussia()->getRegions();
		}
		return $cache;
	}

	/**
	 * @static
	 * @param Df_Directory_Setup_Entity_Region $region
	 * @param Df_Core_Model_Resource_Setup $installer
	 * @return Df_Directory_Setup_Processor_Region
	 */
	public static function i(
		Df_Directory_Setup_Entity_Region $region, Df_Core_Model_Resource_Setup $installer
	) {
		return new self(array(self::$P__REGION => $region, self::$P__INSTALLER => $installer));
	}
}