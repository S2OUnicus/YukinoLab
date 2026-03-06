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
 * フォーム用ヘルパークラス
 *
 * @category   KumbiaPHP
 */
class Form
{
    /**
     * ラジオボタンの id を生成するために使用される内部カウンタ
     *
     * @var array
     */
    protected static $radios = array();

    /**
     * Form::file() が使用されているにもかかわらず、
     * フォームが multipart になっていない場合に
     * 開発者へ警告を出すためのフラグ
     *
     * @var bool
     */
    protected static $multipart = false;

    /**
     * フィールド名とフォーム名から値を取得します。
     *
     * form.field 形式の名前に対応し、同名の属性を持つ
     * 文字列／オブジェクト／配列などから値を取得して返します。
     *
     * @param string $field   フィールド名（例: user.name）
     * @param mixed  $value   フィールドのデフォルト値
     * @param bool   $filter  HTML の特殊文字をフィルタするかどうか
     * @param bool   $check   チェックボックスがチェックされているか
     * @param bool   $is_check チェックボックス／ラジオかどうか
     *
     * @return array id, name, value の 3 要素を持つ配列 array(id, name, value) を返します
     */
    public static function getField($field, $value = null, $is_check = false, $filter = true, $check = false)
    {
        // form.field 形式を考慮して分解
        $formField = explode('.', $field, 2);
        [$id, $name] = self::fieldName($formField);
        // まず $_POST を確認
        if (Input::hasPost($field)) {
            $value = $is_check ?
                Input::post($field) == $value : Input::post($field);
        } elseif ($is_check) {
            $value = $check;
        } elseif ($tmp_val = self::getFromModel($formField)) {
            // モデルからの自動ロード
            $value = $is_check ? $tmp_val == $value : $tmp_val;
        }
        // 特殊文字をエスケープ
        if (!$is_check && $value !== null && $filter) {
            if (is_array($value)) {
                $value = self::filterArrayValues($value);
            } else {
                $value = htmlspecialchars($value, ENT_COMPAT, APP_CHARSET);
            }
        }
        // データを返す
        return array($id, $name, $value);
    }

    /**
     * 配列内のすべての値に htmlspecialchars を適用する
     *
     * @param array $array
     * @return array
     */
    private static function filterArrayValues(array $array)
    {
        foreach ($array as &$value) {
            if (is_array($value)) {
                $value = self::filterArrayValues($value);
            } else {
                $value = htmlspecialchars($value, ENT_COMPAT, APP_CHARSET);
            }
        }
        return $array;
    }

    /**
     * モデル（ビュー変数）から値を取得する
     *
     * @param array $formField array [modelo, campo]
     *
     * @return mixed
     */
    protected static function getFromModel(array $formField)
    {
        $form = View::getVar($formField[0]);
        if (is_scalar($form) || is_null($form)) {
            return $form;
        }
        $form = (object) $form;

        return $form->{$formField[1]} ?? null;
    }

    /**
     * フィールドの name と id を返す
     *
     * @param array $field explode 結果の配列
     *
     * @return array array(id, name)
     */
    protected static function fieldName(array $field)
    {
        return isset($field[1]) ?
            array("{$field[0]}_{$field[1]}", "{$field[0]}[{$field[1]}]") : array($field[0], $field[0]);
    }

    /**
     * フィールド名とフォーム名から値を取得します。
     *
     * チェックボックス等を考慮しない通常のフィールド用です。
     *
     * @param string $field  フィールド名
     * @param mixed  $value  フィールドのデフォルト値
     * @param bool   $filter HTML の特殊文字をフィルタするかどうか
     *
     * @return array id, name, value の 3 要素を持つ配列 array(id, name, value) を返します
     */
    public static function getFieldData($field, $value = null, $filter = true)
    {
        return self::getField($field, $value, false, $filter);
    }

