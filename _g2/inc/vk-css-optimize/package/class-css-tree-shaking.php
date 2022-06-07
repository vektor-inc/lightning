<?php
/*
The original of this file is located at:
https://github.com/vektor-inc/vektor-wp-libraries
If you want to change this file, please change the original file.
*/
/**
 * CSS Simple Tree Shaking
 *
 * Description: CSS tree shaking minify library
 *
 * Version: 2.1.0
 * Author: enomoto@celtislab
 * Modefied: Vektor:Inc.
 * Author URI: https://celtislab.net/
 * License: GPLv2
 *
 */
namespace celtislab\v2_1;

defined( 'ABSPATH' ) || exit;


class CSS_tree_shaking {

    private static $cmplist;
    private static $jsaddlist;

    const  SHAKING_PATTERN = '#@(?<pref>\-[\w]+\-)?(?<atname>((keyframes|supports|media|container|layer)[^\{]+?))\{(?<atstyle>.*?\})\}|@([^;\{\}]+?)(;|\{.*?\})|(?<sel>.+?)\{(?<pv>.+?)\}#u';

	function __construct() {}

    //上書きカスタムCSS(simple_minify済)をセレクタ(md5),プロパティ,値の配列にする
    //※セレクタが複数の場合も考慮して完全同一記述のセレクタのみを簡潔に重複判定出来るよう md5 を使用
    public static function create_csslist($cusomcss, &$csslist) {
        if(preg_match_all( self::SHAKING_PATTERN, $cusomcss, $matches, PREG_SET_ORDER)){
            foreach ($matches as $style) {
                if(!empty($style['atname'])){
                    $atkey = md5(trim($style['pref'] . $style['atname']));
                    if(preg_match_all( '#(?<sel>.+?)\{(?<pv>.+?)\}#u', $style['atstyle'], $atcustoms, PREG_SET_ORDER)){
                        foreach ($atcustoms as $subitem) {
                            self::_create_csslist($atkey, $subitem['sel'], $subitem['pv'], $csslist);
                        }
                    }
                } elseif(!empty($style['sel'])) {
                    self::_create_csslist(0, $style['sel'], $style['pv'], $csslist);
                }
            }
        }
    }
    private static function _create_csslist($key, $sel, $pv, &$csslist) {
        $sel = md5(trim($sel));
        $pv  = array_map("trim", preg_split("|[:;]|", $pv));
        $pcnt= count($pv);
        for($i=0; $i<$pcnt; $i += 2){
            if(empty($pv[$i]))
                break;
            $csslist[ $key ][ $sel ][ $pv[$i] ] = $pv[$i+1];
        }
    }

    //selector 内の :not :where :is :has 疑似クラスを退避(カッコ内の,区切りによるセレクタ誤解析回避のため)
    public static function evacuate_pseudo($selector, &$temp) {
        $newsel = '';
        $ofset = 0;
        $maxlen = strlen($selector);
        while($ofset < $maxlen){
            if(preg_match('#(:not|:where|:is|:has)\(#u', substr($selector, $ofset), $omatch)){
                $open = strpos($selector, $omatch[0], $ofset) + strlen($omatch[0]);
                if($ofset < $open){
                    $newsel .= substr($selector, $ofset, $open - $ofset);
                }
                $ofset = $open;
                $depth = 1;
                while($depth > 0){
                    if(preg_match('#(\(|\))#u', substr($selector, $ofset), $cmatch)){
                        $ofset = strpos($selector, $cmatch[0], $ofset) + strlen($cmatch[0]);
                        if($cmatch[1] == '('){
                            $depth++;
                        } else{
                            $depth--;
                        }
                        if($depth === 0){
                            $replace = substr($selector, $open, $ofset - $open - 1);
                            $pskey = md5($replace);
                            $temp[$pskey] = $replace;
                            $newsel .= $pskey . ')';
                        }
                    } else {
                        $newsel .= substr($selector, $ofset);
                        $ofset = $maxlen;
                        break;
                    }
                }
            } else {
                $newsel .= substr($selector, $ofset);
                break;
            }
        }
        return $newsel;
    }

    //selector 内の :not :where :is :has 疑似クラスを復元
    public static function restore_pseudo($selector, &$temp) {
        if(!empty($temp)){
            $selector = preg_replace_callback( '#(:not|:where|:is|:has)\((.*?)\)#u', function($match) use(&$temp) {
                $kv = $match[2];
                if(!empty($kv) && isset($temp[ $kv ])){
                    $kv = $temp[ $kv ];
                }
                return $match[1] . '(' . $kv . ')';
            }, $selector);
        }
        return $selector;
    }

