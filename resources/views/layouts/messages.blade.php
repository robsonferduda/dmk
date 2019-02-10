@if(\Session::has('flash_notification'))
    @foreach (Session::get('flash_notification') as $message)
        <div class="alert alert-{{ $message['level'] }}">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            {!! $message['message'] !!}
        </div>
    @endforeach
@endif    
{{ Session::forget('flash_notification') }}

@if ($errors->any())
	@foreach($errors->all() as $error)

	<div class="alert alert-warning fade in">
		<i class="fa-fw fa fa-warning"></i>
		<strong><font><font>Atenção!</font></font></strong><font><font> {{ $error }}
		</font></font>
	</div>

	@endforeach
@endif