    /**
     * チェックボックス／ラジオボタン用のフィールド値を取得します
     *
     * @param string $field      フィールド名
     * @param string $checkValue チェック時の値
     * @param bool   $checked    初期状態でチェックするかどうか
     *
     * @return array id, name, checked の 3 要素を持つ配列を返します
     */
    public static function getFieldDataCheck($field, $checkValue, $checked = false)
    {
        return self::getField($field, $checkValue, true, false, $checked);
    }

    /**
     * 共通タグ生成用の内部メソッド
     *
     * @param string       $tag   タグ名
     * @param string       $field フィールド名
     * @param string|array $attrs 追加属性
     * @param string       $value 初期値
     * @param string       $extra 追加の生属性文字列
     * @param bool         $close 閉じタグを付けるかどうか
     */
    protected static function tag($tag, $field, $attrs = '', $value = '', $extra = '', $close = true)
    {
        $attrs = Tag::getAttrs($attrs);
        $end = $close ? ">{{value}}</$tag>" : '/>';
        // フィールドの id, name, value を取得
        [$id, $name, $value] = self::getFieldData($field, $value);

        return str_replace('{{value}}', (string) $value, "<$tag id=\"$id\" name=\"$name\" $extra $attrs $end");
    }

    /*
     * input 要素を生成します
     *
     * @param string       $type  input の type 属性
     * @param string       $field フィールド名
     * @param string|array $attrs フィールド属性（任意）
     * @param string       $value 初期値
     * @return string
     */
    public static function input($type, $field, $attrs = '', $value = '')
    {
        return self::tag('input', $field, $attrs, $value, "type=\"$type\" value=\"{{value}}\"", false);
    }

    /**
     * form タグを生成します
     *
     * @param string $action フォームの送信先アクション（任意）
     * @param string $method メソッド。デフォルトは post（任意）
     * @param string $attrs  追加属性（任意）
     *
     * @return string
     */
    public static function open($action = '', $method = 'post', $attrs = '')
    {
        $attrs = Tag::getAttrs($attrs);
        if ($action) {
            $action = PUBLIC_PATH . $action;
        } else {
            $action = PUBLIC_PATH . ltrim(Router::get('route'), '/');
        }

        return "<form action=\"$action\" method=\"$method\" $attrs>";
    }

    /**
     * multipart 対応の form タグを生成します
     *
     * @param string       $action フォームの送信先アクション（任意）
     * @param string|array $attrs  追加属性（任意）
     *
     * @return string
     */
    public static function openMultipart($action = null, $attrs = '')
    {
        self::$multipart = true;
        if (is_array($attrs)) {
            $attrs['enctype'] = 'multipart/form-data';
            $attrs = Tag::getAttrs($attrs);
        } else {
            $attrs .= ' enctype="multipart/form-data"';
        }

        return self::open($action, 'post', $attrs);
    }

    /**
     * form の閉じタグを生成します
     *
     * @return string
     */
    public static function close()
    {
        self::$multipart = false;

        return '</form>';
    }

    /**
     * submit ボタンを生成します
     *
     * @param string       $text  ボタンのラベル
     * @param string|array $attrs 追加属性（任意）
     *
     * @return string
     */
    public static function submit($text, $attrs = '')
    {
        return self::button($text, $attrs, 'submit');
    }

    /**
     * reset ボタンを生成します
     *
     * @param string       $text  ボタンのラベル
     * @param string|array $attrs 追加属性（任意）
     *
     * @return string
     */
    public static function reset($text, $attrs = '')
    {
        return self::button($text, $attrs, 'reset');
    }

    /**
     * button 要素を生成します
     *
     * @param string       $text  ボタンのラベル
     * @param array|string $attrs 追加属性（任意）
     * @param string       $type  ボタンタイプ
     * @param string       $value ボタンの value 属性
     *
     * @todo name 属性が無いと value がサーバーに送信されないため、name の追加が必要
     *
     * @return string
     */
    public static function button($text, $attrs = '', $type = 'button', $value = null)
    {
        $attrs = Tag::getAttrs($attrs);
        $value = is_null($value) ? '' : "value=\"$value\"";

        return "<button type=\"$type\" $value $attrs>$text</button>";
    }