    //CSS から未使用の id, class, tag を含む定義を削除
    private static function tree_shaking($css, $customcsslist=array(), $atkey='') {
        $ncss = preg_replace_callback( self::SHAKING_PATTERN, function($mstyle) use(&$customcsslist, &$atkey) {
            $data = $mstyle[0];
            if(!empty($mstyle['atname'])){
                $atkey = md5(trim($mstyle['pref'] . $mstyle['atname']));
                $atcss = self::tree_shaking( $mstyle['atstyle'], $customcsslist, $atkey );
                $atkey = ''; //key clear
                $data  = (!empty($atcss))? '@' . trim($mstyle['pref'] . $mstyle['atname']) . '{' . $atcss . '}' : '';

            } elseif(!empty($mstyle['sel'])) {
                $sel = $mstyle['sel'];
                $pv  = $mstyle['pv'];
                if(!empty($customcsslist)){
                    //tree shaking 前に上書きカスタムCSSとの重複あれば該当プロパティを削除
                    $atkey = (!empty($atkey))? $atkey : 0;
                    $skey  = md5(trim($sel));
                    if(!empty($customcsslist[$atkey][$skey])){
                        $_pv  = array_map("trim", preg_split("|[:;]|", $pv));
                        $pcnt= count($_pv);
                        for($i=0; $i<$pcnt; $i += 2){
                            if(!empty($_pv[$i]) && isset( $customcsslist[ $atkey ][ $skey ][ $_pv[$i] ] )){
                                $pv = preg_replace( '/(^|;|\s)(' . preg_quote($_pv[$i]) . ')\s?:.*?(url\([^\)]+?\).*?)?(;|$)/u', '$1', $pv, 1 );
                            }
                        }
                        if(!empty($pv)){
                            $pv = preg_replace('/\s+/su', ' ', $pv);
                            $pv = trim($pv);
                        }
                    }
                }
                $pattern = array( 'id'       => '|(#)([\w\-\\%]+)|u',
                                  'class'    => '|(\.)([\w\-\\%]+)|u',
                                  'tag'      => '/(^|[,\s>\+~\(\)\]\|])([\w\-]+)/iu',
                                );

                $pseudo = array();
                $sel = self::evacuate_pseudo($sel, $pseudo);
                $slist = array_map("trim", explode(',', $sel));
                foreach ($slist as $s) {
                    //selector の判定条件から :not :where :is :has を除外 ($_s で判定して削除時は $s で行う)
                    $_s = $s;
                    foreach (array(':not',':where',':is',':has') as $psd) {
                        if(strpos($_s, $psd) !== false){
                            $_s = preg_replace("/$psd\(.+?\)/", '', $_s );
                        }
                    }
                    foreach (array('id','class','tag') as $item) {
                        if(!empty($_s) && preg_match_all( $pattern[$item], $_s, $match)){
                            foreach ($match[2] as $v) {
                                //$jsaddlist 登録名が含まれていれば削除しない（処理を簡略化するため上位層のセレクタのみで判定）
                                if(isset(self::$jsaddlist[$v]))
                                    break;

                                //$cmplist 登録名に含まれていなければ削除
                                $delfg = false;
                                if(!isset(self::$cmplist[$item][$v]) && !preg_match('/^[0-9]+/', $v)){
                                    $delfg = true;
                                } elseif($item === 'tag' && preg_match('#\[type\s?=\s?[\'"](.+?)[\'"]#', $_s, $type)){
                                    if(!isset(self::$cmplist['type'][$type[1]])){
                                        $delfg = true;
                                    }
                                }
                                if($delfg){
                                    $sel = preg_replace( '/(' . preg_quote($s) . ')(,|$)/u', '$2', $sel, 1 );
                                    $_s  = '';
                                    break;
                                }
                            }
                        }
                    }
                    if(!empty($_s)){
                        // id / css 属性セレクタ限定の部分マッチチェック
                        if(preg_match_all( '#\[(?<attr>class|id)(?<type>\^|\$|\*)?=["\'](?<str>[\w\-]+?)["\'].*?\]#', $_s, $smatch, PREG_SET_ORDER)){
                            foreach ($smatch as $item) {
                                $attr = $item['attr'];
                                $type = $item['type'];
                                if($type == '^'){
                                    $str = $type . $item['str'];
                                } elseif($type == '$'){
                                    $str = $item['str'] . $type;
                                } elseif($type == '*'){
                                    $str = $item['str'];
                                } else {
                                    $str = '^' . $item['str'] . '$';
                                }
                                //属性セレクタにマッチしなければ削除　
                                if ( ($ret = strpos(self::$cmplist[ $attr . '_str' ], $str )) === false){
                                    $sel = preg_replace( '/(' . preg_quote($s) . ')(,|$)/u', '$2', $sel, 1 );
                                    $_s  = '';
                                    break;
                                }
                            }
                        }
                    }
                }
                $sel = self::restore_pseudo($sel, $pseudo);

                if(!empty($sel)){
                    $sel = preg_replace('/,+/su', ',', $sel);
                    $sel = preg_replace('/\s+/su', ' ', $sel);
                    $sel = trim($sel);
                    $sel = trim($sel, ',');
                }
                $data = (!empty($sel) && !empty($pv))? $sel . '{'. $pv .'}' : '';
            }
            return $data;
        }, $css);
        return $ncss;
    }

