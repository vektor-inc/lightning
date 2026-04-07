/**
 * Lightning Design Setting - Block Editor Sidebar Panel
 * Lightning デザイン設定 - ブロックエディターサイドバーパネル
 *
 * Registers a native sidebar panel for the block editor that replaces
 * the legacy metabox. Adapts fields based on theme generation (G2/G3).
 * レガシーメタボックスを置き換えるブロックエディターネイティブサイドバーパネルを登録する。
 * テーマの世代（G2/G3）に応じてフィールドを切り替える。
 */
import { registerPlugin } from '@wordpress/plugins';
import { PluginDocumentSettingPanel } from '@wordpress/editor';
import { CheckboxControl, SelectControl } from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import { useEntityProp } from '@wordpress/core-data';

const data = window.lightningPanelData || {};
const i18n = data.i18n || {};
const generation = data.generation || 'g3';

/**
 * Lightning Design Panel Component
 * Lightning デザインパネルコンポーネント
 *
 * Renders layout and design settings in the block editor sidebar.
 * ブロックエディターサイドバーにレイアウトとデザイン設定を表示する。
 *
 * @return {JSX.Element} The panel component / パネルコンポーネント
 */
const LightningDesignPanel = () => {
	const postType = useSelect(
		( select ) => select( 'core/editor' ).getCurrentPostType(),
		[]
	);
	const [ meta, setMeta ] = useEntityProp( 'postType', postType, 'meta' );

	const setting = meta?._lightning_design_setting || {};

	/**
	 * Update a single key in the _lightning_design_setting meta object.
	 * _lightning_design_setting メタオブジェクトの単一キーを更新する。
	 *
	 * @param {string} key   - The setting key / 設定キー
	 * @param {string} value - The new value / 新しい値
	 */
	const updateSetting = ( key, value ) => {
		setMeta( {
			...meta,
			_lightning_design_setting: { ...setting, [ key ]: value },
		} );
	};

	// Layout options / レイアウト選択肢.
	const layoutOptions = [
		{
			value: 'default',
			label: i18n.useCommon || 'Use common settings',
		},
		{ value: 'col-two', label: i18n.twoCol || '2 column' },
		{
			value: 'col-one-no-subsection',
			label: i18n.oneCol || '1 column',
		},
		{
			value: 'col-one',
			label: i18n.oneColSidebar || '1 column (with sidebar element)',
		},
	];

	return (
		<PluginDocumentSettingPanel
			name="lightning-design"
			title={ data.panelTitle || 'Lightning' }
		>
			{ /* Layout setting / レイアウト設定 */ }
			<SelectControl
				label={ i18n.layoutSetting || 'Layout setting' }
				value={ setting.layout || 'default' }
				options={ layoutOptions }
				onChange={ ( v ) => updateSetting( 'layout', v ) }
			/>

			{ /* G2: Page Header and Breadcrumb / G2: ページヘッダーとパンくずリスト */ }
			{ generation === 'g2' && (
				<>
					<CheckboxControl
						label={
							i18n.hidePageHeader ||
							"Don't display Page Header"
						}
						checked={ !! setting.hidden_page_header }
						onChange={ ( c ) =>
							updateSetting(
								'hidden_page_header',
								c ? 'true' : ''
							)
						}
					/>
					<CheckboxControl
						label={
							i18n.hideBreadcrumb ||
							"Don't display Breadcrumb"
						}
						checked={ !! setting.hidden_breadcrumb }
						onChange={ ( c ) =>
							updateSetting(
								'hidden_breadcrumb',
								c ? 'true' : ''
							)
						}
					/>
					<CheckboxControl
						label={
							i18n.deleteSiteContent ||
							'Delete siteContent padding'
						}
						checked={ !! setting.siteContent_padding }
						onChange={ ( c ) =>
							updateSetting(
								'siteContent_padding',
								c ? 'true' : ''
							)
						}
					/>
				</>
			) }

			{ /* G3: Site body padding / G3: サイトボディパディング */ }
			{ generation === 'g3' && (
				<CheckboxControl
					label={
						i18n.deletePadding || 'Delete site-body padding'
					}
					checked={ !! setting.site_body_padding }
					onChange={ ( c ) =>
						updateSetting(
							'site_body_padding',
							c ? 'true' : ''
						)
					}
				/>
			) }
		</PluginDocumentSettingPanel>
	);
};

registerPlugin( 'lightning-design-panel', { render: LightningDesignPanel } );
