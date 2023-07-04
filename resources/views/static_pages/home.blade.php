@extends('layouts.default')

@section('content')
<div class="bg-light p-3 p-sm-5 rounded">
  <h1>欢迎来到我的地盘</h1>
  <p class="lead">
    你现在所看到的是 一片不一样的天地。
  </p>
  <p>
    一切，将从这里开始。
  </p>
  <p>
    <a class="btn btn-lg btn-success" href="{{route('signup')}}" role="button">现在注册</a>
  </p>
</div>
@stop
