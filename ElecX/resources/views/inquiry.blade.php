@extends('app')

@section('content')
    <div class="join center-block">
      <h1 class="pull-left">查询</h1>
      <div class="clearfix"></div>
      <form class="form-horizontal" style="margin-top:50px" method="post" action="/inquiry">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <div class="form-group">
          <label for="phone" class="col-sm-2 control-label form-label">手机号</label>
          <div class="col-sm-8">
            <input type="text" class="form-region" id="phone" name="phone">
          </div>
          <div class="col-sm-2">
            <button class="btn btnWhite" type="submit">提交</button>
          </div>
        </div>
      </form>
      @if($flag)
      <div style="text-align:center">
        <h3>该账号所绑定的宿舍</h3>
        <h2>{{ $latest }}</h2>
      </div>
      @endif
    </div>
@endsection