<?php
/**
 * CSS Simple Tree Shaking
 * 
 * Description: CSS 定義データから未使用の id, class, tag に関する定義を取り除き縮小化します
 * 
 * Version: 1.0.2
 * Author: enomoto@celtislab
 * Author URI: https://celtislab.net/
 * License: GPLv2
 * 
 */
namespace celtislab;

defined( 'ABSPATH' ) || exit;

class CSS_tree_shaking {
    
    private static $cmplist;
    private static $varlist;
    private static $jsaddlist;
    private static $atrule_data;
	function __construct() {}
    
    // アットルール一時退避( @{md5key} に置き換えておく)
    private static function atrule_store($css){
        $pattern = array( '|(@(\-[\w]+\-)?keyframes.+?\{)(.+?\})\}|',
                          '|(@(\-[\w]+\-)?supports.+?\{)(.+?\})\}|',
                          '|(@(\-[\w]+\-)?media.+?\{)(.+?\})\}|',
                          '|@[^;\{]+?;|',
                          '|@[^;\{]+?\{(.+?)\}|'
                        );
        foreach ( $pattern as $atrule ) {
            $css = preg_replace_callback( $atrule, function($matches) {
                $key = 'AR_' . md5($matches[0]);
                $data = $matches[0];
                if(!empty($matches[3])){
                    $incss = self::atrule_store( $matches[3] );
                    $incss = self::tree_shaking( $incss );
                    $data = (!empty($incss))? $matches[1] . $incss . '}' : '';
                }
               	self::$atrule_data[ $key ] = $data;
                return '@{' . $key . '}';
            }, $css);            
        }
        return $css;
    }

    //アットルール復元
    private static function atrule_restore($css){
        $css = preg_replace_callback('|@\{(.+?)\}|', function($matches) {
            $data = $matches[0];
            $key = $matches[1];
            if(strpos($key, 'AR_') !== false){
                $data = (!empty(self::$atrule_data[ $key ]))? self::atrule_restore( self::$atrule_data[ $key ] ) : ''; 
            }
            return $data;
        }, $css);
        return $css;
    }
    
    //CSS から未使用の id, class, tag を含む定義を削除
    private static function tree_shaking($css) {
        $ncss = preg_replace_callback("|(.+?)(\{.+?\})|u", function($matches) {
            $data = $matches[0];
            $sel = $matches[1];
            if($sel !== '@'){
                $pattern = array( 'id'    => '|(#)([\w\-\\%]+)|u',
                                  'class' => '|(\.)([\w\-\\%]+)|u',
                                  'tag'   => '/(^|[,\s>\+~\(\)\]\|])([\w\-]+)/iu'
                                );

                $slist = array_map("trim", explode(',', $sel));
                foreach ($slist as $s) {
                    //selector の判定条件から :not(...) を除外 ($_s で判定して削除時は $s で行う)
                    $_s = $s;
                    if(preg_match('/:not\(.+?\)/', $s)){
                        $_s = preg_replace( '/:not\(.+?\)/', '', $s );
                    }
                    foreach (array('id','class','tag') as $item) {
                        if(!empty($_s) && preg_match_all( $pattern[$item], $_s, $match)){
                            foreach ($match[2] as $val) {
                                //$jsaddlist 登録名が一部でも含まれていれば削除しない（処理を簡略化するため上位層のセレクタのみで判定）
                                if(in_array($val, self::$jsaddlist))
                                    break;
                                
                                //$cmplist 登録名に含まれていないものが一つでもあれば削除
                                if(!preg_match('/^[0-9]+/', $val) && !in_array($val, self::$cmplist[$item])){
                                    $sel = preg_replace( '/(' . preg_quote($s) . ')(,|$)/u', '$2', $sel, 1 );
                                    $s = '';
                                    break;
                                }
                            }
                        }
                    }
                }
                $sel = preg_replace('/,+/su', ',', $sel);
                $sel = preg_replace('/\s+/su', ' ', $sel);
                $sel = trim($sel);
                $sel = trim($sel, ',');
                $data = (!empty($sel))? $sel . $matches[2] : '';
            }
            return $data;
        }, $css);
        return $ncss;
    }
    
