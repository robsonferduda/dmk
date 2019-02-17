@extends('layouts.admin')
@section('content')
<div id="ribbon">
    <ol class="breadcrumb">
        <li><a href="{{ url('home') }}">Início</a></li>
        <li><a href="{{ url('clientes') }}">Clientes</a></li>
        <li>Novo</li>
    </ol>
</div>
<div id="content">
    <div class="row">
        <div class="col-xs-12 col-sm-7 col-md-7 col-lg-4">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-group"></i> Clientes <span>> Novo</span>
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
                <header role="heading" class="ui-sortable-handle"><div class="jarviswidget-ctrls" role="menu">   <a href="javascript:void(0);" class="button-icon jarviswidget-toggle-btn" rel="tooltip" title="" data-placement="bottom" data-original-title="Collapse"><i class="fa fa-minus "></i></a> <a href="javascript:void(0);" class="button-icon jarviswidget-fullscreen-btn" rel="tooltip" title="" data-placement="bottom" data-original-title="Fullscreen"><i class="fa fa-expand "></i></a> <a href="javascript:void(0);" class="button-icon jarviswidget-delete-btn" rel="tooltip" title="" data-placement="bottom" data-original-title="Delete"><i class="fa fa-times"></i></a></div><div class="widget-toolbar" role="menu"><a data-toggle="dropdown" class="dropdown-toggle color-box selector" href="javascript:void(0);"></a><ul class="dropdown-menu arrow-box-up-right color-select pull-right"><li><span class="bg-color-green" data-widget-setstyle="jarviswidget-color-green" rel="tooltip" data-placement="left" data-original-title="Green Grass"></span></li><li><span class="bg-color-greenDark" data-widget-setstyle="jarviswidget-color-greenDark" rel="tooltip" data-placement="top" data-original-title="Dark Green"></span></li><li><span class="bg-color-greenLight" data-widget-setstyle="jarviswidget-color-greenLight" rel="tooltip" data-placement="top" data-original-title="Light Green"></span></li><li><span class="bg-color-purple" data-widget-setstyle="jarviswidget-color-purple" rel="tooltip" data-placement="top" data-original-title="Purple"></span></li><li><span class="bg-color-magenta" data-widget-setstyle="jarviswidget-color-magenta" rel="tooltip" data-placement="top" data-original-title="Magenta"></span></li><li><span class="bg-color-pink" data-widget-setstyle="jarviswidget-color-pink" rel="tooltip" data-placement="right" data-original-title="Pink"></span></li><li><span class="bg-color-pinkDark" data-widget-setstyle="jarviswidget-color-pinkDark" rel="tooltip" data-placement="left" data-original-title="Fade Pink"></span></li><li><span class="bg-color-blueLight" data-widget-setstyle="jarviswidget-color-blueLight" rel="tooltip" data-placement="top" data-original-title="Light Blue"></span></li><li><span class="bg-color-teal" data-widget-setstyle="jarviswidget-color-teal" rel="tooltip" data-placement="top" data-original-title="Teal"></span></li><li><span class="bg-color-blue" data-widget-setstyle="jarviswidget-color-blue" rel="tooltip" data-placement="top" data-original-title="Ocean Blue"></span></li><li><span class="bg-color-blueDark" data-widget-setstyle="jarviswidget-color-blueDark" rel="tooltip" data-placement="top" data-original-title="Night Sky"></span></li><li><span class="bg-color-darken" data-widget-setstyle="jarviswidget-color-darken" rel="tooltip" data-placement="right" data-original-title="Night"></span></li><li><span class="bg-color-yellow" data-widget-setstyle="jarviswidget-color-yellow" rel="tooltip" data-placement="left" data-original-title="Day Light"></span></li><li><span class="bg-color-orange" data-widget-setstyle="jarviswidget-color-orange" rel="tooltip" data-placement="bottom" data-original-title="Orange"></span></li><li><span class="bg-color-orangeDark" data-widget-setstyle="jarviswidget-color-orangeDark" rel="tooltip" data-placement="bottom" data-original-title="Dark Orange"></span></li><li><span class="bg-color-red" data-widget-setstyle="jarviswidget-color-red" rel="tooltip" data-placement="bottom" data-original-title="Red Rose"></span></li><li><span class="bg-color-redLight" data-widget-setstyle="jarviswidget-color-redLight" rel="tooltip" data-placement="bottom" data-original-title="Light Red"></span></li><li><span class="bg-color-white" data-widget-setstyle="jarviswidget-color-white" rel="tooltip" data-placement="right" data-original-title="Purity"></span></li><li><a href="javascript:void(0);" class="jarviswidget-remove-colors" data-widget-setstyle="" rel="tooltip" data-placement="bottom" data-original-title="Reset widget color to default">Remove</a></li></ul></div>
                    <span class="widget-icon"> <i class="fa fa-edit"></i> </span>
                    <h2>Cadastro de Cliente </h2>             
                    
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
                        
                        <form id="smart-form-register" class="smart-form" novalidate="novalidate">
                            <header>
                                Dados Básicos
                            </header>

                            <fieldset>
                                <section>
                                    <div class="inline-group">
                                        <label class="radio">
                                            <input type="radio" class="tipo-pessoa" name="tipo-pessoa" value="2" checked="checked">
                                            <i></i>Pessoa Jurídica</label>
                                        <label class="radio">
                                            <input type="radio" class="tipo-pessoa" name="tipo-pessoa" value="1">
                                            <i></i>Pessoa Física</label>
                                    </div>
                                </section>
                                <div class="row box-pessoa-juridica">
                                    <section class="col col-1">
                                        <label class="label">Código</label>
                                        <label class="input">
                                            <input type="text" name="firstname" disabled placeholder="Código">
                                        </label>
                                    </section>
                                    <section class="col col-2">
                                        <label class="label">CNPJ</label>
                                        <label class="input">
                                            <input type="text" name="firstname" placeholder="CNPJ">
                                        </label>
                                    </section>
                                    <section class="col col-9">
                                        <label class="label">Cliente (Nome Fantasia)</label>
                                        <label class="input">
                                            <input type="text" name="lastname" placeholder="Nome Fantasia">
                                        </label>
                                    </section>
                                </div> 

                                <div class="row box-pessoa-fisica">
                                    <section class="col col-1">
                                        <label class="label">Código</label>
                                        <label class="input">
                                            <input type="text" name="firstname" disabled placeholder="Código">
                                        </label>
                                    </section>
                                    <section class="col col-2">
                                        <label class="label">CPF</label>
                                        <label class="input">
                                            <input type="text" name="firstname" placeholder="CNPJ">
                                        </label>
                                    </section>
                                    <section class="col col-9">
                                        <label class="label">Nome</label>
                                        <label class="input">
                                            <input type="text" name="lastname" placeholder="Nome Fantasia">
                                        </label>
                                    </section>
                                </div>                                   
                                
                                <div class="row">
                                    <section class="col col-4">
                                        <label class="label">Razão Social</label>
                                        <label class="input">
                                            <input type="text" name="firstname" placeholder="Razão Social">
                                        </label>
                                    </section>
                                    <section class="col col-4">
                                        <label class="label">Inscrição Municipal</label>
                                        <label class="input">
                                            <input type="text" name="lastname" placeholder="Inscrição Municipal">
                                        </label>
                                    </section>
                                    <section class="col col-4">
                                        <label class="label">Inscrição Estadual</label>
                                        <label class="input">
                                            <input type="text" name="lastname" placeholder="Inscrição Estadual">
                                        </label>
                                    </section>
                                </div>  

                                <section>
                                    <div class="onoffswitch-container">
                                        <span class="onoffswitch-title">Pagamento Com Nota Fiscal</span> 
                                        <span class="onoffswitch">
                                            <input type="checkbox" class="onoffswitch-checkbox" id="autoopen">
                                            <label class="onoffswitch-label" for="autoopen"> 
                                                <span class="onoffswitch-inner" data-swchon-text="SIM" data-swchoff-text="NÃO"></span> 
                                                <span class="onoffswitch-switch"></span>
                                            </label> 
                                        </span> 
                                    </div>
                                </section>

                            </fieldset>

                            <header>
                                <i class="fa fa-map-marker"></i> Endereço 
                            </header>

                            <fieldset>

                                <div class="row">
                                    <section class="col col-2">
                                        <label class="input"> <i class="icon-append fa fa-map-marker"></i>
                                        <input type="text" name="username" placeholder="CEP">
                                        <b class="tooltip tooltip-bottom-right">Needed to enter the website</b> </label>
                                    </section>
                                    <section class="col col-8">
                                        <label class="input">
                                            <input type="text" name="lastname" placeholder="Rua">
                                        </label>
                                    </section>
                                    <section class="col col-2">
                                        <label class="input">
                                            <input type="text" name="lastname" placeholder="Número">
                                        </label>
                                    </section>
                                </div>  

                                <section>
                                    <label class="input"> <i class="icon-append fa fa-user"></i>
                                        <input type="text" name="username" placeholder="Username">
                                        <b class="tooltip tooltip-bottom-right">Needed to enter the website</b> </label>
                                </section>                      
                                
                                
                                <section>
                                    <label class="input"> <i class="icon-append fa fa-envelope-o"></i>
                                        <input type="email" name="email" placeholder="Email address">
                                        <b class="tooltip tooltip-bottom-right">Needed to verify your account</b> </label>
                                </section>

                                <section>
                                    <label class="input"> <i class="icon-append fa fa-lock"></i>
                                        <input type="password" name="password" placeholder="Password" id="password">
                                        <b class="tooltip tooltip-bottom-right">Don't forget your password</b> </label>
                                </section>

                                <section>
                                    <label class="input"> <i class="icon-append fa fa-lock"></i>
                                        <input type="password" name="passwordConfirm" placeholder="Confirm password">
                                        <b class="tooltip tooltip-bottom-right">Don't forget your password</b> </label>
                                </section>
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
                        </form>                     
                        
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
