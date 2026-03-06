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
 * HTML タグ用ヘルパークラス
 *
 * @category   KumbiaPHP
 */
class Html
{
    /**
     * メタタグを保持する配列
     *
     * @var array
     */
    protected static $_metatags = array();

    /**
     * head 内に出力する link 要素を保持する配列
     *
     * @var array
     */
    protected static $_headLinks = array();

    /**
     * PUBLIC_PATH 定数を使用してリンクを生成します。
     * これにより、常に正しいパスでリンクが動作します。
     *
     * @example <?= Html::link('usuario/crear','Crear usuario') ?>
     * @example コントローラ usuario の crear アクションへのリンクを、
     *          テキスト 'Crear usuario' で出力します。
     * @example <?= Html::link('usuario/crear','Crear usuario', 'class="button"') ?>
     * @example 上記と同じですが、class="button" 属性を追加します。
     *
     * @param string       $action アクションへのパス
     * @param string       $text   表示テキスト
     * @param string|array $attrs  追加属性
     *
     * @return string
     */
    public static function link($action, $text, $attrs = '')
    {
        if (is_array($attrs)) {
            $attrs = Tag::getAttrs($attrs);
        }

        return '<a href="'.PUBLIC_PATH."$action\" $attrs>$text</a>";
    }

    /**
     * 現在のコントローラ内のアクションへのリンクを生成します。
     *
     * @example <?= Html::linkAction('crear/','Crear') ?>
     * @example 現在のコントローラの crear アクションへのリンクを、
     *          テキスト 'Crear' で出力します。
     * @example <?= Html::linkAction('usuario/crear','Crear usuario', 'class="button"') ?>
     * @example 上記と同じですが、class="button" 属性を追加します。
     *
     * @param string       $action アクションへのパス
     * @param string       $text   表示テキスト
     * @param string|array $attrs  追加属性
     *
     * @return string
     */
    public static function linkAction($action, $text, $attrs = '')
    {
        $action = Router::get('controller_path')."/$action";

        return self::link($action, $text, $attrs);
    }

    /**
     * 画像タグを生成します。デフォルトでは public/img/ 配下を参照します。
     *
     * @example <?= Html::img('logo.png','Logo de KumbiaPHP') ?>
     * @example <img src="/img/logo.png" alt="Logo de KumbiaPHP"> を出力します。
     * @example <?= Html::img('logo.png','Logo de KumbiaPHP', 'width="100px" height="100px"') ?>
     * @example <img src="/img/logo.png" alt="Logo de KumbiaPHP" width="100px" height="100px"> を出力します。
     *
     * @param string       $src   public/img/ からの相対パス
     * @param string       $alt   画像の代替テキスト
     * @param string|array $attrs 追加属性
     *
     * @return string
     */
    public static function img($src, $alt = '', $attrs = '')
    {
        return '<img src="'.PUBLIC_PATH."img/$src\" alt=\"$alt\" ".Tag::getAttrs($attrs).'/>';
    }

    /**
     * meta タグを登録します。
     *
     * @param string       $content meta タグの content 属性の値
     * @param string|array $attrs   その他の属性
     */
    public static function meta($content, $attrs = '')
    {
        if (is_array($attrs)) {
            $attrs = Tag::getAttrs($attrs);
        }

        self::$_metatags[] = array('content' => $content, 'attrs' => $attrs);
    }

    /**
     * 登録されている meta タグを出力します。
     *
     * @return string
     */
    public static function includeMetatags()
    {
        $code = '';
        foreach (self::$_metatags as $meta) {
            $code .= "<meta content=\"{$meta['content']}\" {$meta['attrs']}>" . PHP_EOL;
        }
        return $code;
    }

    /**
     * 配列から HTML のリスト（ul または ol）を生成します。
     *
     * @param array        $array リストに表示する要素の配列
     * @param string       $type  デフォルトは 'ul'、'ol' を指定すると番号付きリスト
     * @param string|array $attrs リストタグの属性
     *
     * @return string
     */
    public static function lists($array, $type = 'ul', $attrs = '')
    {
        if (is_array($attrs)) {
            $attrs = Tag::getAttrs($attrs);
        }

        $list = "<$type $attrs>".PHP_EOL;
        foreach ($array as $item) {
            $list .= "<li>$item</li>".PHP_EOL;
        }

        return "$list</$type>".PHP_EOL;
    }

    /**
     * 登録されている CSS を link タグとして出力します。
     *
     * @return string
     */
    public static function includeCss()
    {
        $code = '';
        foreach (Tag::getCss() as $css) {
            $code .= '<link href="'.PUBLIC_PATH."css/{$css['src']}.css\" rel=\"stylesheet\" type=\"text/css\" media=\"{$css['media']}\" />".PHP_EOL;
        }

        return $code;
    }

    /**
     * 外部リソースへの link タグを head 用キューに登録します。
     *
     * @param string       $href  リソースの URL
     * @param string|array $attrs link タグの属性
     */
    public static function headLink($href, $attrs = '')
    {
        if (is_array($attrs)) {
            $attrs = Tag::getAttrs($attrs);
        }

        self::$_headLinks[] = array('href' => $href, 'attrs' => $attrs);
    }

    /**
     * アプリケーション内のアクションへの link タグを登録します。
     *
     * @param string       $action アクションのパス
     * @param string|array $attrs  link タグの属性
     */
    public static function headLinkAction($action, $attrs = '')
    {
        self::headLink(PUBLIC_PATH.$action, $attrs);
    }

    /**
     * public ディレクトリ配下のリソースへの link タグを登録します。
     *
     * @param string       $resource public からのリソースパス
     * @param string|array $attrs    link タグの属性
     */
    public static function headLinkResource($resource, $attrs = '')
    {
        self::headLink(PUBLIC_PATH.$resource, $attrs);
    }

    /**
     * head 用に登録された link タグをすべて出力します。
     *
     * @return string
     */
    public static function includeHeadLinks()
    {
        $code = '';
        foreach (self::$_headLinks as $link) {
            $code .= "<link href=\"{$link['href']}\" {$link['attrs']} />".PHP_EOL;
        }

        return $code;
    }

    /**
     * gravatar.com の画像を表示する img タグを生成します。
     *
     * @example シンプルな使用例: <?= Html::gravatar($email); ?>
     * @example 詳細指定: echo Html::gravatar($email, $name, 20, 'http://www.example.com/default.jpg')
     * @example Gravatar 画像自体をリンクにする例:
     *          echo Html::link(Html::gravatar($email), $url)
     *
     * @param string $email   Gravatar を取得するためのメールアドレス
     * @param string $alt     画像の代替テキスト（デフォルト: gravatar）
     * @param int    $size    画像サイズ（1〜512、デフォルト: 40）
     * @param string $default Gravatar が存在しない場合に使用するデフォルト画像の URL、
     *                        または Gravatar のデフォルト指定値（デフォルト: mm）
     *
     * @return string
     */
    public static function gravatar($email, $alt = 'gravatar', $size = 40, $default = 'mm')
    {
        $grav_url = 'https://secure.gravatar.com/avatar/'.md5(strtolower(trim($email))).'?d='.urlencode($default).'&amp;s='.$size;

        return '<img src="'.$grav_url.'" alt="'.$alt.'" class="avatar" width="'.$size.'" height="'.$size.'" />';
    }
}
