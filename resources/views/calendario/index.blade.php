@extends('layouts.admin')
@section('content')
<div id="ribbon">
    <ol class="breadcrumb">
        <li><a href="{{ url('home') }}">Início</a></li>
        <li>Correspondentes</li>
        <li>Relatórios</li>
    </ol>
</div>
<div id="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-file-o"></i> Calendário <span></span>
            </h1>
        </div>
    </div>
    <div class="row">
 
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			{!! $calendar->calendar() !!}
        </article>

    </div>
</div>
@endsection
@section('script')
	{!! $calendar->script() !!}

	<script type="text/javascript">
		$('#calendar').fullCalendar({
	      lang: 'es'
	    });
	</script>
	
@endsection