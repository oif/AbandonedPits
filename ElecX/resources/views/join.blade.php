@extends('app')

@section('content')
    <div class="join center-block">
      <h1 class="pull-left">加入 ElecX 成员计划</h1>
      <div class="clearfix"></div>
      @if (count($errors) > 0)
        <div class="alert alert-danger row">
          <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif
      @if(!$flag)
      <form accept-charset="UTF-8" class="form-horizontal" style="margin-top:50px" method="POST" action="{{ URL('/join') }}">
        <div class="form-group">
          <input type="hidden" name="_token" value="{{ csrf_token() }}">
          <label for="nickname" class="col-sm-2 control-label form-label">昵称</label>
          <div class="col-sm-10">
            <input type="text" class="form-region" id="nickname" name="nickname">
          </div>
        </div>
        <div class="form-group" id="location">
          <label for="nickname" class="col-sm-2 control-label form-label">园区</label>
          <div class="col-sm-6">
            <select class="area form-control" name="area"></select>
          </div>
          <div class="col-sm-4">
            <select class="building form-control" name="building"></select>
          </div>
        </div>
        <div class="form-group">
          <label for="dorm" class="col-sm-2 control-label form-label">房号</label>
          <div class="col-sm-10">
            <input type="text" class="form-region" id="dorm" name="dorm">
          </div>
        </div>
        <div class="form-group">
          <label for="phone" class="col-sm-2 control-label form-label">手机号</label>
          <div class="col-sm-10">
            <input type="text" class="form-region" id="phone" name="phone">
          </div>
        </div>
        <div class="form-group">
          <label for="code" class="col-sm-2 control-label form-label">暗号</label>
          <div class="col-sm-10">
            <input type="text" class="form-region" id="code" name="code">
          </div>
        </div>
        <div class="form-group">
          <div class="col-sm-offset-2 col-sm-10">
            <button class="btn btnWhite" type="submit">提交</button>
          </div>
        </div>
      </form>
      @else
      <h2 style="text-aligen:center;margin-top:70px;margin-buttom:70px;">{{ $notice }}</h2>
      @endif
    </div>
    <script src="jquery.cxselect.min.js"></script>
    <script type="text/javascript">
    $.cxSelect.defaults.url = '/dormData.json';
    $.cxSelect.defaults.nodata = '"hidden"(visibility:hidden)';
    $('#location').cxSelect({
        selects: ['area', 'building']
    });
    </script>
@endsection