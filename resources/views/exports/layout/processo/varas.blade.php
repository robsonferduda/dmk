<table>
    <thead>
	    <tr>
            <td style="font-weight: bold;">Varas</td>
	    </tr>
    </thead>
    <tbody>
        @foreach($varas as $vara)
        <tr>
            <td>{{ $vara->nm_vara_var  }} ---{{$vara->nu_vara_var}}---</td>     
        </tr>
        @endforeach
    </tbody>
</table>