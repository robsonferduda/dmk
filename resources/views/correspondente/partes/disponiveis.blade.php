
{{-- Paginação no topo --}}
<div id="paginacao-links-top" class="text-center">
    {!! $correspondentes->links() !!}
</div>
<div id="correspondente-cards" class="row">
    @foreach($correspondentes as $correspondente)
                    <div class="col-xs-12 col-sm-12 col-md-6">
                        <div class="well shadow-hover" style="border-radius: 10px; padding: 15px; background: #fff; display: flex; gap: 15px;">
                            
                            {{-- Foto --}}
                            <div style="flex: 0 0 80px;">
                                <figure style="text-align: center;">
                                    <img src="{{ (!empty($correspondente->entidade) && file_exists('public/img/users/ent'.$correspondente->entidade->cd_entidade_ete.'.png')) 
                                            ? asset('img/users/ent'.$correspondente->entidade->cd_entidade_ete.'.png') 
                                            : asset('img/users/user.png') }}"
                                        alt="Foto de Perfil" 
                                        class="img-circle" 
                                        style="width: 70px; height: 70px; object-fit: cover; border: 2px solid #ccc;">
                                    <button class="btn btn-success btn-xs" style="margin-top: 8px;">
                                        <i class="fa fa-send"></i> Convidar
                                    </button>
                                </figure>
                            </div>

                            {{-- Informações --}}
                            <div style="flex: 1;">
                                <h5 style="margin-top: 0; margin-bottom: 5px;">
                                    <strong>{{ $correspondente->nm_razao_social_con }}</strong>
                                </h5>
                                <p style="margin: 0 0 5px; font-size: 13px;">
                                    <i class="fa fa-phone"></i> (99) 99999-9999
                                </p>
                                <p style="margin: 0 0 5px; font-size: 13px;">
                                    <i class="fa fa-envelope"></i> 
                                    {{ $correspondente->entidade->usuario->email ?? 'Não informado' }}
                                </p>
                                <p style="margin: 0; font-size: 13px;">
                                    <i class="fa fa-map-marker"></i> Comarca: Florianópolis
                                </p>
                            </div>

                        </div>
                    </div>
                @endforeach
</div>
<div id="paginacao-links-bottom" class="text-center">
    {!! $correspondentes->links() !!}
</div>