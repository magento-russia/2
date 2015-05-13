/**
 * Модификация стандартного для Magento класса Fieldset
 * Мой класс запоминает состояние раскрытости/схлопнутости вкладок системных настроек
 * на момент сохранения системных настроек
 */
var Fieldset = {
    cookiePrefix: 'fh-',
    applyCollapse: function(containerId) {
        var collapsed = Cookie.read(this.cookiePrefix + containerId);
        if (collapsed !== null) {
            Cookie.clear(this.cookiePrefix + containerId);
        }
        if ($(containerId + '-state')) {
            collapsed = $(containerId + '-state').value == 1 ? 0 : 1;
        } else {
            collapsed = $(containerId + '-head').collapsed;
        }
        if (collapsed==1 || collapsed===undefined) {
           $(containerId + '-head').removeClassName('open');
           $(containerId).hide();
        } else {
           $(containerId + '-head').addClassName('open');
           $(containerId).show();
        }
    },
    toggleCollapse: function(containerId, saveThroughAjax) {
        if ($(containerId + '-state')) {
            collapsed = $(containerId + '-state').value == 1 ? 0 : 1;
        } else {
            collapsed = $(containerId + '-head').collapsed;
        }
        Cookie.read(this.cookiePrefix + containerId);
        if(collapsed==1 || collapsed===undefined) {
            Cookie.write(this.cookiePrefix + containerId,  0, 30*24*60*60);
            if ($(containerId + '-state')) {
                $(containerId + '-state').value = 1;
            }
            $(containerId + '-head').collapsed = 0;
        } else {
            Cookie.clear(this.cookiePrefix + containerId);
            if ($(containerId + '-state')) {
                $(containerId + '-state').value = 0;
            }
            $(containerId + '-head').collapsed = 1;
        }

        this.applyCollapse(containerId);
        if (typeof saveThroughAjax != "undefined") {
            this.saveState(saveThroughAjax, {container: containerId, value: $(containerId + '-state').value});
        }
    },
    addToPrefix: function (value) {
        this.cookiePrefix += value + '-';
    },
    saveState: function(url, parameters) {
        new Ajax.Request(url, {
            method: 'get',
            parameters: Object.toQueryString(parameters),
            loaderArea: false
        });
    }
};