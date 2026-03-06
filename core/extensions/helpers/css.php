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
 * CSS を扱うヘルパークラス
 *
 * @category   KumbiaPHP
 * @package    Helpers
 */
class Css
{
    /**
     * 他の CSS から依存される CSS ファイル
     *
     * @var array
     */
    protected static $_dependencies = array();

    /**
     * 読み込む CSS ファイル一覧
     *
     * @var array
     */
    protected static $_css = array();

    /**
     * CSS ディレクトリ
     *
     * @var string
     */
    protected static $css_dir = 'css/';

    /**
     * テンプレート外から CSS ファイルを登録し、
     * テンプレート内でまとめて読み込めるようにします
     *
     * @param string $file          追加する CSS ファイル名（拡張子なし）
     * @param array  $dependencies  先に読み込まれる必要がある依存 CSS ファイル名配列
     */
    public static function add($file, array $dependencies = [])
    {
        self::$_css[$file] = $file;
        foreach ($dependencies as $file) {
            self::$_dependencies[$file] = $file;
        }
    }

    /**
     * add メソッドで登録されたすべての CSS ファイルを
     * `<link>` タグとして出力します
     *
     * @return string 生成された `<link>` タグの HTML
     */
    public static function inc()
    {
        $css  = self::$_dependencies + self::$_css;
        $html = '';
        foreach ($css as $file) {
            $html .= '<link href="' . PUBLIC_PATH . self::$css_dir . "$file.css\" rel=\"stylesheet\" type=\"text/css\" />" . PHP_EOL;
        }
        return $html;
    }
}
