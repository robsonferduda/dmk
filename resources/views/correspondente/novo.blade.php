@extends('layouts.admin')
@section('content')
<div id="ribbon">
    <ol class="breadcrumb">
        <li><a href="{{ url('home') }}">Início</a></li>
        <li><a href="{{ url('correspondentes') }}">Correspondentes</a></li>
        <li>Novo</li>
    </ol>
</div>
<div id="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-legal"></i> Correspondentes <span> > Novo</span>
            </h1>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 boxBtnTopo">
            <a href="{{ url('correspondentes') }}" class="btn btn-default pull-right header-btn"><i class="fa fa-list"></i> Listar Correspondentes</a>           
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            @include('layouts/messages')
        </div>
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="well" style="margin-left: 15px; margin-right: 15px;">
                <p>
                    <strong class="text-danger">Informações Importantes!</strong><br/>
                    Utilize as opções abaixo para adicionar novos correspondentes a sua lista.
                    Você pode inserir novos correspondentes de duas maneiras: Pela opção "Novo Correspondente", onde via formulário de cadastro você vincula um novo registro a sua lista ou "Enviando Convite", onde o correspondente receberá uma mensagem no email informado para realizar o cadastro e fazer a vinculação a sua conta.
                </p>
            </div>

            <div class="col-md-6">
                <div class="well">
                  <div class="row">
                    <div class="col-md-12">     
                        <h4 style="margin-left: 15px; margin-bottom: 5px; font-weight: bold;">Novo Correspondente</h4>                   
                        {!! Form::open(['id' => 'frm-add-conta', 'url' => 'correspondente/cadastro/conta', 'id' => 'frmAddCorrespondente', 'class' => 'smart-form client-form']) !!}
                        <div class="well" style="margin: 0px 15px; padding: 5px;">
                            <p>
                                <strong class="text-danger">Atenção!</strong><br/>
                                Ao realizar o cadastro, o correpondente é inserido na sua lista de correspondentes e recebe uma mensagem no email informado para confirmar seu cadastro e ter acesso ao sistema. Nesse acesso, além de confirmar 
                                o cadastro ele também cria a senha de acesso ao sistema.
                            </p>
                        </div>
                            <fieldset>
                                    <h5 style="margin-bottom: 10px;"><strong>Dados do Correspondente</strong></h5>
                                    <section>
                                        <label class="input"> <i class="icon-append fa fa-user"></i>
                                        <input type="text" name="nm_razao_social_con" id="nm_razao_social_con" placeholder="Nome" value="{{ old('nm_razao_social_con') }}">
                                        <b class="tooltip tooltip-bottom-right">Nome Completo</b> </label>
                                    </section>
                                    <section>
                                        <label class="input"> <i class="icon-append fa fa-envelope"></i>
                                        <input type="email" name="email" id="email" placeholder="Email" value="{{ old('email') }}">
                                        <b class="tooltip tooltip-bottom-right">Email do Correspondente</b> </label>
                                    </section>
                                </fieldset>
                                <footer>
                                    <button type="submit" class="btn btn-success"><i class="fa fa-user-plus"></i> Adicionar</button>                                 
                                </footer>
                        {!! Form::close() !!} 
                    </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="well">
                    <div class="row">
                        <div class="col-md-12">
                            <h4 style="margin-bottom: 5px; font-weight: bold;">Enviar Convite</h4> 
                            {!! Form::open(['id' => 'frm_envio_convite', 'url' => 'correspondente/convidar', 'class' => 'form-inline']) !!}
                                <div class="well" style="padding: 5px;">
                                    <p>
                                        <strong class="text-danger">Atenção!</strong><br/>
                                        Ao enviar o convite o correspondente receberá uma mensagem, no email informado abaixo, para acessar o sistema e realizar seu cadastro.
                                    </p>
                                </div>
                                <span>Informe o email do correspondente para enviar o convite</span>
                                <div style="margin-top: 8px;">
                                    <div class="form-group" style="width: 50%;">
                                        <input type="text" class="form-control" style="width: 100%;" name="email" id="email" placeholder="Email" value="{{old('email')}}">
                                    </div>                        
                                
                                    <button type="submit" id="btnSalvarTelefone" class="btn btn-success"><i class="fa-fw fa fa-send"></i> Enviar</button>
                                </div>

                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
            </div>
        </article>
    </div>
</div>

@endsection
@section('script')
<script type="text/javascript">

    $(document).ready(function() {

        $('.modal_dados_correspondente').click(function () {
            
            var id = $(this).data("id");

            $.ajax({
                
                url: '../correspondente/dados/'+id,
                type: 'GET',
                dataType: "JSON",
                beforeSend: function(){
                    $("#label > span").empty(); 
                    $('#modalDadosCorrespondente').modal('show');                           
                },
                success: function(response){      

                    $(".data-modal-correspondente-loading").css('display','none');
                    $(".data-modal-correspondente").css('display','block');
                    $("#nm_correspondente span").append(response.dados.nm_razao_social_con);
                                                      
                },
                error: function(response)
                {
                    $(".data-modal-correspondente-loading").css('display','none');
                    alert("Erro ao processar requisição");
                }
            });
        }); 

        $(function() {

            $("#frmAddCorrespondente").validate({
                rules : {
                    nm_razao_social_con : {
                        required : true
                    },
                    email : {
                        required : true
                    }

                },

                messages : {
                    nm_razao_social_con : {
                        required : 'Campo nome é obrigatório'
                    },
                    email : {
                        required : 'Campo email é obrigatório'
                    }
                },
                errorPlacement : function(error, element) {
                    error.insertAfter(element.parent());
                }
            });
        });       

    });
</script>
@endsection