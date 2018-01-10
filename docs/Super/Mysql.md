#### 1. 背景介绍
##### 1.2 Mysql 介绍
- MySQL™软件提供了一个非常快速，多线程，多用户和健壮的SQL（结构化查询语言）数据库服务器。
##### 1.3 Mysql 架构

![image](https://gss0.bdstatic.com/-4o3dSag_xI4khGkpoWK1HF6hhy/baike/c0%3Dbaike80%2C5%2C5%2C80%2C26/sign=e5e447e9cd11728b24208470a995a8ab/908fa0ec08fa513d1273a4a63d6d55fbb3fbd9e9.jpg)


#### 2. 机制和原理， [查看](http://idiotsky.me/2017/09/29/mysql-optimization-mechanism/)
##### 2.1 Mysql 逻辑架构
![image](http://idiotsky.me/images1/mysql-optimization-mechanism-1.jpg)

##### 2.2 Mysql 具体的查询过程


#### 3. Mysql 数据库集群
![](https://ws1.sinaimg.cn/large/006tKfTcgy1fn12zzpzcoj30no0jiahh.jpg)
##### 3.1 Mysql 源码的安装，[查看](http://blog.csdn.net/xyang81/article/details/51792144)

##### 3.2 Mysql的主从配置，[查看](http://database.51cto.com/art/200607/29199_all.htm)

##### 3.3 搭建mysql 高可用的负载集群，[查看](http://www.cnblogs.com/phpstudy2015-6/p/6706465.html)

#### 4. Mysql 新增特性 & 性能优化 
- 表和索引的分区
- 行级复制
- MySQL 基群基于磁盘的数据支持
- MySQL 集群复制
- 增强的全文本搜索函数
- 增强的信息模式(数据字典)
- 可插入的 API
- 服务器日志表
- XML（标准通用标记语言的子集）/ XPath支持
- 实例管理器
- 表空间备份
- mysql_upgrade 升级程序
- 内部任务/事件调度器
- 新的性能工具和选项如 mysqlslap[3] 


#### 5. Mysql 的工具
可以使用命令行工具管理 MySQL 数据库（命令 mysql 和 mysqladmin)，也可以从 MySQL 的网站下载图形管理工具 MySQL Administrator, MySQL Query Browser 和 MySQL Workbench。
phpMyAdmin是由 php 写成的 MySQ L资料库系统管理程程序，让管理者可用 Web 界面管理 MySQL 资料库。
phpMyBackupPro也是由 PHP 写成的，可以透过 Web 界面创建和管理数据库。它可以创建伪 cronjobs，可以用来自动在某个时间或周期备份 MySQL 数据库。
另外，还有其他的 GUI 管理工具，例如 mysql-front 以及 ems mysql manager, navicat等等。

#### 9. 备注
- mysql 7.5官方文档 ，[查看](https://dev.mysql.com/doc/refman/5.7/en/)
- 搜狐的mysql的镜像文件：，[查看](http://mirrors.sohu.com/mysql/MySQL-5.7/)
