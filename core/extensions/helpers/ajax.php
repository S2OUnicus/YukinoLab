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
 * Ajax を利用するヘルパークラス
 *
 * @category   KumbiaPHP
 * @package    Helpers
 */
class Ajax
{

    /**
     * 指定したアクションへのリンクを生成し、Ajax で指定の要素を更新します
     *
     * @param string       $action アクションへのパス
     * @param string       $text   表示するテキスト
     * @param string       $update 更新対象の要素（ID など）
     * @param string       $class  追加するクラス
     * @param string|array $attrs  追加の属性
     * @return string
     */
    public static function link($action, $text, $update, $class = '', $attrs = '')
    {
        $attrs = Tag::getAttrs($attrs);
        return '<a href="' . PUBLIC_PATH . "$action\" class=\"js-remote $class\" data-to=\"{$update}\" $attrs>$text</a>";
    }

    /**
     * 現在のコントローラー内のアクションへのリンクを生成し、
     * Ajax で指定の要素を更新します
     *
     * @param string       $action アクションへのパス（コントローラー相対）
     * @param string       $text   表示するテキスト
     * @param string       $update 更新対象の要素（ID など）
     * @param string       $class  追加するクラス
     * @param string|array $attrs  追加の属性
     * @return string
     */
    public static function linkAction($action, $text, $update, $class = '', $attrs = '')
    {
        $attrs = Tag::getAttrs($attrs);
        return '<a href="' . PUBLIC_PATH . Router::get('controller_path') . "/$action\" class=\"js-remote $class\" data-to=\"{$update}\" $attrs>$text</a>";
    }

    /**
     * 確認メッセージ付きで、Ajax により要素を更新するリンクを生成します
     *
     * @param string       $action  アクションへのパス
     * @param string       $text    表示するテキスト
     * @param string       $update  更新対象の要素（ID など）
     * @param string       $confirm 確認メッセージ
     * @param string       $class   追加するクラス
     * @param string|array $attrs   追加の属性
     * @return string
     */
    public static function linkConfirm($action, $text, $update, $confirm, $class = '', $attrs = '')
    {
        $attrs = Tag::getAttrs($attrs);
        return '<a href="' . PUBLIC_PATH . "$action\" data-msg=\"$confirm\" class=\"js-remote-confirm $class\" data-to=\"{$update}\" title=\"$confirm\" $attrs>$text</a>";
    }

    /**
     * 確認メッセージ付きで、現在のコントローラー内のアクションへのリンクを生成し、
     * Ajax により要素を更新します
     *
     * @param string       $action  アクションへのパス（コントローラー相対）
     * @param string       $text    表示するテキスト
     * @param string       $update  更新対象の要素（ID など）
     * @param string       $confirm 確認メッセージ
     * @param string       $class   追加するクラス
     * @param string|array $attrs   追加の属性
     * @return string
     */
    public static function linkActionConfirm($action, $text, $update, $confirm, $class = '', $attrs = '')
    {
        $attrs = Tag::getAttrs($attrs);
        return '<a href="' . PUBLIC_PATH . Router::get('controller_path') . "/$action\" data-msg=\"$confirm\" class=\"js-remote-confirm $class\" data-to=\"{$update}\" title=\"$confirm\" $attrs>$text</a>";
    }

    /**
     * Ajax で更新を行うセレクトボックスを生成します
     *
     * @param string       $field  フィールド名
     * @param array        $data   選択肢データ
     * @param string       $update 更新対象の要素（ID など）
     * @param string       $action 実行するアクション
     * @param string       $class  追加するクラス
     * @param string|array $attrs  追加の属性
     */
    public static function select($field, $data, $update, $action, $class = '', $attrs = '')
    {
        $attrs = Tag::getAttrs($attrs);
        // アクションへのパス
        $action = PUBLIC_PATH . rtrim($action, '/') . '/';
        // セレクト要素を生成
        return Form::select($field, $data, "class=\"js-remote $class\" data-update=\"$update\" data-url=\"$action\" $attrs");
    }

    /**
     * オブジェクト配列から値を取得し、Ajax で更新を行うセレクトボックスを生成します
     *
     * @param string       $field  フィールド名
     * @param string       $show   表示に使用するフィールド名
     * @param array        $data   Array('modelo','metodo','param') 形式の配列
     * @param string       $update 更新対象の要素（ID など）
     * @param string       $action 実行するアクション
     * @param string       $blank  空行（ブランク）を表示する場合のラベル
     * @param string       $class  追加するクラス
     * @param string|array $attrs  追加の属性
     */
    public static function dbSelect($field, $show, $data, $update, $action, $blank=null, $class = '', $attrs = '')
    {
        $attrs = Tag::getAttrs($attrs);
        // アクションへのパス
        $action = PUBLIC_PATH . rtrim($action, '/') . '/';

        // セレクト要素を生成
        return Form::dbSelect($field, $show, $data, $blank, "class=\"js-remote $class\" data-update=\"$update\" data-url=\"$action\" $attrs");
    }

    /**
     * Ajax 送信を行うフォームを生成します
     *
     * @param string       $update 更新対象の要素（ID など）
     * @param string       $action 実行するアクション
     * @param string       $class  スタイル用クラス
     * @param string       $method 送信メソッド（GET / POST）
     * @param string|array $attrs  追加の属性
     * @return string
     */
    public static function form($update, $action = '', $class = '', $method = 'post', $attrs = '')
    {
        $attrs = "class=\"js-remote $class\" data-to=\"$update\" " . Tag::getAttrs($attrs);
        return Form::open($action, $method, $attrs);
    }

}
