;(function($) { $(function() {
	rm.namespace('rm.vk.comments');
	if (rm.vk.comments.enabled) {
		rm.vk.Widget.construct({
			applicationId: rm.vk.comments.applicationId
			,containerId: 'vk_comments'
			,objectName: 'VK.Widgets.Comments'
			,parentSelector: '.product-view'
			,widgetSettings: rm.vk.comments.settings
		});
	}
	rm.namespace('rm.vk.like');
	if (rm.vk.like.enabled) {
		rm.vk.Widget.construct({
			applicationId: rm.vk.like.applicationId
			,containerId: 'vk_like'
			,objectName: 'VK.Widgets.Like'
			,parentSelector: '.product-shop'
			,widgetSettings: rm.vk.like.settings
		});
	}
	rm.namespace('rm.vk.groups');
	if (rm.vk.groups.enabled) {
		rm.vk.widget.Groups.construct({
			applicationId: rm.vk.groups.applicationId
			,containerId: 'vk_groups'
			,objectName: 'VK.Widgets.Group'
			,widgetSettings: rm.vk.groups.settings
		});
	}
}); })(jQuery);