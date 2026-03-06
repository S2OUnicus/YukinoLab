<?php
/**
 * KumbiaPHP web & app Framework
 *
 * LICENSE
 *
 * このソースファイルは、同梱されている LICENSE ファイルに記載された
 * New BSD ライセンスの条件に従います。
 *
 * @category   Kumbia
 * @package    Auth
 * @subpackage Adapters  認証アダプタ
 *
 * @copyright  Copyright (c) 2005 - 2023 KumbiaPHP Team
 * @license    https://github.com/KumbiaPHP/KumbiaPHP/blob/master/LICENSE   New BSD License
 */

/**
 * Digest Access Authentication を用いてユーザー認証を行うクラス
 *
 * @category   Kumbia
 * @package    Auth
 * @subpackage Adapters
 * @link http://en.wikipedia.org/wiki/Digest_access_authentication
 */
class DigestAuth implements AuthInterface
{

    /**
     * 認証情報を読み込むファイル名（使用する場合）
     *
     * @var string
     */
    private $filename;

    /**
     * 認証サーバー（使用する場合）
     *
     * @var string
     */
    private $server;

    /**
     * 認証サーバーに接続するためのユーザー名（使用する場合）
     *
     * @var string
     */
    private $username;

    /**
     * 認証サーバーに接続するためのパスワード（使用する場合）
     *
     * @var string
     */
    private $password;

    /**
     * 検証時に見つかった realm
     *
     * @var string
     */
    private $realm;

    /**
     * ファイルリソース
     *
     * @var resource|string
     */
    private $resource;

    /**
     * アダプタのコンストラクタ
     *
     * @param mixed $auth       未使用（インターフェース互換用）
     * @param array $extra_args 追加パラメータ（filename, username, password など）
     *
     * @throws KumbiaException 必須パラメータが指定されていない場合
     */
    public function __construct($auth, $extra_args)
    {
        // 必須パラメータ
        foreach (array('filename') as $param) {
            if (isset($extra_args[$param])) {
                $this->$param = $extra_args[$param];
            } else {
                throw new KumbiaException("パラメータ '{$param}' を指定する必要があります。");
            }
        }
        // 任意パラメータ
        foreach (array('username', 'password') as $param) {
            if (isset($extra_args[$param])) {
                $this->$param = $extra_args[$param];
            }
        }
    }

    /**
     * 認証後に取得できるアイデンティティ情報を返す
     *
     * @return array ['username' => ..., 'realm' => ...]
     */
    public function get_identity()
    {
        return array("username" => $this->username, "realm" => $this->realm);
    }

    /**
     * アダプタを用いてユーザーを認証する
     *
     * @return boolean 認証成功なら true、失敗なら false
     *
     * @throws KumbiaException ファイルが存在しない、または読み込めない場合
     */
    public function authenticate()
    {
        $this->resource = @fopen($this->filename, "r");
        if ($this->resource === false) {
            throw new KumbiaException("ファイル '{$this->filename}' が存在しないか、読み込むことができません");
        }

        $exists_user = false;
        while (!feof($this->resource)) {
            $line = fgets($this->resource);
            $data = explode(":", $line);

            if ($data[0] === $this->username) {
                if (trim($data[2]) === md5($this->password)) {
                    $this->realm = $data[1];
                    $exists_user = true;
                    break;
                }
            }
        }
        return $exists_user;
    }

    /**
     * 認証オブジェクトに追加パラメータを設定する
     *
     * @param array $extra_args ['filename','username','password' など]
     */
    public function set_params($extra_args)
    {
        foreach (array('filename', 'username', 'password') as $param) {
            if (isset($extra_args[$param])) {
                $this->$param = $extra_args[$param];
            }
        }
    }

    /**
     * デストラクタ
     * 開いているファイルリソースがあればクローズする
     */
    public function __destruct()
    {
        @fclose($this->resource);
    }

}
