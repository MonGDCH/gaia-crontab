# gaia-crontab

`Gaia`框架定时任务工具

### 安装

```bash

composer require mongdch/gaia-crontab

```

- 支持基于数组的`Job`任务定义方式
- 支持基于`Think-ORM`的`Mysql`任务定义方式

注意：使用`Mysql`的任务定义驱动需自行导入`crontab.sql`，并初始化数据库，推荐使用`mongdch/mon-think-orm`