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
 * @package    Controller
 *
 * @copyright  Copyright (c) 2005 - 2023 KumbiaPHP Team (http://www.kumbiaphp.com)
 * @license    https://github.com/KumbiaPHP/KumbiaPHP/blob/master/LICENSE   New BSD License
 */

/**
 * Kumbia コントローラの基底クラス
 *
 * すべてのアプリケーションコントローラはこのクラスを継承します。
 *
 * @category   Kumbia
 * @package    Controller
 */
#[\AllowDynamicProperties]
abstract class Controller
{

    /**
     * 現在のモジュール名
     *
     * @var string
     */
    public string $module_name;

    /**
     * 現在のコントローラ名
     *
     * @var string
     */
    public string $controller_name;

    /**
     * 現在のアクション名
     *
     * @var string
     */
    public string $action_name;

    /**
     * アクションに渡されたパラメータ
     *
     * @var array
     */
    public array $parameters;

    /**
     * アクションに渡されるパラメータ数を検証するかどうか
     *
     * @var bool
     */
    public $limit_params = true;

    /**
     * ビューに渡すためのデータ
     * 
     * @var mixed
     */
    public $data;

    /**
     * コントローラのコンストラクタ
     *
     * ルーターから渡された情報を元に各種プロパティを初期化し、
     * View の初期化も行います。
     *
     * @param array $args ルーターから渡される情報
     */
    public function __construct(array $args)
    {
        $this->module_name     = $args['module'];
        $this->controller_name = $args['controller'];
        $this->parameters      = $args['parameters'];
        $this->action_name     = $args['action'];
        View::init($args['action'], $args['controller_path']);
    }

    /**
     * アクション実行前フィルタ（前処理）
     *
     * 必要に応じて各コントローラでオーバーライドして使用します。
     *
     * @return false|null false を返すとアクションの実行を中断
     */
    protected function before_filter()
    {
    }

    /**
     * アクション実行後フィルタ（後処理）
     *
     * 必要に応じて各コントローラでオーバーライドして使用します。
     *
     * @return false|void false を返すと後続処理を抑制可能
     */
    protected function after_filter()
    {
    }

    /**
     * コントローラの初期化処理
     *
     * コントローラ生成直後に一度だけ呼ばれます。
     * 必要に応じて各コントローラでオーバーライドして使用します。
     *
     * @return false|void false を返すと before_filter が呼ばれません
     */
    protected function initialize()
    {
    }

    /**
     * コントローラの終了処理
     *
     * レスポンス生成後の後片付けなどに使用します。
     * 必要に応じて各コントローラでオーバーライドして使用します。
     *
     * @return false|void
     */
    protected function finalize()
    {
    }

    /**
     * 各種コールバックフィルタを実行する
     *
     * @param bool $init true の場合は initialize / before_filter を、
     *                   false の場合は after_filter / finalize を実行します
     * @return false|void
     */
    final public function k_callback($init = false)
    {
        if ($init) {
            if ($this->initialize() !== false) {
                return $this->before_filter();
            }
            return false;
        }

        $this->after_filter();
        $this->finalize();
    }

    /**
     * 存在しないメソッドが呼び出された際に実行されるマジックメソッド
     *
     * @param string $name      呼び出されたメソッド名
     * @param array  $arguments 渡された引数
     * @throws KumbiaException  対応するアクションが存在しない場合にスロー
     *
     * @return void
     */
    public function __call($name, $arguments)
    {
        throw new KumbiaException($name, 'no_action');
    }
}
