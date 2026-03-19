<?php
/**
 * KumbiaPHP Web & アプリケーションフレームワーク
 *
 * LICENSE
 *
 * このソースファイルは、同梱されている LICENSE ファイルに記載の
 * New BSD License の条件に従います。
 *
 * @category   Kumbia
 *
 * @copyright  Copyright (c) 2005 - 2023 KumbiaPHP Team (http://www.kumbiaphp.com)
 * @license    https://github.com/KumbiaPHP/KumbiaPHP/blob/master/LICENSE   New BSD License
 */
require_once __DIR__.'/controller.php';

/**
 * REST リクエストを扱うためのコントローラクラス
 *
 * 通常、各アクション名はクライアントが使用した HTTP メソッド名
 * （GET, POST, PUT, DELETE, OPTIONS, HEADERS, PURGE...）と対応します。
 * さらに、put_cancel, post_reset のように
 * 「メソッド名_アクション名」という形式でアクションを追加することもできます。
 *
 * @category Kumbia
 *
 * @author kumbiaPHP Team
 */
abstract class KumbiaRest extends Controller
{
    /**
     * クライアントから送信されたデータを解釈するための入力フォーマット
     *
     * @var string フォーマットの MIME タイプ
     */
    protected $_inputFormat;

    /**
     * MIME タイプ毎にカスタムパーサを定義する配列
     *
     * ここで定義されたパーサを使ってリクエストボディを解釈します。
     * キーに MIME タイプ、値にパースを行うコールバックを指定します。
     * コールバックは「解釈済みデータ」を返す必要があります。
     */
    protected $_inputType = [
        'application/json' => [self::class, 'parseJSON'],
        'application/xml' => [self::class, 'parseXML'],
        'text/xml' => [self::class, 'parseXML'],
        'text/csv' => [self::class, 'parseCSV'],
        'application/x-www-form-urlencoded' => [self::class, 'parseForm']
    ];

    /**
     * クライアントへ返却する出力フォーマット
     *
     * @var string 使用するテンプレート名
     */
    protected $_outputFormat;

    /**
     * 利用可能な出力形式の定義
     *
     * 同じレスポンスを、クライアントの要求に応じて
     * 異なるフォーマットで返すことができます。
     * キーに MIME タイプ、値にテンプレート名（フォーマット名）を指定します。
     */
    protected $_outputType = [
        'application/json' => 'json',
        'application/xml' => 'xml',
        'text/xml' => 'xml',
        'text/csv' => 'csv',
    ];

    /**
     * コンストラクタ
     *
     * @param array $arg ルーターから渡されるパラメータ
     */
    public function __construct($arg)
    {
        parent::__construct($arg);
        $this->initREST();
    }

    /**
     * REST 用の初期設定を行う
     *
     * ルーティングされた情報を元に、入力・出力フォーマットを決定し、
     * 使用するビュー（テンプレート）やアクション名を書き換えます。
     */
    protected function initREST()
    {
        /* 入力フォーマットを決定 */
        $this->_inputFormat = self::getInputFormat();
        /* 出力フォーマットを決定 */
        $this->_outputFormat = self::getOutputFormat($this->_outputType);
        View::select(null, $this->_outputFormat);
        $this->rewriteActionName();
    }

    /**
     * アクション名を書き換える
     *
     * HTTP メソッドに応じて、実際に呼び出すアクション名を変換します。
     */
    protected function rewriteActionName()
    {
        /**
         * 実行されるアクション名を書き換える。
         * これにより、実際には HTTP メソッド名がアクションとして呼ばれます。
         * 例: get(:id), getAll, put, post, delete など。
         */
        $action = $this->action_name;
        $method = strtolower(Router::get('method'));
        $rewrite = "{$method}_{$action}";
        if ($this->actionExist($rewrite)) {
            $this->action_name = $rewrite;

            return;
        }
        if ($rewrite === 'get_index') {
            $this->action_name = 'getAll';

            return;
        }
        $this->action_name = $method;
        // index 以外のアクション名はパラメータとして引き渡す
        $this->parameters = ($action === 'index') ? $this->parameters : [$action] + $this->parameters;
    }

    /**
     * 指定されたアクション $name が存在するか確認する
     *
     * @param string $name アクション名
     *
     * @return bool
     */
    protected function actionExist($name)
    {
        if (method_exists($this, $name)) {
            return (new ReflectionMethod($this, $name))->isPublic();
        }

        return false;
    }

    /**
     * 入力フォーマットに応じてリクエストパラメータを取得する
     *
     * クラスに定義されたパーサ（$_inputType）を使用してリクエストボディを解析します。
     *
     * @return mixed パースされたデータ、または生の入力文字列
     */
    protected function param()
    {
        $input = file_get_contents('php://input');
        $format = $this->_inputFormat;
        /* 指定されたフォーマットに有効なパーサがあるか確認 */
        if (isset($this->_inputType[$format]) && is_callable($this->_inputType[$format])) {
            $result = call_user_func($this->_inputType[$format], $input);
            if ($result) {
                return $result;
            }
        }

        return $input;
    }