    /**
     * label 要素を生成します
     *
     * @param string        $text  表示テキスト
     * @param string        $field 対応するフィールド名（for 属性に使用）
     * @param string|array  $attrs 追加属性（任意）
     *
     * @return string
     */
    public static function label($text, $field, $attrs = '')
    {
        $attrs = Tag::getAttrs($attrs);

        return "<label for=\"$field\" $attrs>$text</label>";
    }

    /**
     * 単一行テキスト入力フィールドを生成します
     *
     * @param string       $field フィールド名
     * @param string|array $attrs 追加属性（任意）
     * @param string       $value 初期値（任意）
     *
     * @return string
     */
    public static function text($field, $attrs = '', $value = null)
    {
        return self::input('text', $field, $attrs, $value);
    }

    /**
     * select 要素を生成します
     *
     * @param string       $field  フィールド名
     * @param array        $data   選択肢の配列
     * @param string|array $attrs  追加属性（任意）
     * @param string|array $value  選択済み値（複数選択時は配列）
     * @param string       $blank  空の項目を追加する場合のラベル（空でなければ追加）
     * @param string       $itemId オブジェクト配列使用時の ID プロパティ名
     * @param string       $show   表示テキストに使用するプロパティ名（空なら toString）
     *
     * @return string
     */
    public static function select($field, $data, $attrs = '', $value = null, $blank = '', $itemId = 'id', $show = '')
    {
        $attrs = Tag::getAttrs($attrs);
        // id, name, value を取得
        [$id, $name, $value] = self::getFieldData($field, $value);
        // 空項目を追加する場合
        $options = empty($blank) ? '' :
            '<option value="">' . htmlspecialchars($blank, ENT_COMPAT, APP_CHARSET) . '</option>';
        foreach ($data as $k => $v) {
            $val = self::selectValue($v, $k, $itemId);
            $text = self::selectShow($v, $show);
            $selected = self::selectedValue($value, $val);
            $options .= "<option value=\"$val\" $selected>$text</option>";
        }

        return "<select id=\"$id\" name=\"$name\" $attrs>$options</select>";
    }

    /**
     * select 要素の value 値を返します
     *
     * @param mixed  $item 配列要素
     * @param string $key  配列のキー
     * @param string $id   オブジェクトの場合の ID プロパティ名
     *
     * @return string
     */
    public static function selectValue($item, $key, $id)
    {
        return htmlspecialchars(
            is_object($item) ? $item->$id : $key,
            ENT_COMPAT,
            APP_CHARSET
        );
    }

    /**
     * select 要素の項目を選択状態にするための属性文字列を返します
     *
     * @param string|array $value 選択されるべき値（複数の場合は配列）
     * @param string       $key   現在の項目の値
     *
     * @return string selected="selected" または空文字列
     */
    public static function selectedValue($value, $key)
    {
        return ((is_array($value) && in_array($key, $value)) || $key === $value) ?
            'selected="selected"' : '';
    }

    /**
     * select 要素の表示テキストを返します
     *
     * @param mixed  $item 配列要素
     * @param string $show オブジェクトの場合の表示プロパティ名
     *
     * @return string
     */
    public static function selectShow($item, $show)
    {
        $value = (is_object($item) && !empty($show)) ? $item->$show : (string) $item;

        return htmlspecialchars($value, ENT_COMPAT, APP_CHARSET);
    }

