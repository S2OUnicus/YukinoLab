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
 * JavaScript を利用するヘルパークラス
 *
 * @category   KumbiaPHP
 * @package    Helpers
 */
class Js
{
    /**
     * 他のスクリプトから依存される JavaScript ファイル
     *
     * @var array
     */
    protected static $_dependencies = array();

    /**
     * 読み込む JavaScript ファイル一覧
     *
     * @var array
     */
    protected static $_js = array();

    /**
     * JavaScript のディレクトリ
     *
     * @var string
     */
    protected static $js_dir = 'javascript/';

    /**
     * Kumbia の規約に従い、確認メッセージ付きのリンクを生成します
     *
     * @param string       $action  アクションへのパス
     * @param string       $text    表示テキスト
     * @param string       $confirm 確認メッセージ
     * @param string       $class   リンクに付与する追加クラス
     * @param string|array $attrs   追加属性
     * @return string
     */
    public static function link($action, $text, $confirm = '本当に実行してもよろしいですか？', $class = '', $attrs = '')
    {
        $attrs = Tag::getAttrs($attrs);
        return '<a href="' . PUBLIC_PATH . "$action\" data-msg=\"$confirm\" class=\"js-confirm $class\" $attrs>$text</a>";
    }

    /**
     * Kumbia の規約に従い、確認メッセージ付きで
     * 現在のコントローラ内のアクションへのリンクを生成します
     *
     * @param string       $action  アクション名／パス
     * @param string       $text    表示テキスト
     * @param string       $confirm 確認メッセージ
     * @param string       $class   リンクに付与する追加クラス
     * @param string|array $attrs   追加属性
     * @return string
     */
    public static function linkAction($action, $text, $confirm = '本当に実行してもよろしいですか？', $class = '', $attrs = '')
    {
        $attrs = Tag::getAttrs($attrs);
        return '<a href="' . PUBLIC_PATH . Router::get('controller_path') . "/$action\" data-msg=\"$confirm\" class=\"js-confirm $class\" $attrs>$text</a>";
    }

    /**
     * Kumbia の規約に従い、確認メッセージ付きの submit ボタンを生成します
     *
     * @param string       $text    表示テキスト
     * @param string       $confirm 確認メッセージ
     * @param string       $class   ボタンに付与する追加クラス
     * @param string|array $attrs   追加属性
     * @return string
     */
    public static function submit($text, $confirm = '本当に実行してもよろしいですか？', $class = '', $attrs = '')
    {
        $attrs = Tag::getAttrs($attrs);
        return "<input type=\"submit\" value=\"$text\" data-msg=\"$confirm\" class=\"js-confirm $class\" $attrs/>";
    }

    /**
     * 確認メッセージ付きの画像ボタン（submit image）を生成します
     *
     * @param string       $img     画像ファイル名／パス
     * @param string       $class   ボタンに付与する追加クラス
     * @param string|array $attrs   追加属性
     * @return string
     */
    public static function submitImage($img, $confirm = '本当に実行してもよろしいですか？', $class = '', $attrs = '')
    {
        $attrs = Tag::getAttrs($attrs);
        return "<input type=\"image\" data-msg=\"$confirm\" src=\"" . PUBLIC_PATH . "img/$img\" class=\"js-confirm $class\" $attrs/>";
    }

    /**
     * テンプレートで読み込む JavaScript ファイルを登録します
     *
     * @param string $file         追加するファイル名（拡張子なし）
     * @param array  $dependencies 先に読み込まれる必要がある依存ファイル名の配列
     */
    public static function add($file, $dependencies = array())
    {
        self::$_js[$file] = $file;
        foreach ($dependencies as $file) {
            self::$_dependencies[$file] = $file;
        }
    }

    /**
     * add メソッドで登録されたすべての JavaScript ファイルを
     * `<script>` タグとして出力します
     *
     * @return string
     */
    public static function inc()
    {
        $js = self::$_dependencies + self::$_js;
        $html = '';
        foreach ($js as $file) {
            $html .= '<script type="text/javascript" src="' . PUBLIC_PATH . self::$js_dir . "$file.js" . '"></script>';
        }
        return $html;
    }
}
