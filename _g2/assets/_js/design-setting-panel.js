/**
 * Lightning G2 - Design Setting Panel
 * ブロックエディタ用サイドバーパネル（G2）
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
	const hiddenPageHeader = designSetting.hidden_page_header === 'true';
	const hiddenBreadcrumb = designSetting.hidden_breadcrumb === 'true';
	const siteContentPadding = designSetting.siteContent_padding === 'true';

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
				__( 'Page Header and Breadcrumb', 'lightning' )
			),
			createElement( CheckboxControl, {
				label: __( "Don't display Page Header", 'lightning' ),
				checked: hiddenPageHeader,
				onChange: ( checked ) =>
					updateDesignSetting( 'hidden_page_header', checked ? 'true' : '' ),
			} ),
			createElement( CheckboxControl, {
				label: __( "Don't display Breadcrumb", 'lightning' ),
				checked: hiddenBreadcrumb,
				onChange: ( checked ) =>
					updateDesignSetting( 'hidden_breadcrumb', checked ? 'true' : '' ),
			} ),
			createElement( 'h4', { style: { marginTop: '16px', marginBottom: '8px' } },
				__( 'Padding and margin setting', 'lightning' )
			),
			createElement( CheckboxControl, {
				label: __( 'Delete siteContent padding', 'lightning' ),
				checked: siteContentPadding,
				onChange: ( checked ) =>
					updateDesignSetting( 'siteContent_padding', checked ? 'true' : '' ),
			} )
		)
	);
};

registerPlugin( 'lightning-design-setting-panel', {
	render: LightningDesignSettingPanel,
	icon: 'admin-settings',
} );
