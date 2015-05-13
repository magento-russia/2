;(function($) { $(function() {
	$('.cms-hierarchy .fieldset')
		.each(
			function() {
				rm.admin.configForm.Fieldset
					.construct(
						{
							element: $(this)
						}
					)
				;
			}
		)
	;
	$('.cms-hierarchy .fieldset .df-field')
		.change(
			function() {
				if (rm.defined(window.hierarchyNodes)) {
					hierarchyNodes.nodeChanged()
				}
			}
		)
	;
}); })(jQuery);