    /**
     * チェックボックスを生成します
     *
     * @param string       $field      フィールド名
     * @param string       $checkValue チェック時の値
     * @param string|array $attrs      追加属性（任意）
     * @param bool         $checked    初期状態でチェックするかどうか（任意）
     *
     * @return string
     */
    public static function check($field, $checkValue, $attrs = '', $checked = false)
    {
        $attrs = Tag::getAttrs($attrs);
        // id, name, checked を取得
        [$id, $name, $checked] = self::getFieldDataCheck($field, $checkValue, $checked);

        if ($checked) {
            $checked = 'checked="checked"';
        }

        return "<input id=\"$id\" name=\"$name\" type=\"checkbox\" value=\"$checkValue\" $attrs $checked/>";
    }

    /**
     * ラジオボタンを生成します
     *
     * @param string       $field      フィールド名
     * @param string       $radioValue ラジオボタンの値
     * @param string|array $attrs      追加属性（任意）
     * @param bool         $checked    初期状態でチェックするかどうか（任意）
     *
     * @return string
     */
    public static function radio($field, $radioValue, $attrs = '', $checked = false)
    {
        $attrs = Tag::getAttrs($attrs);
        // id, name, checked を取得
        [$id, $name, $checked] = self::getFieldDataCheck($field, $radioValue, $checked);

        if ($checked) {
            $checked = 'checked="checked"';
        }

        // ラジオボタンの連番を管理
        if (isset(self::$radios[$field])) {
            ++self::$radios[$field];
        } else {
            self::$radios[$field] = 0;
        }
        $id .= self::$radios[$field];

        return "<input id=\"$id\" name=\"$name\" type=\"radio\" value=\"$radioValue\" $attrs $checked/>";
    }

    /**
     * 画像を使用した submit ボタンを生成します
     *
     * @param string       $img   画像ファイル名またはパス
     * @param string|array $attrs 追加属性（任意）
     *
     * @return string
     */
    public static function submitImage($img, $attrs = '')
    {
        $attrs = Tag::getAttrs($attrs);

        return '<input type="image" src="' . PUBLIC_PATH . "img/$img\" $attrs/>";
    }

    /**
     * hidden フィールドを生成します
     *
     * @param string       $field フィールド名
     * @param string|array $attrs 追加属性（任意）
     * @param string       $value 値
     *
     * @return string
     */
    public static function hidden($field, $attrs = '', $value = null)
    {
        return self::input('hidden', $field, $attrs, $value);
    }

    /**
     * password フィールドを生成します（非推奨）
     *
     * @deprecated バージョン 1.0 以降は非推奨です。password() を使用してください。
     *
     * @param string       $field フィールド名
     * @param string|array $attrs 追加属性（任意）
     * @param string       $value 値
     */
    public static function pass($field, $attrs = '', $value = null)
    {
        return self::password($field, $attrs, $value);
    }

    /**
     * password フィールドを生成します
     *
     * @param string       $field フィールド名
     * @param string|array $attrs 追加属性（任意）
     * @param string       $value 値
     */
    public static function password($field, $attrs = '', $value = null)
    {
        return self::input('password', $field, $attrs, $value);
    }

    /**
     * オブジェクト配列を元に select 要素を生成します
     *
     * @param string       $field フィールド名
     * @param string       $show  表示に使用するフィールド名（任意）
     * @param array        $data  Array('modelo','metodo','param') 形式（任意）
     * @param string       $blank 空欄として表示するラベル（任意）
     * @param string|array $attrs 追加属性（任意）
     * @param string|array $value 選択済み値（任意・複数選択時は配列）
     *
     * @return string
     */
    public static function dbSelect($field, $show = null, $data = null, $blank = '選択してください', $attrs = '', $value = null)
    {
        $model = ($data === null) ? substr($field, strpos($field, '.') + 1, -3) : $data[0];
        $model = Util::camelcase($model);
        $model_asoc = new $model();
        // デフォルトでは最初の非 PK フィールドを表示用に使用
        $show = $show ?: $model_asoc->non_primary[0];
        $pk = $model_asoc->primary_key[0];
        if ($data === null) {
            // カラム指定＋ソート（配列指定を使う方が望ましい）
            $data = $model_asoc->find("columns: $pk,$show", "order: $show asc");
        } else {
            $data = (isset($data[2])) ?
                $model_asoc->{$data[1]}($data[2]) :
                $model_asoc->{$data[1]}();
        }

        return self::select($field, $data, $attrs, $value, $blank, $pk, $show);
    }

