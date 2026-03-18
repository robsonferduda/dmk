<div class="">
    <h6><i class="fa fa-lg fa-fw fa-clock-o"></i> ÚLTIMOS ACESSOS</h6>
    <ul class="list-group no-margin">
        @forelse($acessos as $acesso)
            <li class="list-group-item" style="padding: 8px 10px; margin-bottom: 5px;">
                <div class="row" style="display: flex; align-items: center;">
                    <div class="col-xs-2" style="padding-right: 5px;">
                        @if($acesso->user && file_exists(public_path('img/users/ent'.$acesso->user->cd_entidade_ete.'.png')))
                            <img src="{{ asset('img/users/ent'.$acesso->user->cd_entidade_ete.'.png') }}" class="img-circle img-responsive" style="width: 36px; height: 36px; object-fit: cover;">
                        @else
                            <img src="{{ asset('img/users/user.png') }}" class="img-circle img-responsive" style="width: 36px; height: 36px; object-fit: cover;">
                        @endif
                    </div>
                    <div class="col-xs-10">
                        <strong>{{ $acesso->user ? $acesso->user->name : 'Usuário removido' }}</strong>
                        <span class="text-muted pull-right" style="font-size: 11px;">
                            {{ \Carbon\Carbon::parse($acesso->created_at)->format('d/m/Y H:i') }}
                        </span>
                        @if($acesso->user)
                            @php
                                $niveis = [1 => 'Administrador', 2 => 'Colaborador', 3 => 'Correspondente', 4 => 'Cliente'];
                            @endphp
                            <br><small class="text-muted">{{ $niveis[$acesso->user->cd_nivel_niv] ?? 'Desconhecido' }}</small>
                        @endif
                    </div>
                </div>
            </li>
        @empty
            <li class="list-group-item text-muted text-center">Nenhum acesso registrado.</li>
        @endforelse
    </ul>
</div>