    /**
     * クライアントにエラーとメッセージを返す
     *
     * @param string $text  エラーメッセージ
     * @param int    $error HTTP ステータスコード
     *
     * @return array エラー情報を含む配列
     */
    protected function error($text, $error = 400)
    {
        http_response_code((int) $error);

        return ['error' => $text];
    }

    /**
     * クライアントが受け入れ可能なフォーマットを、
     * HTTP_ACCEPT ヘッダから優先度付きで取得する
     *
     * @return array MIME タイプをキー、優先度を値とした配列（優先度の高い順にソート済み）
     */
    protected static function accept()
    {
        /* クライアントが Accept しているフォーマットを格納する配列 */
        $aTypes = [];
        /* 空白を削除し、小文字に変換してから分割 */
        $accept = explode(',', strtolower(str_replace(' ', '', Input::server('HTTP_ACCEPT'))));
        foreach ($accept as $a) {
            $q = 1; /* 優先度が指定されていない場合のデフォルトは 1 */
            if (strpos($a, ';q=')) {
                /* "mime/type;q=X" を "mime/type" と "X" に分割 */
                [$a, $q] = explode(';q=', $a);
            }
            $aTypes[$a] = $q;
        }
        /* 優先度の高い順にソート */
        arsort($aTypes);

        return $aTypes;
    }

    /**
     * JSON をパースする
     *
     * JSON 文字列を連想配列に変換します。
     *
     * @param string $input JSON 文字列
     *
     * @return array|string
     */
    protected static function parseJSON($input)
    {
        return json_decode($input, true);
    }

    /**
     * XML をパースする
     *
     * XML 文字列を SimpleXMLElement オブジェクトに変換します。
     * 必要に応じて、さらに標準化された配列やオブジェクトに変換することも想定されています。
     *
     * @param string $input XML 文字列
     *
     * @return \SimpleXMLElement|null
     */
    protected static function parseXML($input)
    {
        try {
            return new SimpleXMLElement($input);
        } catch (Exception $e) {
            // 何もしない（パース失敗時は null を返す）
        }
    }

    /**
     * CSV をパースする
     *
     * CSV 文字列を数値添字配列の配列に変換します。
     * 各要素が 1 行分のデータになります。
     *
     * @param string $input CSV 文字列
     *
     * @return array
     */
    protected static function parseCSV($input)
    {
        $temp = fopen('php://memory', 'rw');
        fwrite($temp, $input);
        fseek($temp, 0);
        $res = [];
        while (($data = fgetcsv($temp)) !== false) {
            $res[] = $data;
        }
        fclose($temp);

        return $res;
    }

    /**
     * フォーム形式の文字列を配列に変換する
     *
     * @param string $input クエリストリング形式の文字列
     *
     * @return array
     */
    protected static function parseForm($input)
    {
        parse_str($input, $vars);

        return $vars;
    }

    /**
     * 入力の MIME タイプを取得する
     *
     * @return string
     */
    protected static function getInputFormat()
    {
        if (isset($_SERVER['CONTENT_TYPE'])) {
            $str = explode(';', $_SERVER['CONTENT_TYPE']);
            return trim($str[0]);
        }

        return '';
    }

    /**
     * 出力フォーマット名を取得する
     *
     * @param array $validOutput サポートしている出力フォーマットの配列
     *                           （キー: MIME タイプ, 値: フォーマット名）
     *
     * @return string 使用するフォーマット名
     */
    protected static function getOutputFormat(array $validOutput)
    {
        /* クライアントの要求に合う出力フォーマットを探す */
        $accept = self::accept();
        foreach ($accept as $key => $q) {
            if (array_key_exists($key, $validOutput)) {
                return $validOutput[$key];
            }
        }

        // 該当がなければデフォルトで JSON を使用
        return 'json';
    }

    /**
     * クライアントから送信されたすべての HTTP ヘッダを取得する
     *
     * @return array
     */
    protected static function getHeaders()
    {
        return getallheaders();
    }

    /**
     * 本番環境では出力フォーマットに従ってエラーを返す
     *
     * 定義されていないメソッドが呼ばれた際のハンドラ。
     *
     * @return void
     */
    public function __call($name, $arguments)
    {
        if (PRODUCTION) {
            // 本番環境では 404 エラーとして処理
            $this->data = $this->error('リソースが見つかりません', 404);
            return;
        }
        // 開発環境では通常の未定義メソッド扱い（例外）
        parent::__call($name, $arguments);
    }
}
