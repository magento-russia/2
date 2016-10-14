<?php
class Df_Directory_Helper_Data extends Mage_Directory_Helper_Data {
	/** @return Df_Directory_Helper_Country */
	public function country() {return Df_Directory_Helper_Country::s();}

	/** @return Df_Directory_Helper_Finder */
	public function finder() {return Df_Directory_Helper_Finder::s();}

	/** @return Zend_Locale */
	public function getLocaleEnglish() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = new Zend_Locale(Mage_Core_Model_Locale::DEFAULT_LOCALE);
		}
		return $this->{__METHOD__};
	}

	/**
	 * Цель перекрытия —
	 * «Исправление упорядочивания субъектов РФ для Webkit».
	 *
	 * Этот метод используют устаревшие версии Magento CE.
	 * Новые версии Magento CE вместо
	 * @see Mage_Directory_Helper_Data::getRegionJson()
	 * используют новый (отсутствующий в устаревших версиях) метод
	 * @see Mage_Directory_Helper_Data::getRegionJsonByStore().
	 * Для этого нового метода @see Mage_Directory_Helper_Data::getRegionJsonByStore()
	 * заплатка содержится в методе @see Df_Directory_Helper_Data::_getRegions()
	 * @override
	 * @deprecated after 1.7.0.2
	 * @return string
	 */
	public function getRegionJson() {
		Varien_Profiler::start('TEST: '.__METHOD__);
		if (!$this->_regionJson) {
			$cacheKey = 'DIRECTORY_REGIONS_JSON_STORE' . rm_store_id();
			if (Mage::app()->useCache('config')) {
				$json = Mage::app()->loadCache($cacheKey);
			}
			if (empty($json)) {
				/** @var Df_Directory_Model_Resource_Region_Collection $collection */
				$collection = Df_Directory_Model_Region::c();
				$collection
					/** @uses Mage_Directory_Model_Country::getCountryId() */
					->addCountryFilter($this->getCountryCollection()->walk('getCountryId'))
					->load();
				$regions = array();
				foreach ($collection as $region) {
					/** @var Df_Directory_Model_Region $region */
					if (!$region->getRegionId()) {
						continue;
					}
					$regions[$region->getCountryId()][]= array(
						'code' => $region->getCode()
						,'name' => $this->__($region->getName())
						// НАЧАЛО ЗАПЛАТКИ
						/**
						 * 2014-10-23
						 * Задача нашей заплатки — добавить в массив регионов их идентификаторы.
						 * Эти идентификаторы используются потом только в одном месте:
						 * в перекрытом скрипте RegionUpdater.js,
						 * который, в силу архитектуры Magento CE, дублируется в двух местах:
						 * для административной части и для витрины.
						 *
						 * Скрипт RegionUpdater.js был перекрыт 3 года назад, 2011-11-05,
						 * причём к заплатке в системе контроля версий был дан такой комментарий:
						 * «Исправление упорядочивания субъектов РФ для Webkit».
						 *
						 * Я уже сейчас не помню, в чём там проблема была с упорядочиванием регионов,
						 * но заплатка оставалась все 3 года и останется сейчас.
						 */
						,'id' => $region->getRegionId()
						// КОНЕЦ ЗАПЛАТКИ
					);
				}
				$json = df_mage()->coreHelper()->jsonEncode($regions);
				if (Mage::app()->useCache('config')) {
					Mage::app()->saveCache($json, $cacheKey, array('config'));
				}
			}
			$this->_regionJson = $json;
		}

		Varien_Profiler::stop('TEST: '.__METHOD__);
		return $this->_regionJson;
	}

	/**
	 * @param int $regionId
	 * @return string
	 */
	public function getRegionFullNameById($regionId) {
		df_param_integer($regionId, 0);
		/** @var Mage_Directory_Model_Region $region */
		$region = $this->getRussianRegions()->getItemById($regionId);
		df_assert($region);
		return $region->getName();
	}

	/** @return Df_Directory_Model_Resource_Region_Collection */
	public function getRegions() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Directory_Model_Region::c();
			$this->normalizeRegions($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param int $regionId
	 * @return string|null
	 */
	public function getRegionNameById($regionId) {
		if (
				!isset($this->_regionNameById[$regionId])
			&&
				!dfa($this->_regionNameByIdIsNull, $regionId, false)
		) {
			/** @var Mage_Directory_Model_Region|null $region */
			$region = $this->getRegions()->getItemById($regionId);
			/** @var string|null $result */
			$result =
				is_null($region)
				? null
				: dfa(
					$region->getData()
					,Df_Directory_Model_Region::P__ORIGINAL_NAME
					,$region->getName()
				)
			;
			if (!is_null($result)) {
				df_result_string($result);
			}
			else {
				$this->_regionNameByIdIsNull[$regionId] = true;
			}
			$this->_regionNameById[$regionId] = $result;
		}
		return $this->_regionNameById[$regionId];
	}
	/** @var string[] */
	private $_regionNameById = array();
	/** @var bool[] */
	private $_regionNameByIdIsNull = array();

	/** @return Df_Directory_Model_Resource_Region_Collection */
	public function getRussianRegions() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_h()->directory()->country()->getRussia()->getRegions();
			$this->normalizeRegions($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param string $locationName
	 * @return string
	 */
	public function normalizeLocationName($locationName) {
		return df_trim(str_replace('Ё', 'Е', mb_strtoupper($locationName)));
	}

	/**
	 * @param Varien_Data_Collection_Db $regions
	 * @return Mage_Directory_Model_Resource_Region_Collection|Mage_Directory_Model_Mysql4_Region_Collection
	 */
	public function normalizeRegions(Varien_Data_Collection_Db $regions) {
		/** @var bool $needNormalize */
		static $needNormalize;
		if (is_null($needNormalize)) {
			$needNormalize = !df_cfg()->directory()->regionsRu()->getEnabled();
		}
		if ($needNormalize) {
			Df_Directory_Model_Handler_ProcessRegionsAfterLoading::addTypeToNameStatic($regions);
		}
		return $this;
	}

	/**
	 * Цель перекрытия —
	 * «Исправление упорядочивания субъектов РФ для Webkit».
	 *
	 * Обратите внимание, что данный метод отсутствует
	 * и не должен вызываться в устаревших версиях Magento CE.
	 * @override
	 * @param Df_Core_Model_StoreM|int|string|bool|null $storeId
	 * @return array(string => mixed)|null
	 */
	protected function _getRegions($storeId) {
		/** @var int[] $countryIds */
		$countryIds = array();
		/** @var Mage_Directory_Model_Resource_Country_Collection $collection */
		$countryCollection = $this->getCountryCollection()->loadByStore($storeId);
		/** @var Mage_Directory_Model_Region $regionModel */
		$regionModel = $this->_factory->getModel('directory/region');
		/** @var Mage_Directory_Model_Resource_Region_Collection $collection */
		$collection =
			$regionModel->getResourceCollection()
				/** @uses Mage_Directory_Model_Country::getCountryId() */
				->addCountryFilter($countryCollection->walk('getCountryId'))
				->load()
		;
		/** @var array(string => array(string => bool|string[])) $regions */
		$regions = array(
			'config' => array(
				/**
				 * Обратите внимание, что методы
				 * @see Mage_Directory_Helper_Data::getShowNonRequiredState()
				 * @see Mage_Directory_Helper_Data::getCountriesWithStatesRequired()
				 * отсутстствуют в устаревших версях Magento CE,
				 * однако в этих устаревших версиях мы вообще не должны попадать в данный метод
				 * @see _getRegions()
				 */
				'show_all_regions' => $this->getShowNonRequiredState(),
				'regions_required' => $this->getCountriesWithStatesRequired()
			)
		);
		foreach ($collection as $region) {
			/** @var Mage_Directory_Model_Region $region */
			if (!$region->getRegionId()) {
				continue;
			}
			$regions[$region->getCountryId()][$region->getRegionId()] = array(
				'code' => $region->getCode()
				,'name' => $this->__($region->getName())
				// НАЧАЛО ЗАПЛАТКИ
				/**
				 * 2014-10-23
				 * Задача нашей заплатки — добавить в массив регионов их идентификаторы.
				 * Эти идентификаторы используются потом только в одном месте:
				 * в перекрытом скрипте RegionUpdater.js,
				 * который, в силу архитектуры Magento CE, дублируется в двух местах:
				 * для административной части и для витрины.
				 *
				 * Скрипт RegionUpdater.js был перекрыт 3 года назад, 2011-11-05,
				 * причём к заплатке в системе контроля версий был дан такой комментарий:
				 * «Исправление упорядочивания субъектов РФ для Webkit».
				 *
				 * Я уже сейчас не помню, в чём там проблема была с упорядочиванием регионов,
				 * но заплатка оставалась все 3 года и останется сейчас.
				 */
				,'id' => $region->getRegionId()
				// КОНЕЦ ЗАПЛАТКИ
			);
		}
		return $regions;
	}

	/** @return Df_Directory_Helper_Data */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}