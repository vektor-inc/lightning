/**
 * Lightning G3 - Design Setting Panel
 * ブロックエディタ用サイドバーパネル（G3）
 *
 * Replaces the legacy add_meta_box() with PluginDocumentSettingPanel.
 * レガシーなadd_meta_box()をPluginDocumentSettingPanelに置き換える。
 */

const { registerPlugin } = wp.plugins;
const { PluginDocumentSettingPanel } = wp.editPost;
const { SelectControl, CheckboxControl } = wp.components;
const { useSelect } = wp.data;
const { useEntityProp } = wp.coreData;
const { createElement, Fragment } = wp.element;
const { __ } = wp.i18n;

const LightningDesignSettingPanel = () => {
	const postType = useSelect(
		( select ) => select( 'core/editor' ).getCurrentPostType(),
		[]
	);

	const [ meta, setMeta ] = useEntityProp( 'postType', postType, 'meta' );

	const designSetting = meta && meta._lightning_design_setting
		? meta._lightning_design_setting
		: {};

	const layout = designSetting.layout || 'default';
	const siteBodyPadding = designSetting.site_body_padding === 'true';

	const updateDesignSetting = ( key, value ) => {
		setMeta( {
			...meta,
			_lightning_design_setting: {
				...designSetting,
				[ key ]: value,
			},
		} );
	};

	const layoutOptions = [
		{ label: __( 'Use common settings', 'lightning' ), value: 'default' },
		{ label: __( '2 column', 'lightning' ), value: 'col-two' },
		{ label: __( '1 column', 'lightning' ), value: 'col-one-no-subsection' },
		{ label: __( '1 column (with sidebar element)', 'lightning' ), value: 'col-one' },
	];

	return createElement(
		PluginDocumentSettingPanel,
		{
			name: 'lightning-design-setting',
			title: __( 'Lightning design setting', 'lightning' ),
			className: 'lightning-design-setting-panel',
		},
		createElement( Fragment, null,
			createElement( SelectControl, {
				label: __( 'Layout setting', 'lightning' ),
				value: layout,
				options: layoutOptions,
				onChange: ( value ) => updateDesignSetting( 'layout', value ),
			} ),
			createElement( 'h4', { style: { marginTop: '16px', marginBottom: '8px' } },
				__( 'Padding and margin setting', 'lightning' )
			),
			createElement( CheckboxControl, {
				label: __( 'Delete site-body padding', 'lightning' ),
				checked: siteBodyPadding,
				onChange: ( checked ) =>
					updateDesignSetting( 'site_body_padding', checked ? 'true' : '' ),
			} )
		)
	);
};

registerPlugin( 'lightning-design-setting-panel', {
	render: LightningDesignSettingPanel,
	icon: 'admin-settings',
} );