    //未使用変数定義の削除（tree_shaking 実行後に実施する）
    //CSSファイルが分割されていると使われている変数定義まで削除の可能性があるので amp-custom style に限定した使用を想定
    public static function tree_shaking4var($css) {
        $varlist = array();
        if(preg_match_all( "/var\((\-\-[^\s\),]+?)[\s\),]/iu", $css, $vmatches)){
            foreach ($vmatches[1] as $v) {
                $v = trim($v);
                if(!isset($varlist[$v])){
                    $varlist[$v] = 1;
                }
            }
        }
        //url() 定義時は文字列中に ; が含まれている場合あり
        $pattern = '/(\-\-[\w\-]+?):.*?(url\([^\)]+?\).*?)?([;\}])/u';
        $css = preg_replace_callback( $pattern, function($match) use(&$varlist) {
            $vdef = $match[0];
            $var  = trim($match[1]);
            if(!isset($varlist[ $var ])){
                $vdef = ($match[3] === '}')? '}' : '';
            }
            return $vdef;
        }, $css);
        return $css;
    }

    /*=============================================================
     * CSS 内のコメント、改行、空白等を削除するだけのシンプルな縮小化
     */
    public static function simple_minify( $css ) {
        $css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css );
        $css = str_replace(array("\r", "\n"), '', $css);
        $css = str_replace("\t", ' ', $css);
        $css = preg_replace('/\s+/su', ' ', $css);
        $css = preg_replace('/\s([\{\}=,\)\/;>])/', '$1', $css);
        $css = preg_replace('/([\{\}:=,\(\/!;])\s/', '$1', $css);
        return $css;
    }

    /*=============================================================
     * css(simple_minify済)の未使用 id, class, tag に関する定義を取り除き縮小化
     */
    public static function extended_minify($css, $html, $jsaddlist=array(), $varminify=false, $customcsslist=array()) {
        self::$jsaddlist = array();
        if(!empty($jsaddlist)){   //JS によりDOMロード後に追加される ID や class 等が除外されなくするための名前の事前登録用
            foreach ($jsaddlist as $tag) {
                if(!isset(self::$jsaddlist[$tag])){
                    self::$jsaddlist[$tag] = 1;
                }
            }
        }

        if(empty(self::$cmplist['parse'])){
            self::$cmplist['parse'] = true;
            self::$cmplist['id_str'] = '';
            self::$cmplist['class_str'] = '';

			$inidata           = array(
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
					'noscript',
				),
			);
			$inidata           = apply_filters( 'css_tree_shaking_exclude', $inidata );

            foreach (array('id','class','tag') as $item) {
                foreach ($inidata[$item] as $tag) {
                    if(!isset(self::$cmplist[$item][$tag])){
                        self::$cmplist[$item][$tag] = 1;
                    }
                }
            }
            //HTML から使用している tag, id, class 抽出
            $pattern = '#<style(?<catrb>.*?)>.*?<\/style|<script(?<satrb>.*?)>.*?<\/script|<!--.+?-->|<(?<tag>[\w\-]+)(?<tatrb>.*?)>#si';
            if(preg_match_all( $pattern, $html, $items, PREG_SET_ORDER)){
                foreach ($items as $item) {
                    $atrb = '';
                    if(!empty($item['tag'])){
                        if(!isset(self::$cmplist['tag'][$item['tag']])){
                            self::$cmplist['tag'][$item['tag']] = 1;
                        }
                        $atrb = $item['tatrb'];
                        //type ほぼ input のみと思われる
                        if(preg_match('#type\s?=\s?[\'"](.+?)[\'"]#u', $atrb, $type)){
                            if(!isset(self::$cmplist['type'][$type[1]])){
                                self::$cmplist['type'][$type[1]] = 1;
                            }
                        }
                    } elseif(!empty($item['catrb'])) {
                        $atrb = $item['catrb'];
                    } elseif(!empty($item['satrb'])) {
                        $atrb = $item['satrb'];
                    }
                    $atrb = trim($atrb);
                    if(!empty($atrb)){
                        if(preg_match('#id\s?=\s?[\'"](.+?)[\'"]#u', $atrb, $id)){
                            $arr = array_map("trim", explode(' ', $id[1]));
                            foreach($arr as $id){
                                $id = trim($id);
                                if(!isset(self::$cmplist['id'][$id])){
                                    self::$cmplist['id'][$id] = 1;
                                    self::$cmplist['id_str'] .= '^' . $id . '$';
                                }
                            }
                        }
                        if(preg_match('#class\s?=\s?[\'"](.+?)[\'"]#u', $atrb, $cls)){
                            $arr = array_map("trim", explode(' ', $cls[1]));
                            foreach($arr as $cls){
                                $cls = trim($cls);
                                if(!isset(self::$cmplist['class'][$cls])){
                                    self::$cmplist['class'][$cls] = 1;
                                    self::$cmplist['class_str'] .= '^' . $cls . '$';
                                }
                            }
                        }
                    }
                }
            }
        }
        $css = self::tree_shaking( $css, $customcsslist );
        if($varminify){
            $css = self::tree_shaking4var( $css );
        }
        return $css;
    }
}
