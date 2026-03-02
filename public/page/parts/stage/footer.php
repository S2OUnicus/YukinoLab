<span>© <?= date('Y') ?> YUKINO Lab. All Rights Reserved.</span>
<span>
    <?= '（実行時間: ', round((microtime(1) - $_SERVER['REQUEST_TIME_FLOAT']) * 1000, 4), ' ms / メモリ使用量: ', number_format(memory_get_usage() / 1048576, 3), ' MB）'; ?>
</span>