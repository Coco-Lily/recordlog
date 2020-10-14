recordLog
=========

安装：
----

```bash
composer require "recordlog/recordlog @dev"
```

使用：
---

```php
<?php

use Recordlog\RecordLog;

class Index
{
    public function index()
    {
        // 日志设置
        $config = [
            'path'              => '',          // 日志保存目录，默认runtime->recordlogs
            'max_files'         => 0,           // 最大日志保留天数，超过删除 0无限制
            'file_permission'   => 0666,        // 文件权限
            'channel'           => '影院管理平台' // 日志通道名，平台名称
        ];

        // 准备日志记录器
		$log = new RecordLog($config);
		 
		// 使用日志记录器
		$content = ['title'   => '日志标题',
					'content' => '日志内容'];

		//等同于$log->info('日志Message', $content);
        $log->log('info', '日志Message', $content);
    }
}
```
