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
 * @package    Core
 *
 * @copyright  Copyright (c) 2005 - 2023 KumbiaPHP Team (http://www.kumbiaphp.com)
 * @license    https://github.com/KumbiaPHP/KumbiaPHP/blob/master/LICENSE   New BSD License
 */

/**
 * Kumbia のバージョン情報
 *
 * @category   Kumbia
 * @package    Core
 */
const KUMBIA_VERSION = '1.2.1';

/**
 * KumbiaPHP のバージョンを返す
 *
 * @deprecated 1.1 以降は KUMBIA_VERSION 定数を使用してください
 * @return string
 */
function kumbia_version()
{
    return KUMBIA_VERSION;
}
