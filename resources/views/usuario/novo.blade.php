@extends('layouts.admin')
@section('content')
<div id="ribbon">
    <ol class="breadcrumb">
        <li><a href="{{ url('home') }}">Início</a></li>
        <li><a href="#">Usuários</a></li>
        <li>Novo</li>
    </ol>
</div>
<div id="content">
    <div class="row">
        <div class="col-xs-12 col-sm-7 col-md-7 col-lg-4">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-group"></i> Usuários <span>> Novo</span>
            </h1>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
              <article class="col-sm-12 col-md-12 col-lg-12 sortable-grid ui-sortable">
            
            <!-- Widget ID (each widget will need unique ID)-->
            <div class="jarviswidget jarviswidget-sortable" id="wid-id-4" data-widget-editbutton="false" data-widget-custombutton="false" role="widget">
                <!-- widget options:
                    usage: <div class="jarviswidget" id="wid-id-0" data-widget-editbutton="false">
                    
                    data-widget-colorbutton="false" 
                    data-widget-editbutton="false"
                    data-widget-togglebutton="false"
                    data-widget-deletebutton="false"
                    data-widget-fullscreenbutton="false"
                    data-widget-custombutton="false"
                    data-widget-collapsed="true" 
                    data-widget-sortable="false"
                    
                -->
                <header role="heading" class="ui-sortable-handle">
                    <span class="widget-icon"> <i class="fa fa-edit"></i> </span>
                    <h2>Cadastro de Usuário </h2>             
                    
                <span class="jarviswidget-loader"><i class="fa fa-refresh fa-spin"></i></span></header>

                <!-- widget div-->
                <div role="content">
                    
                    <!-- widget edit box -->
                    <div class="jarviswidget-editbox">
                        <!-- This area used as dropdown edit box -->
                        
                    </div>
                    <!-- end widget edit box -->
                    
                    <!-- widget content -->
                    <div class="widget-body no-padding">
                        
                        {!! Form::open(['id' => 'frm-add-usuario', 'url' => 'usuarios', 'class' => 'smart-form']) !!}
                            <header>
                                Dados Básicos
                            </header>

                            <fieldset>
                               
                                <div class="row">
                    
                                    <section class="col col-6">
                                        <label class="label">Nome</label>
                                        <label class="input">
                                            <input type="text" name="name" placeholder="Nome">
                                        </label>
                                    </section>
                                    <section class="col col-6">
                                        <label class="label">Email</label>
                                        <label class="input">
                                            <input type="text" name="email" placeholder="E-mail">
                                        </label>
                                    </section>
                                </div> 

                                <div class="row ">
                                   <section class="col col-3">
                                        <label class="label">Nível</label>
                                        <label class="select"> 
                                            <select name="cd_nivel_niv">
                                                <option value="" >Selecione</option>
                                                @foreach($niveis as $nivel)
                                                    <option value="{{ $nivel->cd_nivel_niv }}" >{{ $nivel->dc_nivel_niv }}</option>
                                                @endforeach
                                              
                                            </select> <i></i> </label>
                                    </section>
                                    <section class="col col-3">
                                        <label class="label">Data de Nascimento</label>
                                        <label class="input">
                                            <input type="text" name="data_nascimento"  placeholder="Data de Nascimento">
                                        </label>
                                    </section>
                                    <section class="col col-3">
                                        <label class="label">Data de Admissão</label>
                                        <label class="input">
                                            <input type="text" name="data_admissao" placeholder="Data de Admissão">
                                        </label>
                                    </section>
                                                                        
                                </div>                                   
                                
                            </fieldset>

                            <header>
                                <i class="fa fa-file-o"></i> Documentos 
                            </header>

                            <fieldset>

                                <div class="row">
                    
                                    <section class="col col-3">
                                        <label class="label">N º OAB</label>
                                        <label class="input">
                                            <input type="text" name="oab" placeholder="N º OAB">
                                        </label>
                                    </section>
                                    <section class="col col-3">
                                        <label class="label">CPF</label>
                                        <label class="input">
                                            <input type="text" name="cpf" placeholder="CPF">
                                        </label>
                                    </section>
                                </div> 
                            </fieldset>

                            <header>
                                <i class="fa fa-phone"></i> Contatos
                            </header>
                            <fieldset>
                                <div class="row">
                                    <section class="col col-6">
                                        <label class="input">
                                            <input type="text" name="firstname" placeholder="First name">
                                        </label>
                                    </section>
                                    <section class="col col-6">
                                        <label class="input">
                                            <input type="text" name="lastname" placeholder="Last name">
                                        </label>
                                    </section>
                                </div>
                                
                                <div class="row">
                                    <section class="col col-6">
                                        <label class="select">
                                            <select name="gender">
                                                <option value="0" selected="" disabled="">Gender</option>
                                                <option value="1">Male</option>
                                                <option value="2">Female</option>
                                                <option value="3">Prefer not to answer</option>
                                            </select> <i></i> </label>
                                    </section>
                                    <section class="col col-6">
                                        <label class="input"> <i class="icon-append fa fa-calendar"></i>
                                            <input type="text" name="request" placeholder="Request activation on" class="datepicker hasDatepicker" data-dateformat="dd/mm/yy" id="dp1549825195233">
                                        </label>
                                    </section>
                                </div>  

                            </fieldset>
                            <footer>
                                <button type="submit" class="btn btn-primary">
                                    Validate Form
                                </button>
                            </footer>
                        {!! Form::close() !!}                      
                        
                    </div>
                    <!-- end widget content -->
                    
                </div>
                <!-- end widget div -->
                
            </div>
            </article>
        </div>
    </div>
</div>
@endsection
