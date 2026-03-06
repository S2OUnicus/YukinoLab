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
 * @copyright  Copyright (c) 2005 - 2024 KumbiaPHP Team (http://www.kumbiaphp.com)
 * @license    https://github.com/KumbiaPHP/KumbiaPHP/blob/master/LICENSE   New BSD License
 */

/**
 * モデルからフォームを自動生成するヘルパークラス
 *
 * @category   KumbiaPHP
 * @package    Helpers
 */
class ModelForm
{
    /**
     * モデル（オブジェクト）からフォームを自動生成する
     *
     * @param object $model  対象となるモデルオブジェクト
     * @param string $action フォーム送信先アクション（省略時は現在のルート）
     */
    public static function create(object $model, string $action = ''): void
    {
        $model_name = $model::class;
        if (!$action) {
            $action = ltrim(Router::get('route'), '/');
        }
        // 将来的に、異なる ORM や json / ini / xml / array 形式などで分岐させるための箇所

        echo '<form action="', PUBLIC_PATH.$action, '" method="post" id="', $model_name, '" class="scaffold">' , PHP_EOL;
        $pk = $model->primary_key[0];
        echo '<input id="', $model_name, '_', $pk, '" name="', $model_name, '[', $pk, ']" class="id" value="', $model->$pk , '" type="hidden">' , PHP_EOL;

        $fields = array_diff($model->fields, [...$model->_at, ...$model->_in, ...$model->primary_key]);

        foreach ($fields as $field) {
            // TODO: フィールドサイズやその他の属性も取得できるようにする
            $tipo = trim(preg_replace('/(\(.*\))/', '', $model->_data_type[$field]));
            $alias = $model->get_alias($field);
            $formId = $model_name.'_'.$field;
            $formName = $model_name.'['.$field.']';

            if (in_array($field, $model->not_null)) {
                echo "<label class=\"required\">$alias" , PHP_EOL;
                $required = ' required';
            } else {
                echo "<label>$alias" , PHP_EOL;
                $required = '';
            }

            switch ($tipo) {
                // 数値型
                case 'tinyint': case 'smallint': case 'mediumint':
                case 'integer': case 'int': case 'bigint':
                case 'float': case 'double': case 'precision':
                case 'real': case 'decimal': case 'numeric':
                case 'year': case 'day': case 'int unsigned':

                    if (str_ends_with($field, '_id')) {
                        // 関連 ID 用フィールドは dbSelect を使用
                        echo Form::dbSelect($model_name.'.'.$field, null, null, '選択してください', $required, $model->$field);
                        break;
                    }

                    echo "<input id=\"$formId\" type=\"number\" name=\"$formName\" value=\"{$model->$field}\"$required>" , PHP_EOL;
                    break;

                case 'date':
                    echo "<input id=\"$formId\" type=\"date\" name=\"$formName\" value=\"{$model->$field}\"$required>" , PHP_EOL;
                    break;

                case 'datetime': case 'timestamp':
                    echo "<input id=\"$formId\" type=\"datetime-local\" name=\"$formName\" value=\"{$model->$field}\"$required>" , PHP_EOL;
                    break;

                case 'enum': case 'set': case 'bool':
                    $enumList = explode(',', str_replace("'", '', substr($model->_data_type[$field], 5, (strlen($model->_data_type[$field]) - 6))));
                    echo "<select id=\"$formId\" class=\"select\" name=\"$formName\" >", PHP_EOL;
                    foreach ($enumList as $value) {
                        echo "<option value=\"{$value}\">$value</option>", PHP_EOL;
                    }
                    echo '</select>', PHP_EOL;
                    break;

                // 長文・バイナリなどは textarea を利用
                case 'text': case 'mediumtext': case 'longtext':
                case 'blob': case 'mediumblob': case 'longblob':
                    echo "<textarea id=\"$formId\" name=\"$formName\"$required>{$model->$field}</textarea>" , PHP_EOL;
                    break;

                default:
                    // text, tinytext, varchar, char 等（将来的にサイズに応じた制御も可能）
                    echo "<input id=\"$formId\" type=\"text\" name=\"$formName\" value=\"{$model->$field}\"$required>" , PHP_EOL;
            }
            echo '</label>';
        }
        echo '<input type="submit">' , PHP_EOL;
        echo '</form>' , PHP_EOL;
    }
}
