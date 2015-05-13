<?php
class Df_Autotrading_Model_Api_Calculator extends Df_Core_Model_Abstract {
	/**
	 * @param string $paramName
	 * @param int $productIndex
	 * @return string
	 */
	public function addProductIndexToParameter($paramName, $productIndex) {
		return implode('_', array($paramName, $productIndex));
	}

	/** @return Df_Autotrading_Model_Request_Rate */
	public function getApi() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Autotrading_Model_Request_Rate::i($this->getRequestParameters());
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param string $locationName
	 * @return Df_Autotrading_Model_Api_Calculator
	 * @throws Exception
	 */
	private function assertDestinationIsAllowed($locationName) {
		if (!(Df_Autotrading_Model_Request_Locations::s()->isLocationAllowed($locationName))) {
			$this->getRequest()->throwExceptionInvalidDestination();
		}
		return $this;
	}
	
	/** @return string */
	private function getDestinationRegionalCenter() {
		if (!isset($this->{__METHOD__})) {
			/**
			 * Обратите внимание, что данный алгоритм работает только тех стран,
			 * для которых в справочнике Magento содержится информация о региональных центрах.
			 * Калькулятор Автотрейдинга рассчитывет доставку только в Россию,
			 * поэтому мы можем применять данный алгоритм.
			 */
			$this->{__METHOD__} = $this->getRequest()->getDestinationRegionalCenter();
			if (!$this->{__METHOD__}) {
				df_error(
					'Не могу определить областной центр населённого пункта «%s».'
					,rm_concat_clean(', '
						,$this->getRequest()->getDestinationCountry()->getName()
						,$this->getRequest()->getDestinationRegionName()
						,$this->getRequest()->getDestinationCity()
					)
				);
			}
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getLocationDestinationInApiFormat() {
		if (!isset($this->{__METHOD__})) {
			/** @var string $result */
			/**
			 * Поведение системы было логически неверным:
			 * если администратор указывал «нет» в качестве значения настройки
			 * «Должна ли служба доставки доставлять товар до дома покупателя?»,
			 * то система молча рассчитывала доставку до регионального центра,
			 * даже не предупреждая об этом покупателя.
			 * Таким образом, если покупатель ввёл адрес:
			 * город Мухоморск (несуществующий) Вологодская область,
			 * то система как ни в чём не бывало рассчитывала доставку до Вологды,
			 * не предупреждая покупателя о том, что доставки в Мухоморск нет.
			 */
			//			if (!$this->getServiceConfig()->needDeliverCargoToTheBuyerHome()) {
			//				$this->assertDestinationIsAllowed($this->getDestinationRegionalCenter());
			//				$result = $this->normalize($this->getDestinationRegionalCenter());
			//			}
			//			else {
			if ($this->isDestinationCityRegionalCenter()) {
				/**
				 * Обратите внимание, что правильный код — именно
				 * $this->assertDestinationIsAllowed($this->getRequest()->getDestinationCity());
				 * а не
				 * $this->assertDestinationIsAllowed($this->getRequest()->getDestinationRegionalCenter());
				 * потому что если покупатель при оформлении заказа укажет областью «Иркутская область»,
				 * а городом — «Петропавловск-Камчатский», то при коде
				 * $this->assertDestinationIsAllowed($this->getRequest()->getDestinationRegionalCenter());
				 * система выдаст неверное диагностическое сообщение:
				 * «Служба Автотрейдинг не доставляет грузы в населённый пункт Иркутск»,
				 * либо же рассчитает тариф до Иркутска, что тоже неверно.
				 */
				$this->assertDestinationIsAllowed($this->getRequest()->getDestinationCity());
				$result =
					rm_sprintf(
						'В пределы города %s'
						,$this->normalize($this->getDestinationRegionalCenter())
					)
				;
			}
			else {
				$this->assertDestinationIsAllowed($this->getRequest()->getDestinationCity());
				$result =
					$this->getPeripheralDestinationNameInApiFormat(
						$this->getRequest()->getDestinationCity()
						,$this->getDestinationRegionalCenter()
					)
				;
				if (!$result) {
					$this->getRequest()->throwExceptionInvalidDestination();
				}
			}
//			}
			df_result_string($result);
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getLocationOriginInApiFormat() {
		if (!isset($this->{__METHOD__})) {
			/** @var string $result */
			$result = 
				!$this->getServiceConfig()->needGetCargoFromTheShopStore()
				? $this->normalize($this->getOriginRegionalCenter())
				:
					(
						$this->isOriginCityRegionalCenter()
						? rm_sprintf(
							'В пределах города %s'
							,$this->normalize($this->getOriginRegionalCenter())
						)
						:
							$this->getPeripheralDestinationNameInApiFormat(
								$this->getRequest()->getOriginCity()
								,$this->getOriginRegionalCenter()
							)
					)
			;
			df_result_string($result);
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getOriginRegionalCenter() {
		if (!isset($this->{__METHOD__})) {
			/**
			 * Обратите внимание, что данный алгоритм работает только тех стран,
			 * для которых в справочнике Magento содержится информация о региональных центрах.
			 * Калькулятор Автотрейдинга рассчитывет доставку только из России,
			 * поэтому мы можем применять данный алгоритм.
			 */
			$this->{__METHOD__} = $this->getRequest()->getOriginRegionalCenter();
			df_result_string_not_empty($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param string $locationName
	 * @param string $regionalCenterName
	 * @return string
	 */
	private function getPeripheralDestinationNameInApiFormat($locationName, $regionalCenterName) {
		df_param_string($locationName, 0);
		df_param_string($regionalCenterName, 1);
		/** @var string $result */
		$result =
			df_a(
				df_a(
					Df_Autotrading_Model_Request_Locations::s()->getLocationsGrouped()
					,df_h()->directory()->normalizeLocationName($regionalCenterName)
					,array()
				)
				,df_h()->directory()->normalizeLocationName($locationName)
			)
		;
		if (!$result) {
			$this->getRequest()->throwExceptionInvalidDestination();
		}
		df_result_string($result);
		return $result;
	}

	/** @return Df_Shipping_Model_Rate_Request */
	private function getRequest() {return $this->cfg(self::$P__REQUEST);}

	/** @return array(string => string) */
	private function getRequestParameters() {
		if (!isset($this->{__METHOD__})) {
			/** @var array(string => string) $result */
			$result = array();
			if ($this->getServiceConfig()->checkCargoOnReceipt()) {
				// Должна ли служба доставки вскрывать и сверять груз при покупателе?
				// Название в модуле: service__check_cargo_on_receipt.
				// Название в интерфейсе калькулятора: «Отправка груза с приемом по ассортименту».
				$result['assort'] = 'assort';
			}
			// Объявленная ценность в том случае,
			// если служба доставки должна вскрывать и сверять груз при покупателе.
			// admin__declared_value_percent
			$result['cargo_cost'] =
				$this->getServiceConfig()->checkCargoOnReceipt()
				? $this->getRequest()->getDeclaredValueInRoubles()
				: 0
			;
			if ($this->getServiceConfig()->notifySenderAboutDelivery()) {
				/**
				 * Должна ли служба доставки
				 * уведомлять отправителя в письменном виде
				 * о доставке груза получателю?
				 * Название в модуле: service__notify_sender_about_delivery.
				 * Название в интерфейсе калькулятора: «Уведомление о выдаче груза».
				 */
				$result['mailuved'] = 'mailuved';
			}
			if ($this->getServiceConfig()->needCollapsiblePalletBox()) {
				// Нужен ли для груза поддон с деревянными съёмными ограждениями
				// (евроборт, паллетный борт)?
				// service__need_collapsible_pallet_box
				$result['evrobort'] = 'evrobort';
			}
			if ($this->getServiceConfig()->needPalletPacking()) {
				// Нужна ли услуга упаковки груза на поддоне (паллете)?
				// service__need_pallet_packing
				$result['pallet'] = 'pallet';
				// Сколько нужно поддонов?
				$result['pallet_places'] = 1;
				// Объём паллетированного груза
				$result['pallet_volume'] = 1;
			}
			if ($this->getServiceConfig()->needTaping()) {
				// Нужна ли услуга перетяжки груза обычной клейкой лентой?
				// service__need_taping
				$result['scotc'] = 'scotc';
			}
			if ($this->getServiceConfig()->needTapingAdvanced()) {
				// Нужна ли услуга перетяжки груза фирменной клейкой лентой?
				// service__need_taping_advanced
				$result['firmscotc'] = 'firmscotc';
			}
			if ($this->getServiceConfig()->needBox()) {
				// Нужна ли коробка?
				// service__need_box
				$result['box'] = 'box';
				// Сколько коробок нужно?
				$result['box_places'] = 1;
			}
			if ($this->getServiceConfig()->needBagPacking()) {
				// Нужна ли услуга упаковки груза в мешок?
				// service__need_bag_packing
				$result['meshok'] = 'meshok';
				// Сколько нужно мешков?
				$result['meshok_places'] = 1;
			}
			if ($this->getServiceConfig()->needOpenSlatCrate()) {
				// Нужна ли услуга обрешётки?
				// service__need_open_slat_crate
				$result['obreshotka'] = 'obreshotka';
			}
			if ($this->getServiceConfig()->needPlywoodBox()) {
				/**
				 * Нужна ли услуга упаковки груза в фанерный ящик?
				 * service__need_plywood_box
				 */
				$result['faneryashik'] = 'faneryashik';
			}
			/** @var float[] $dimensions */
			$dimensions = $this->getRequest()->getDimensionsRoughInMetres();
			$result = array_merge($result, array(
				'Calculate_form[from_filial]' => $this->normalize($this->getOriginRegionalCenter())
				,'Calculate_form[from]' => $this->getLocationOriginInApiFormat()
				,'Calculate_form[from_delivery]' => $this->getLocationOriginInApiFormat()
				,'Calculate_form[to_filial]' => $this->normalize($this->getDestinationRegionalCenter())
				,'Calculate_form[to]' => $this->getLocationDestinationInApiFormat()
				,'Calculate_form[to_delivery]' => $this->getLocationDestinationInApiFormat()
				,'Calculate_form[dlinna]' =>
					df_text()->formatFloat(df_a($dimensions, Df_Catalog_Model_Product::P__LENGTH), 2)
				,'Calculate_form[shirina]' =>
					df_text()->formatFloat(df_a($dimensions, Df_Catalog_Model_Product::P__WIDTH), 2)
				,'Calculate_form[visota]' =>
					df_text()->formatFloat(df_a($dimensions, Df_Catalog_Model_Product::P__HEIGHT), 2)
				,'Calculate_form[weight]' =>
					df_text()->formatFloat($this->getRequest()->getWeightInKilogrammes(), 2)
				,'Calculate_form[volume]' =>
					df_text()->formatFloat($this->getRequest()->getVolumeInCubicMetres(), 3)
			));
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Shipping_Model_Config_Facade */
	private function getRmConfig() {return $this->cfg(self::$P__RM_CONFIG);}

	/** @return Df_Autotrading_Model_Config_Area_Service */
	private function getServiceConfig() {return $this->getRmConfig()->service();}

	/** @return bool */
	private function isDestinationCityRegionalCenter() {
		return $this->getDestinationRegionalCenter() === $this->getRequest()->getDestinationCity();
	}

	/** @return bool */
	private function isOriginCityRegionalCenter() {
		return $this->getOriginRegionalCenter() === $this->getRequest()->getOriginCity();
	}

	/**
	 * @param string $locationName
	 * @return string
	 */
	private function normalize($locationName) {
		df_assert_string_not_empty($locationName, 0);
		/** @var string $result */
		$result =
					!rm_contains($locationName, '-')
				&&
					!rm_contains($locationName, ' ')
			? df_text()->ucfirst(mb_strtolower($locationName))
			:
				/**
				 * Чтобы не преображало «Санкт-Петербург» в «Санкт-петербург»
				 * (хотя влияние регистра букв на работу калькулятора Автотрейдинга не установлена)
				 */
				$locationName
		;
		return $result;
	}

	/**
	 * @param string $locationName
	 * @return void
	 * @throws Exception
	 */
	private function throwCanNotDeliver($locationName) {
		df_error('Служба Автотрейдинг не доставляет грузы в населённый пункт %s.', $locationName);
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::$P__REQUEST, Df_Shipping_Model_Rate_Request::_CLASS)
			->_prop(self::$P__RM_CONFIG, Df_Shipping_Model_Config_Facade::_CLASS)
		;
	}
	const _CLASS = __CLASS__;
	/** @var string */
	private static $P__REQUEST = 'request';
	/** @var string */
	private static $P__RM_CONFIG = 'rm_config';
	/**
	 * @static
	 * @param Df_Shipping_Model_Rate_Request $request
	 * @param Df_Shipping_Model_Config_Facade $config
	 * @return Df_Autotrading_Model_Api_Calculator
	 */
	public static function i(
		Df_Shipping_Model_Rate_Request $request, Df_Shipping_Model_Config_Facade $config
	) {return new self(array(self::$P__REQUEST => $request, self::$P__RM_CONFIG => $config));}
}