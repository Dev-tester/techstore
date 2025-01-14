(function ()
{
	"use strict";
	BX.namespace("BX.Crm.Report.Dashboard.Content");

	/**
	 * @param options
	 * @extends {BX.Report.Dashboard.Content}
	 * @constructor
	 */
	BX.Crm.Report.Dashboard.Content.SalesTarget = function (options)
	{
		BX.Report.Dashboard.Content.Html.apply(this, arguments);

		console.log(options);
		this.layout.settingsButtonInHeader = null;
	};

	BX.Crm.Report.Dashboard.Content.SalesTarget.prototype = {
		__proto__: BX.Report.Dashboard.Content.Html.prototype,
		constructor: BX.Crm.Report.Dashboard.Content.SalesTarget,
		render: function()
		{

			var widgetHeadWrapper = this.getWidget().getHeadWrapper();

			widgetHeadWrapper.appendChild(this.getSettingsButtonInHeader());

			return BX.Report.Dashboard.Content.Html.prototype.render.call(this);
		},
		getSettingsButtonInHeader: function()
		{
			if (this.layout.settingsButtonContainer)
			{
				return this.layout.settingsButtonContainer;
			}

			this.layout.settingsButtonContainer = BX.create('div', {
				attrs: {
					className: 'report-visualconstructor-properties-in-heed-button'
				},
				text: BX.message('CRM_SALES_TARGET_CONFIGURE_BUTTON_TITLE'),
				events: {
					click: this.handleTargetConfigureClick.bind(this)
				}
			});
			return this.layout.settingsButtonContainer;
		},
		handleTargetConfigureClick: function()
		{
			console.log(this);
		}

	};

})();