    /**
     * file フィールドを生成します
     *
     * @param string       $field フィールド名
     * @param string|array $attrs 追加属性（任意）
     *
     * @return string
     */
    public static function file($field, $attrs = '')
    {
        // 開発者向けの警告
        if (!self::$multipart) {
            Flash::error('ファイルをアップロードするには、フォームを Form::openMultipart() で開く必要があります');
        }

        $attrs = Tag::getAttrs($attrs);

        // id と name を取得
        [$id, $name] = self::getFieldData($field, false);

        return "<input id=\"$id\" name=\"$name\" type=\"file\" $attrs/>";
    }

    /**
     * textarea フィールドを生成します
     *
     * @param string       $field フィールド名
     * @param string|array $attrs 追加属性（任意）
     * @param string       $value 初期値（任意）
     *
     * @return string
     */
    public static function textarea($field, $attrs = '', $value = '')
    {
        return self::tag('textarea', $field, $attrs, $value);
    }

    /**
     * HTML5 の date フィールドを生成します
     *
     * @param string       $field フィールド名
     * @param string|array $attrs 追加属性（任意）
     * @param string       $value 初期値（任意）
     *
     * @return string
     */
    public static function date($field, $attrs = '', $value = '')
    {
        return self::input('date', $field, $attrs, $value);
    }

    /**
     * JS を利用した日付入力用テキストフィールドを生成します
     *
     * @param string       $field フィールド名
     * @param string       $class CSS クラス名（任意）
     * @param string|array $attrs 追加属性（任意）
     * @param string       $value 初期値（任意）
     *
     * @return string
     */
    public static function datepicker($field, $class = '', $attrs = '', $value = null)
    {
        return self::tag('input', $field, $attrs, null, "class=\"js-datepicker $class\" type=\"text\" value=\"$value\" ");
    }

    /**
     * HTML5 の time フィールドを生成します
     *
     * @param string       $field フィールド名
     * @param string|array $attrs 追加属性（任意）
     * @param string       $value 初期値（任意）
     *
     * @return string
     */
    public static function time($field, $attrs = '', $value = null)
    {
        return self::input('time', $field, $attrs, $value);
    }

    /**
     * HTML5 の datetime-local フィールドを生成します
     *
     * @param string       $field フィールド名
     * @param string|array $attrs 追加属性（任意）
     * @param string       $value 初期値（任意）
     *
     * @return string
     */
    public static function datetime($field, $attrs = '', $value = null)
    {
        return self::input('datetime-local', $field, $attrs, $value);
    }

    /**
     * HTML5 の number フィールドを生成します
     *
     * @param string       $field フィールド名
     * @param string|array $attrs 追加属性（任意）
     * @param string       $value 初期値（任意）
     *
     * @return string
     */
    public static function number($field, $attrs = '', $value = null)
    {
        return self::input('number', $field, $attrs, $value);
    }

    /**
     * HTML5 の url フィールドを生成します
     *
     * @param string       $field フィールド名
     * @param string|array $attrs 追加属性（任意）
     * @param string       $value 初期値（任意）
     *
     * @return string
     */
    public static function url($field, $attrs = '', $value = null)
    {
        return self::input('url', $field, $attrs, $value);
    }

    /**
     * HTML5 の email フィールドを生成します
     *
     * @param string       $field フィールド名
     * @param string|array $attrs 追加属性（任意）
     * @param string       $value 初期値（任意）
     *
     * @return string
     */
    public static function email($field, $attrs = '', $value = null)
    {
        return self::input('email', $field, $attrs, $value);
    }
}
