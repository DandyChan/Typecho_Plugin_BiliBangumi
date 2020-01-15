# Typecho追番插件

#### 更新

- 20.1.15
	- 图片和评论可以兼得了
	- 加载速度提升了至少3倍

#### 项目介绍
插件基于[WikiBangumi](https://www.wikimoe.com/?post=136)(原作者 广树)修改而成，将获取信息的来源更改为B站账户。

#### 修改内容

|      项目       |                 原插件                 |                    修改后                    |
| :-------------: | :------------------------------------: | :------------------------------------------: |
|       api       |                Bangumi                 |                   Bilibili                   |
| 设置项/登录方式 |               账号、密码               |                 UID、cookie                  |
|    追番信息     | 中文番剧名、外文番剧名、首播日期、进度 | 中文番剧名、最后更新的剧集名、首播日期、进度 |
|   进度条-文字   |             10/13、5/未知              |    已看完第13话/共 13 话、看到PV1/未完结     |
|       css       |                   …                    |                      …                       |
|      函数       |                   …                    |                      …                       |

#### 使用方法

1. 克隆或下载
2. 解压到 `usr/plugins`，并重命名成`BilibiliBangumi`
3. Typecho后台，启用插件
4. 追番页-使用RAW主题
	1. 使用RAW主题：将bangumi.php拷贝到主题文件夹
	2. 新建独立页面，模板选择`bangumi`，发布
5. 追番页-其他
	1. 参见 [WikimoeBangumi](https://www.wikimoe.com/?post=136)

---

#### 已知问题

详细说明：参考 [博客](https://www.bwsl.wang/csother/85.html)

效果：参考 [追番页](http://58.87.102.195/bangumi.html)

RAW效果参见 [飞蚊话 - 追番](https://www.bwsl.wang/bangumi.html)

