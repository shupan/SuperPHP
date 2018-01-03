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

#### 2. 为什么需要mock对象呢？
有时候，很难测试被测系统（System Under Test，“被测系统”以下简称SUT），
因为SUT依赖一些不能在测试环境使用的组件。这些组件有可能不可用（如第三方系统），
或者他们不能返回测试中期望的结果，或者是这些组件执行后会带来负面效果（如修改数据库中的数据）。
这时候，就需要mock对象来解决这些问题。Mock对象提供相同的API，供SUT调用，使得SUT可以正常运转。
如果希望在测试中大范围的使用mock对象，对程序开发而言也有要求，程序开发过程中必须依照高内聚，底耦合的策略

#### 3. Mockery 的原理
Mockery 主要是利用PHP的类->方法反射机制, 对于不能够测试的调用方法,可以假设这个方法已经给出了返回值后继续执行。 
我们把对方法的假设作为stub , 这样在测试过程中, 对于存在依赖的方法或者数据都可以使用stub来替代。 
最后mockery启动单元测试的时候,就自动把stub数据执行替代。 从而达到正常的测试和内部的解耦。 