/**
  * CMS JS Function
  */

function previewAction(formId, formObj, url){
    var formElem = $(formId);
    var previewWindowName = 'cms-page-preview-' + $('page_page_id').value;

    formElem.writeAttribute('target', previewWindowName);
    formObj.submit(url);
    formElem.writeAttribute('target', '');
}

function publishAction(publishUrl){
    setLocation(publishUrl);
}

function saveAndPublishAction(formObj, saveUrl){
    formObj.submit(saveUrl + 'back/publish/');
}

function dataChanged() {
    var buttonSaveAndPublish = $('save_publish_button');
    if (buttonSaveAndPublish && buttonSaveAndPublish.hasClassName('no-display')) {
        var buttonPublish = $('publish_button');
        if (buttonPublish) {
            buttonPublish.hide();
        }
        buttonSaveAndPublish.removeClassName('no-display');
    }
}

varienGlobalEvents.attachEventHandler('tinymceChange', dataChanged);
