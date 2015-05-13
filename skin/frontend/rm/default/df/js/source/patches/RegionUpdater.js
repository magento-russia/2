/**
 * Обратите внимание, что нужно писать именно rm.defined(window.RegionUpdater),
 * а не rm.defined(RegionUpdater),
 * потому что второй вариант приводит к сбою в Firefox:
 * «ReferenceError: RegionUpdater is not defined».
 */
if (rm.defined(window.RegionUpdater)) {
	RegionUpdater.prototype.update =
		function() {
			if (this.regions[this.countryEl.value]) {
				var i, option, region, def;
				var defaultRegionId = this.regionSelectEl.getAttribute('defaultValue');
				if (this.regionTextEl) {
					def = this.regionTextEl.value.toLowerCase();
					this.regionTextEl.value = '';
				}
				if (!def) {
					def = defaultRegionId;
				}
				this.regionSelectEl.options.length = 1;
				for (regionId in this.regions[this.countryEl.value]) {
					region = this.regions[this.countryEl.value][regionId];
					// НАЧАЛО ЗАПЛАТКИ
					/**
					 * 2014-10-23
					 * Скрипт RegionUpdater.js был перекрыт 3 года назад, 2011-11-05,
					 * причём к заплатке в системе контроля версий был дан такой комментарий:
					 * «Исправление упорядочивания субъектов РФ для Webkit».
					 *
					 * Я уже сейчас не помню, в чём там проблема была с упорядочиванием регионов,
					 * но заплатка оставалась все 3 года и останется сейчас.
					 *
					 * Обратите внимание, что идентификаторы добавлены в массив регионов
					 * другой заплаткой, в методах
					 * @see Df_Directory_Helper_Data::getRegionJson()
					 * @see Df_Directory_Helper_Data::_getRegions()
					 */
					regionId = region.id;
					if (rm.undefined(regionId)) {
						continue;
					}
					// КОНЕЦ ЗАПЛАТКИ
					option = document.createElement('OPTION');
					option.value = regionId;
					option.text = region.name;
					if (this.regionSelectEl.options.add) {
						this.regionSelectEl.options.add(option);
					} else {
						this.regionSelectEl.appendChild(option);
					}
					if (
							(regionId == defaultRegionId)
						||
							(region.name.toLowerCase()==def)
						||
							(region.code.toLowerCase()==def)
					) {
						this.regionSelectEl.value = regionId;
					}
				}
				if (this.disableAction=='hide') {
					if (this.regionTextEl) {
						this.regionTextEl.style.display = 'none';
					}
					this.regionSelectEl.style.display = '';
				} else if (this.disableAction=='disable') {
					if (this.regionTextEl) {
						this.regionTextEl.disabled = true;
					}
					this.regionSelectEl.disabled = false;
				}
				this.setMarkDisplay(this.regionSelectEl, true);
			} else {
				if (this.disableAction=='hide') {
					if (this.regionTextEl) {
						this.regionTextEl.style.display = '';
					}
					this.regionSelectEl.style.display = 'none';
					Validation.reset(this.regionSelectEl);
				} else if (this.disableAction=='disable') {
					if (this.regionTextEl) {
						this.regionTextEl.disabled = false;
					}
					this.regionSelectEl.disabled = true;
				} else if (this.disableAction=='nullify') {
					this.regionSelectEl.options.length = 1;
					this.regionSelectEl.value = '';
					this.regionSelectEl.selectedIndex = 0;
					this.lastCountryId = '';
				}
				this.setMarkDisplay(this.regionSelectEl, false);
			}
			// Make Zip and its label required/optional
			var zipUpdater = new ZipUpdater(this.countryEl.value, this.zipEl);
			zipUpdater.update();
		}
	;
}