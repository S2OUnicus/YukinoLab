<?php
/**
 * KumbiaPHP web & app Framework
 *
 * LICENSE
 *
 * このソースファイルは、同梱されている LICENSE ファイルに記載された
 * New BSD ライセンスの条件に従います。
 *
 * @category   Kumbia        フレームワーク本体
 * @package    Acl           アクセス制御 (ACL)
 * @subpackage AclResource   ACL リソース
 *
 * @copyright  Copyright (c) 2005 - 2023 KumbiaPHP Team
 * @license    https://github.com/KumbiaPHP/KumbiaPHP/blob/master/LICENSE   New BSD License
 */

/**
 * ACL リソース作成用クラス
 *
 * @category   Kumbia
 * @package    Acl
 * @subpackage AclResource
 */
class AclResource
{

    /**
     * リソース名
     *
     * @var string
     */
    public $name;

    /**
     * コンストラクタ
     *
     * @param string $name リソース名
     */
    public function __construct($name)
    {
        if ($name == '*') {
            // "*" は ACL リソース名として無効
            throw new KumbiaException('ACL リソース Acl_Resoruce::__constuct において、"*" は無効な名前です');
        }
        $this->name = $name;
    }

    /**
     * オブジェクトの name プロパティ名を書き換えられないようにする
     *
     * @param string $name  プロパティ名
     * @param string $value 値
     */
    public function __set($name, $value)
    {
        if ($name != 'name') {
            $this->$name = $value;
        }
    }

}
