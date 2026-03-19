<?php
/**
 * KumbiaPHP Web & アプリケーションフレームワーク
 *
 * LICENSE
 *
 * このソースファイルは、同梱されている LICENSE ファイルに記載の
 * New BSD License の条件に従います。
 *
 * @category   KumbiaPHP
 * @package    Helpers
 *
 * @copyright  Copyright (c) 2005 - 2023 KumbiaPHP Team (http://www.kumbiaphp.com)
 * @license    https://github.com/KumbiaPHP/KumbiaPHP/blob/master/LICENSE   New BSD License
 */

/**
 * 各種 HTML タグを生成するための基本ヘルパークラス
 *
 * @category   KumbiaPHP
 * @package    Helpers
 */
class Tag
{

    /**
     * スタイルシートの情報を保持する配列
     *
     * @var array
     */
    protected static $_css = array();

    /**
     * メソッドに渡された名前付きパラメータを
     * HTML 属性の文字列へ変換します
     *
     * @param string|array $params 変換対象の引数
     * @return string 変換後の属性文字列
     */
    public static function getAttrs($params)
    {
        if (!is_array($params)) {
            return (string)$params;
        }
        $data = '';
        foreach ($params as $k => $v) {
            $data .= "$k=\"$v\" ";
        }
        return trim($data);
    }

    /**
     * 任意のタグを出力します
     *
     * @param string      $tag     タグ名
     * @param string|null $content タグの中身（null の場合は空要素として出力）
     * @param string|array $attrs  タグに付与する属性
     * @return void
     */
    public static function create($tag, $content = null, $attrs = '')
    {
        if (is_array($attrs)) {
            $attrs = self::getAttrs($attrs);
        }

        if (is_null($content)) {
            echo "<$tag $attrs/>";
            return;
        }

        echo "<$tag $attrs>$content</$tag>";
    }

    /**
     * JavaScript ファイルを読み込むための script タグを生成します
     *
     * @param string  $src   読み込む JavaScript ファイル名（拡張子なし）
     * @param boolean $cache ブラウザキャッシュを使用するかどうか
     * @return string 生成された script タグ
     */
    public static function js($src, $cache = TRUE)
    {
        $src = "javascript/$src.js";
        if (!$cache) {
            $src .= '?nocache=' . uniqid();
        }

        return '<script type="text/javascript" src="' . PUBLIC_PATH . $src . '"></script>';
    }

    /**
     * CSS ファイルを登録します
     *
     * @param string $src   CSS ファイル名（拡張子なし）
     * @param string $media 適用メディア（デフォルト: screen）
     */
    public static function css($src, $media = 'screen')
    {
        self::$_css[] = array('src' => $src, 'media' => $media);
    }

    /**
     * 登録されているスタイルシート情報の配列を取得します
     *
     * @return array
     */
    public static function getCss()
    {
        return self::$_css;
    }

}
