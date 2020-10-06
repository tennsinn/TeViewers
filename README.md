# Viewers

读者数据统计展示插件

## Features

- 提供文章、页面等独立页的点击计数功能以及该计数主题输出接口

## Instructions

### 独立页点击计数

即插即用，计数值存储于table.content中clicksNum字段，最后点击时间存储于clicked字段

### 主题计数输出

点击计数值：

	$this->clicksNum();

最后点击时间：

	$this->clicked();
