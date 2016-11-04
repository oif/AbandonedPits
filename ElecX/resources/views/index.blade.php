@extends('app')

@section('content')
    <div class="intro center-block">
        <h1 class="title">ElecX</h1>
        <div class="before">
          <p>宿舍因电费余额不足而停电可能出现一下情况</p>
          <p>没有UPS的保护心爱台式机的寿命就这样损耗了，更重要的是数据的丢失；</p>
          <p>夏天，停电导致空调不能运行。这时候只能说，你若安好，也没卵用；</p>
          <p>洗衣机工作突然停止；</p>
          <p>为了能用上电，一大早起床去缴电费；</p>
          <p>...</p>
        </div>
        <div class="now">
          <p>其实这一切都是可以避免的。</p>
          <p>加入 ElecX 成员计划，让小X在宿舍电费低于10元时候发短信通知你！</p>
          <a class="btn btnWhite" href="/join" style="margin-top:20px">即刻加入</a>
        </div>
    </div>
@endsection