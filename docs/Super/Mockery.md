#### 1. 背景介绍 
> mockery 的主要是什么的？mockery 是非常好玩的mock 工具。 它的主要方式是把每个方法中存在依赖的地方给一些参考值，而不考虑具体的的依赖。 举个例子：

```
1. 计算offer利润的方法 sumProfit()
function sumProfit(){
    
    $revenue = $this->getRevenue();
    $spend = $this->marketingSpend() + $this->userSpend();
    return $revenue - $spend ;
}

2. mockery 就是把这些不确定的方法进行mocker，就是假定 getRevenue和marketingSpend，userSpend的数据是知道的话，这个方法是否有效？
$obj = $this->mockOffer();
$obj->expects($this->once())->method('getRevenue')->andReturn(1000);

$obj = $this->mockOffer();
$obj->expects($this->once())->method('marketingSpend')->andReturn(500);

$obj = $this->mockOffer();
$obj->expects($this->once())->method('userSpend')->andReturn(200);

$offer = new Offer();
$profit = $offer->sumProfit();
$this->assertEquals(300 , $profit);

3. 这样可以把方法实现的中的所有关于方法的假定都可以去掉，然后关注具体的流程是否能够走通，达到单元测试的目的。 非常棒的设计！

```


- 官网，[查看](http://docs.mockery.io/en/latest/getting_started/quick_reference.html)