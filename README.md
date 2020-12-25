# Typecho B站追番插件

> 感谢原插件创作者[广树](https://www.wikimoe.com/?post=136)，以及修改为B站追番的[飞蚊话](https://www.bwsl.wang/csother/85.html)两位大佬，我所做的就只是解决了一点点小问题而已。

该插件原作者仓库：https://gitee.com/stsiao/typecho_bangumi_bili



因为自己网站追番页出现了两个`undefined`的`notice`，故作对应修复。

修改历程：https://katcloud.cn/archives/19



具体错误：

```php
Notice: Undefined index: pn in C:\xampp\htdocs\usr\plugins\BiliBangumi\Action.php on line 337
    
Notice: Undefined property: stdClass::$long_title in C:\xampp\htdocs\usr\plugins\BiliBangumi\Action.php on line 217
```

只需要将`Action.php`复制入`BiliBangumi`插件文件夹中替换即可。记得备份原文件。



