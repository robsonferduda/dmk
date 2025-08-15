
    <div class="jarviswidget" data-widget-editbutton="false">
        <header><h2>Top 5 Correspondentes</h2></header>
        <div>
            <div class="widget-body no-padding">
                <ul class="list-group no-margin">
                    @foreach($correspondentes as $c)
                    <li class="list-group-item">
                        <div class="row">
                            <div class="col-xs-3">
                                @if(file_exists('public/img/users/ent'.$c->cd_entidade_ete.'.png'))
                                    <img src="{{ $c->foto ?? asset('img/user.png') }}" class="img-circle" style="width: 100%;">
                                @else
                                    <a href="" data-toggle="modal" data-target="#upload-image"><img src="{{ asset('img/users/user.png') }}" alt="" style="width: 100%; margin: 0 auto;" class="img-circle img-responsive"></a>
                                @endif
                            </div>
                            <div class="col-xs-9">
                                <h5 class="no-margin"><strong>{{ $c->nm_razao_social_con }}</strong></h5>
                                <p class="no-margin"><i class="fa fa-map-marker {{ ($c->nm_cidade_cde) ? 'text-danger' : '' }}"></i> {{ ($c->nm_cidade_cde) ? $c->nm_cidade_cde : 'Não Informada' }}</p>
                                <p class="text-muted no-margin">{{ $c->total_processos }} Processos</p>
                            </div>
                        </div>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>