    //未使用変数定義の削除（tree_shaking 実行後に実施する必要あり）
    public static function tree_shaking4var($css) {
        $ncss = $css;
        self::$varlist = array();
        if(preg_match_all( '/var\((\-\-.+?)\)/iu', $css, $vmatchs)){
            foreach ($vmatchs[1] as $v) {
                $v = trim($v);
                if(!in_array($v, self::$varlist)){
                    self::$varlist[] = $v;
                }
            }
        }
        //url() 定義時は文字列中に ; がそのまま含まれている場合があるの分けて処理
        $ncss = preg_replace_callback( '|(\-\-[\w\-]+?):url\(.+?\);?|u', function($matches) {
            $data = $matches[0];
            if(!in_array(trim($matches[1]), self::$varlist)){
                $data = ''; 
            }
            return $data;
        }, $ncss);            
        $ncss = preg_replace_callback( '|(\-\-[\w\-]+?):(.+?)([;\}])|u', function($matches) {
            $data = $matches[0];
            if(!preg_match('|url\(|u', $matches[2]) && !in_array(trim($matches[1]), self::$varlist)){
                $data = ($matches[3] === '}')? '}' : ''; 
            }
            return $data;
        }, $ncss);            

        return $ncss;
    }
    
    /*=============================================================
     * CSS 内のコメント、改行、空白等を削除するだけのシンプルな縮小化
     */
    public static function simple_minify( $css ) {
        $res = preg_replace( '!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css );
        if(!empty($res)){
            $css = $res;
            $css = str_replace(array("\r", "\n"), '', $css);
            $css = str_replace("\t", ' ', $css);
            $css = preg_replace('/\s+/su', ' ', $css);
            $css = preg_replace('/\s([\{\}:=,\)*\/;>])/', '$1', $css);
            $css = preg_replace('/([\{\}:=,\(*\/!;>])\s/', '$1', $css);
        }
        return $css;
    }
    
    /*=============================================================
     * CSS 内の未使用 id, class, tag に関する定義を取り除く縮小化
     */
    public static function extended_minify($css, $html, $jsaddlist=array()) {
        self::$atrule_data = array();
        self::$jsaddlist = $jsaddlist;  //JS によりDOMロード後に追加される ID や class 等が除外されなくするための名前の事前登録用
        $inidata = array(
			'id'    => array(),
			'class' => array(
				'device-mobile',
				'header_scrolled',
				'active',
				'menu-open',
				'vk-mobile-nav-menu-btn',
				'vk-mobile-nav-open',
				'vk-menu-acc-active',
				'acc-parent-open',
				'acc-btn',
				'acc-btn-open',
				'acc-btn-close',
				'acc-child-open',
				'carousel-item-left',
				'carousel-item-next',
				'carousel-item-right',
                'carousel-item-prev',
                'form-control',
                'btn',
                'btn-primary',
				'.vk_post_imgOuter a:hover .card-img-overlay::after',
			),
			'tag'   => array(
				'html',
				'head',
				'body',
				'title',
				'style',
				'meta',
				'link',
				'script',
				'noscript'
			)
		);
		$inidata = apply_filters( 'css_tree_shaking_exclude', $inidata );
        $pattern = array( 'id'    => '|[\s\t\'"]id\s?=\s?[\'"](.+?)[\'"]|u',
                          'class' => '|[\s\t\'"]class\s?=\s?[\'"](.+?)[\'"]|u',
                          'tag'   => '|<([\w\-]+)|iu'
                        );
        
        if(empty(self::$cmplist['parse'])){
            self::$cmplist['parse'] = true;
            foreach (array('id','class','tag') as $item) {
                self::$cmplist[$item] = $inidata[$item];
                if(preg_match_all( $pattern[$item], $html, $matches)){
                    foreach ($matches[1] as $val) {
                        $arr = array_map("trim", explode(' ', $val));
                        foreach($arr as $dt){
                            if(!empty($dt) && !in_array($dt, self::$cmplist[$item]))
                                self::$cmplist[$item][] = $dt;
                        }
                    }
                }
            }
        }
        $css = self::simple_minify( $css );
        $css = self::atrule_store( $css );        
        $css = self::tree_shaking( $css );
        $css = self::atrule_restore( $css );        
        $css = self::tree_shaking4var( $css );
        return $css;
    }
}