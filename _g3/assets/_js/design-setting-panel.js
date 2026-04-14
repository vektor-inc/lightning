var registerPlugin = wp.plugins.registerPlugin;
var PluginDocumentSettingPanel = wp.editor.PluginDocumentSettingPanel;
var SelectControl = wp.components.SelectControl;
var CheckboxControl = wp.components.CheckboxControl;
var useSelect = wp.data.useSelect;
var useEntityProp = wp.coreData.useEntityProp;
var createElement = wp.element.createElement;
var Fragment = wp.element.Fragment;
var i18n = window.lightningPanelI18n || {};

var LightningDesignSettingPanel = function() {
	var postType = useSelect(function(select) {
		return select('core/editor').getCurrentPostType();
	}, []);
	var result = useEntityProp('postType', postType, 'meta');
	var meta = result[0];
	var setMeta = result[1];
	var ds = meta && meta._lightning_design_setting ? meta._lightning_design_setting : {};
	var layout = ds.layout || 'default';
	var siteBodyPadding = ds.site_body_padding === 'true';
	var update = function(key, value) {
		var ns = Object.assign({}, ds);
		ns[key] = value;
		var nm = Object.assign({}, meta);
		nm._lightning_design_setting = ns;
		setMeta(nm);
	};
	return createElement(PluginDocumentSettingPanel,
		{name: 'lightning-design-setting', title: i18n.panelTitle || 'Lightning design setting'},
		createElement(SelectControl, {
			label: i18n.layoutSetting || 'Layout setting',
			value: layout,
			options: [
				{label: i18n.useCommon || 'Use common settings', value: 'default'},
				{label: i18n.col2 || '2 column', value: 'col-two'},
				{label: i18n.col1 || '1 column', value: 'col-one-no-subsection'},
				{label: i18n.col1Sidebar || '1 column (with sidebar element)', value: 'col-one'},
			],
			onChange: function(v) { update('layout', v); },
		}),
		createElement('h4', {style: {marginTop: '16px', marginBottom: '8px'}}, i18n.paddingMargin || 'Padding and margin setting'),
		createElement(CheckboxControl, {
			label: i18n.deletePadding || 'Delete site-body padding',
			checked: siteBodyPadding,
			onChange: function(c) { update('site_body_padding', c ? 'true' : ''); },
		})
	);
};
registerPlugin('lightning-design-setting-panel', {render: LightningDesignSettingPanel, icon: 'admin-settings'});
