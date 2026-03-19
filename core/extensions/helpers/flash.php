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
 * ビューにメッセージを送信するクラス
 *
 * 警告・成功・情報・エラーなどのメッセージを
 * ビューに表示するために使用します。
 * コンソールから利用された場合は、コンソールにも
 * メッセージを出力します。
 *
 * @category   Kumbia
 * @package    Flash
 */
class Flash
{

    /**
     * フラッシュメッセージを表示する
     *
     * @param string $name メッセージ種別（CSS class="$name" にも使用される）
     * @param string $text 表示するメッセージ
     */
    public static function show(string $name, string $text): void
    {
        if (isset($_SERVER['SERVER_SOFTWARE'])) {
            echo '<div class="', $name, ' flash">', $text, '</div>', PHP_EOL;
            return;
        }
        // CLI 出力
        echo $name, ': ', strip_tags($text), PHP_EOL;
    }

    /**
     * エラーメッセージを表示する
     *
     * @param string $text 表示するメッセージ
     */
    public static function error(string $text): void
    {
        self::show('error', $text);
    }

    /**
     * 警告メッセージを表示する
     *
     * @param string $text 表示するメッセージ
     */
    public static function warning(string $text): void
    {
        self::show('warning', $text);
    }

    /**
     * 情報メッセージを表示する
     *
     * @param string $text 表示するメッセージ
     */
    public static function info(string $text): void
    {
        self::show('info', $text);
    }

    /**
     * 処理成功メッセージを表示する
     *
     * @param string $text 表示するメッセージ
     */
    public static function valid(string $text): void
    {
        self::show('valid', $text);
    }

}
