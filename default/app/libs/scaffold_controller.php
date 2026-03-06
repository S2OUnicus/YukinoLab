<?php

/**
 * モデル用の CRUD を素早く構築するためのベースコントローラ
 *
 * @category Kumbia
 * @package Controller
 */
abstract class ScaffoldController extends AdminController
{
    /** @var string views/_shared/scaffolds/ 内のフォルダ名 */
    public string $scaffold = 'kumbia';
    /** @var string CamelCase でのモデル名 */
    public string $model = '';

    /**
     * ページネーションされた結果を表示
     *
     * @param int $page 表示するページ番号
     */
    public function index($page = 1)
    {
        $this->data = (new $this->model)->paginate("page: $page", 'order: id desc');
    }

    /**
     * レコードを作成
     */
    public function crear()
    {
        if (Input::hasPost($this->model)) {

            $obj = new $this->model;
            // 保存処理が失敗した場合
            if (!$obj->save(Input::post($this->model))) {
                Flash::error('処理に失敗しました');
                // フォームに入力された値を保持する
                $this->{$this->model} = $obj;
                return;
            }
            Redirect::to();
            return;
        }
        // autoForm 用にのみ必要
        $this->{$this->model} = new $this->model;
    }

    /**
     * レコードを編集
     *
     * @param int $id レコードの識別子
     */
    public function editar($id)
    {
        View::select('crear');

        // POST でデータが送信されたかを確認
        if (Input::hasPost($this->model)) {
            $obj = new $this->model;
            if (!$obj->update(Input::post($this->model))) {
                Flash::error('処理に失敗しました');
                // フォームに入力された値を保持する
                $this->{$this->model} = Input::post($this->model);
            } else {
                Redirect::to();
                return;
            }
        }

        // 編集開始のためにオブジェクトの自動読み込みを適用
        $this->{$this->model} = (new $this->model)->find((int) $id);
    }

    /**
     * レコードを削除
     *
     * @param int $id レコードの識別子
     */
    public function borrar($id)
    {
        if (!(new $this->model)->delete((int) $id)) {
            Flash::error('処理に失敗しました');
        }
        // 一覧表示のため index へリダイレクト
        Redirect::to();
    }

    /**
     * レコードを表示
     *
     * @param int $id レコードの識別子
     */
    public function ver($id)
    {
        $this->data = (new $this->model)->find_first((int) $id);
    }
}
