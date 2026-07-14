/* global jQuery, wp */
/**
 * 背景画像登録処理
 *
 * @param {HTMLElement} e ボタン要素
 */
window.vk_title_bg_image_addiditional = function( e ) {
	var d = jQuery( e ).parent().children( '._display' );
	var w = jQuery( e ).parent().children( '._form' ).children( '.__id' )[ 0 ];
	var u = wp.media( { library: { type: 'image' }, multiple: false } ).on( 'select', function() {
		u.state().get( 'selection' ).each( function( f ) {
			d.children().remove();
			d.append( jQuery( '<img style="width:100%;height:auto">' ).attr( 'src', f.toJSON().url ) );
			jQuery( w ).val( f.toJSON().id ).change();
		} );
	} );
	u.open();
};

/**
 * 背景画像削除処理
 *
 * @param {HTMLElement} e ボタン要素
 */
window.vk_title_bg_image_delete = function( e ) {
	var d = jQuery( e ).parent().children( '._display' );
	d.children().remove();
	jQuery( e ).parent().children( '._form' ).children( '.__id' ).attr( 'value', '' ).change();
};
