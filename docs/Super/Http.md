#### 1. 背景介绍
##### 1.1 Http的总体知识点
![](https://ws2.sinaimg.cn/large/006tNc79gy1fn5vamc03sj31kw10ddua.jpg)

#### 2. HTTP的机制和原理 ,[查看](http://www.blogjava.net/zjusuyong/articles/304788.html)
##### 2.1 HTTP的原理
##### 2.2 彻底弄懂HTTP缓存机制及原理，[查看](https://www.cnblogs.com/chenqf/p/6386163.html)
- 使用ETAG机制
![](https://ws4.sinaimg.cn/large/006tNc79gy1fn5v85lnfzj30tq0lswgx.jpg)
- 对缓存进行验证
![](https://ws2.sinaimg.cn/large/006tNc79gy1fn5uujllllj30uk0t443m.jpg)

##### 2.3 HTTP的请求/响应
- 请求
![](https://ws1.sinaimg.cn/large/006tNc79gy1fn64paryibj31do0euae4.jpg)
- 响应
![](https://ws4.sinaimg.cn/large/006tNc79gy1fn64qbwxffj30x80qin3a.jpg)


#### 3. HTTP 遇到的问题
##### 3.1 HTTP的 HTTP幂等性概念和应用
>基于幂等性的解决方案中一个完整的取钱流程被分解成了两个步骤：1.调用create_ticket()获取ticket_id；2.调用idempotent_withdraw(ticket_id, account_id, amount)。虽然create_ticket不是幂等的，但在这种设计下，它对系统状态的影响可以忽略，加上idempotent_withdraw是幂等的，所以任何一步由于网络等原因失败或超时，客户端都可以重试，直到获得结果。如图2所示：

- 创建客户端和服务端的唯一ID
- 根据唯一ID去调用接口的操作。
![image](http://images.cnblogs.com/cnblogs_com/weidagang2046/201106/201106042051069339.png)

##### 3.2 分布式系统如何生成全局唯一的ID，[查看](http://blog.csdn.net/firstblood1/article/details/51924824)
###### 3.2.1 生成全局唯一ID的痛点
- 保证生成的 ID 全局唯一
- 今后数据在多个 Shards 之间迁移不会受到 ID 生成方式的限制
- 生成的 ID 中最好能带上时间信息, 例如 ID 的前 k 位是 Timestamp, 这样能够直接通过对 ID 的前 k 位的排序来对数据按时间排序
- 生成的 ID 最好不大于 64 bits
- 生成 ID 的速度有要求. 例如, 在一个高吞吐量的场景中, 需要每秒生成几万个 ID (Twitter 最新的峰值到达了 143,199 Tweets/s, 也就是 10万+/秒)
整个服务最好没有单点

###### 3.2.1 Twitter Snowflake 解决方案
> 时间序列包含毫秒，使用41位， 10位用于表示节点的个数， 12 bits: 产生的序号。 
这样：使用了63位，最高位0，使用了64位来分配一个全局的值


###### 3.3 字符和字节的区别
> 它们完全不是一个位面的概念，所以两者之间没有“区别”这个说法。不同编码里，字符和字节的对应关系不同：
- ①ASCII码中，一个英文字母（不分大小写）占一个字节的空间，一个中文汉字占两个字节的空间。一个二进制数字序列，在计算机中作为一个数字单元，一般为8位二进制数，换算为十进制。最小值0，最大值255。
- ②UTF-8编码中，一个英文字符等于一个字节，一个中文（含繁体）等于三个字节。
- ③Unicode编码中，一个英文等于两个字节，一个中文（含繁体）等于两个字节。
