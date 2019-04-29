@extends('layouts.admin')
@section('content')

<div class="container">
    <div class="panel panel-default">
      <div class="panel-heading">Laravel crop image before upload using croppie plugins</div>
      <div class="panel-body">


        <div class="row">
            <div class="col-md-4 text-center">
                <div id="upload-demo" style="width:350px"></div>
            </div>
            <div class="col-md-4" style="padding-top:30px;">
                <strong>Select Image:</strong>
                <br/>
                <input type="file" id="upload">
                <br/>
                <button class="btn btn-success upload-result">Upload Image</button>
            </div>


            <div class="col-md-4" style="">
                <div id="upload-demo-i" style="background:#e1e1e1;width:300px;padding:30px;height:300px;margin-top:30px"></div>
            </div>
        </div>


      </div>
    </div>
</div>
@endsection