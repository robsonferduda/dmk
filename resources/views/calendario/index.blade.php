@extends('layouts.admin')
@section('content')
<div id="ribbon">
    <ol class="breadcrumb">
        <li><a href="{{ url('home') }}">Início</a></li>
        <li>Calendário</li>
    </ol>
</div>
<div id="content">

                <div class="row">
                    <div class="col-xs-12 col-sm-7 col-md-7 col-lg-6">
                        <h1 class="page-title txt-color-blueDark"><i class="fa fa-calendar fa-fw "></i> 
                            Calendário                        
                        </h1>
                    </div>          
                    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                        <a data-toggle="modal" href="" id='compartilhar' class="btn btn-primary pull-right header-btn"><i class="fa fa-share-alt fa-lg"></i> Compartilhar</a>
                    </div>       
                </div>
                <!-- row -->
                
                <div class="row">
                
                    {{---<div class="col-sm-12 col-md-12 col-lg-3">
                        <!-- new widget -->
                        <div class="jarviswidget jarviswidget-color-blueDark">
                            <header>
                                <h2> Add Events </h2>
                            </header>
                
                            <!-- widget div-->
                            <div>
                
                                <div class="widget-body">
                                    <!-- content goes here -->
                
                                    <form id="add-event-form">
                                        <fieldset>
                
                                            <div class="form-group">
                                                <label>Status do Evento</label>
                                                <div class="btn-group btn-group-sm btn-group-justified" data-toggle="buttons">
                                                    <label class="btn btn-default active">
                                                        <input type="radio" name="iconselect" id="icon-3" value="fa-check">
                                                        <i class="fa fa-check text-muted"></i> </label>
                                                    <label class="btn btn-default">
                                                        <input type="radio" name="iconselect" id="icon-4" value="fa-user">
                                                        <i class="fa fa-times text-muted"></i> </label>
                                                </div>
                                            </div>
                
                                            <div class="form-group">
                                                <label>Event Title</label>
                                                <input class="form-control"  id="title" name="title" maxlength="40" type="text" placeholder="Event Title">
                                            </div>
                                            <div class="form-group">
                                                <label>Event Description</label>
                                                <textarea class="form-control" placeholder="Please be brief" rows="3" maxlength="40" id="description"></textarea>
                                                <p class="note">Maxlength is set to 40 characters</p>
                                            </div>
                
                                            <div class="form-group">
                                                <label>Select Event Color</label>
                                                <div class="btn-group btn-group-justified btn-select-tick" data-toggle="buttons">
                                                    <label class="btn bg-color-darken active">
                                                        <input type="radio" name="priority" id="option1" value="bg-color-darken txt-color-white" checked>
                                                        <i class="fa fa-check txt-color-white"></i> </label>
                                                    <label class="btn bg-color-blue">
                                                        <input type="radio" name="priority" id="option2" value="bg-color-blue txt-color-white">
                                                        <i class="fa fa-check txt-color-white"></i> </label>
                                                    <label class="btn bg-color-orange">
                                                        <input type="radio" name="priority" id="option3" value="bg-color-orange txt-color-white">
                                                        <i class="fa fa-check txt-color-white"></i> </label>
                                                    <label class="btn bg-color-greenLight">
                                                        <input type="radio" name="priority" id="option4" value="bg-color-greenLight txt-color-white">
                                                        <i class="fa fa-check txt-color-white"></i> </label>
                                                    <label class="btn bg-color-blueLight">
                                                        <input type="radio" name="priority" id="option5" value="bg-color-blueLight txt-color-white">
                                                        <i class="fa fa-check txt-color-white"></i> </label>
                                                    <label class="btn bg-color-red">
                                                        <input type="radio" name="priority" id="option6" value="bg-color-red txt-color-white">
                                                        <i class="fa fa-check txt-color-white"></i> </label>
                                                </div>
                                            </div>
                
                                        </fieldset>
                                        <div class="form-actions">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <button class="btn btn-default" type="button" id="add-event" >
                                                        Add Event
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                
                                    <!-- end content -->
                                </div>
                
                            </div>
                            <!-- end widget div -->
                        </div>
                        <!-- end widget -->
                    </div>--}}
                    <div class="col-sm-12 col-md-12 col-lg-12">
                
                        <!-- new widget -->
                        <div class="jarviswidget jarviswidget-color-blueDark">
                
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
                            <header>
                                <span class="widget-icon"> <i class="fa fa-calendar"></i> </span>
                                <h2> Meus Eventos </h2>
                                <div class="widget-toolbar">
                                    <!-- add: non-hidden - to disable auto hide -->
                                    <div class="btn-group">
                                        <button class="btn dropdown-toggle btn-xs btn-default" data-toggle="dropdown">
                                            Mostrar <i class="fa fa-caret-down"></i>
                                        </button>
                                        <ul class="dropdown-menu js-status-update pull-right">
                                            <li>
                                                <a href="javascript:void(0);" id="mt">Mês</a>
                                            </li>
                                            <li>
                                                <a href="javascript:void(0);" id="ag">Semana</a>
                                            </li>
                                            <li>
                                                <a href="javascript:void(0);" id="td">Dia</a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </header>
                
                            <!-- widget div-->
                            <div>
                
                                <div class="widget-body no-padding">
                                    <!-- content goes here -->
                                    <div class="widget-body-toolbar">
                
                                        <div id="calendar-buttons">
                
                                            <div class="btn-group">
                                                <a href="javascript:void(0)" class="btn btn-default btn-xs" id="btn-prev"><i class="fa fa-chevron-left"></i></a>
                                                <a href="javascript:void(0)" class="btn btn-default btn-xs" id="btn-next"><i class="fa fa-chevron-right"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="calendar"></div>
                
                                    <!-- end content -->
                                </div>
                
                            </div>
                            <!-- end widget div -->
                        </div>
                        <!-- end widget -->
                
                    </div>
                
                </div>
                
                <!-- end row -->
</div>
<div class="modal fade modal_top_alto" id="addEvento" data-backdrop="static" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    &times;
                </button>
                <h4 class="modal-title">
                    <i class="icon-append fa fa-plus"></i> Novo Evento
                </h4>
            </div>
            <div class="modal-body no-padding">
                {!! Form::open(['id' => 'form-add-evento', 'url' => '', 'class' => 'smart-form']) !!}
                    <fieldset>
                        <div class="row">
                            <section class="col col-md-12">
                                
                                <label class="label">Título<span class="text-danger">*</span></label>
                                <label class="input">
                                    <input type="text" name="titulo" id="titulo" required>
                                </label>
                                
                            </section>                  
                        </div>
                        <div class="row">
                            <section class="col col-md-6">
                                
                                <label class="label">Início<span class="text-danger">*</span></label>
                                <label class="input">
                                    <input class="data_evento" placeholder="___ /___ /___" type="text" name="inicio" >
                                </label>

                                 <label class="label"></label>
                                <label class="input">
                                    <input class="hora_evento" placeholder="___ : ___" type="text" name="horaInicio" >
                                </label>
                                
                            </section>   
                            <section class="col col-md-6">
                                
                                <label class="label">Fim</label>
                                <label class="input">
                                    <input class="data_evento" placeholder="___ /___ /___" type="text" name="fim" >
                                </label>

                                <label class="label"></label>
                                <label class="input">
                                    <input class="hora_evento" placeholder="___ : ___" type="text" name="horaFim" >
                                </label>
                                
                            </section>                  
                        </div>
                        <div class="row">
                            <section class="col col-md-12">          
       
                                    <label class="label">Descrição</label>
                                    <label class="textarea">
                                        <textarea name="descricao"></textarea>
                                    </label>
         
                            </section>
                        </div>
                     
                        <div class="msg_retorno"></div>
                    </fieldset>
                    <footer>
                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</button>
                        <button id="salvarEvento" class="btn btn-default btn-save-evento" ><i class="fa fa-save"></i> Adicionar Evento </button>
                    </footer>
                {!! Form::close() !!}                    
            </div>
        </div>
    </div>
</div>
<div class="modal fade modal_top_alto" id="editEvento" data-backdrop="static" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    &times;
                </button>
                <h4 class="modal-title">
                    <i class="icon-append fa fa-edit"></i> Editar Evento
                </h4>
            </div>
            <div class="modal-body no-padding">
                {!! Form::open(['id' => 'form-edit-evento', 'url' => '', 'class' => 'smart-form']) !!}
                    <fieldset>
                        <input type="hidden" name="googleCalendarId" >
                        <div class="row">
                            <section class="col col-md-12">
                                
                                <label class="label">Título<span class="text-danger">*</span></label>
                                <label class="input">
                                    <input type="text" name="titulo" id="titulo" required>
                                </label>
                                
                            </section>                  
                        </div>
                        <div class="row">
                            <section class="col col-md-6">
                                
                                <label class="label">Início<span class="text-danger">*</span></label>
                                <label class="input">
                                    <input class="data_evento" placeholder="___ /___ /___" type="text" name="inicio" >
                                </label>

                                 <label class="label"></label>
                                <label class="input">
                                    <input class="hora_evento" placeholder="___ : ___" type="text" name="horaInicio" >
                                </label>
                                
                            </section>   
                            <section class="col col-md-6">
                                
                                <label class="label">Fim</label>
                                <label class="input">
                                    <input class="data_evento" placeholder="___ /___ /___" type="text" name="fim" >
                                </label>

                                <label class="label"></label>
                                <label class="input">
                                    <input class="hora_evento" placeholder="___ : ___" type="text" name="horaFim" >
                                </label>
                                
                            </section>                  
                        </div>
                        <div class="row">
                            <section class="col col-md-12">          
       
                                    <label class="label">Descrição</label>
                                    <label class="textarea">
                                        <textarea rows="4" name="descricao"></textarea>
                                    </label>
         
                            </section>
                        </div>
                     
                        <div class="msg_retorno"></div>
                    </fieldset>
                    <footer>
                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</button>
                        <a id="excluirEvento" class="btn btn-danger btn-exluir-evento" ><i class="fa fa-trash"></i> Excluir Evento </a>
                        <button id="editarEvento" class="btn btn-default btn-editar-evento" ><i class="fa fa-save"></i> Salvar Evento </button>
                    </footer>
                {!! Form::close() !!}                    
            </div>
        </div>
    </div>
</div>
<div class="modal fade modal_top_alto" id="compartilharEvento" data-backdrop="static" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    &times;
                </button>
                <h4 class="modal-title">
                    <i class="icon-append fa fa-share-alt"></i> Compartilhar Evento
                </h4>
            </div>
            <div class="modal-body no-padding">
                {!! Form::open(['id' => 'form-compartilhar-evento', 'url' => '', 'class' => 'smart-form']) !!}
                    <fieldset>                
                        <div class="row">
                            <section class="col col-md-12">
                                
                                <label class="label">Link de compartilhamento</label>
                                <label class="input">
                                    <input type="text" name="link" id="link" >                                    
                                </label>                                
                            </section>   
                                                 
                        </div>
                        <footer>                          
                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</button>
                            <a id="copiarTexto" class="btn btn-default"><i class="fa fa-copy fa-lg"></i> Copiar Link</a>      
                        </footer>   
                    </fieldset>
                {!! Form::close() !!} 

            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
	<script type="text/javascript">
		$(document).ready(function() {

            $('#compartilhar').click(function(e){

                $.ajax({
                    type:'GET',
                    url: "{{ url('calendario/evento/gerar-link') }}",
                    success:function(data){

                        data = JSON.parse(data);

                        $("#link").val(data);

                    }
                        
                });


                $('#compartilharEvento').modal('show');
            });

            $('#copiarTexto').click(function(e){

                var copyText = document.getElementById("link");

                copyText.select();
                document.execCommand("copy");
  
            });

            $('#salvarEvento').click(function(e) {

                if($("#form-add-evento").valid()){

                    e.preventDefault();

                    var titulo     = $("#addEvento input[name='titulo']").val();
                    var inicio     = $("#addEvento input[name='inicio']").val();
                    var fim        = $("#addEvento input[name='fim']").val();
                    var horaInicio = $("#addEvento input[name='horaInicio']").val();
                    var horaFim    = $("#addEvento input[name='horaFim']").val();
                    var descricao  = $("#addEvento textarea[name='descricao']").val();

                    $.ajax({
                        type:'POST',
                        url: "{{ url('calendario/evento/adicionar') }}",
                        data:{titulo:titulo, inicio:inicio, fim:fim, horaInicio:horaInicio, horaFim:horaFim,descricao:descricao},
                        success:function(data){

                            data = JSON.parse(data);
                          
                            if(data.id === true){

                                $('#addEvento').modal('hide');
                                $('#calendar').fullCalendar('refetchEvents');

                                $("#addEvento input[name='titulo']").val('');
                                $("#addEvento input[name='inicio']").val('');
                                $("#addEvento input[name='fim']").val('');
                                $("#addEvento input[name='horaInicio']").val('');
                                $("#addEvento input[name='horaFim']").val('');
                                $("#addEvento textarea[name='descricao']").val('');

                                $(".msg_retorno").html(''); 

                            }else{

                                $(".msg_retorno").html("<h3 style='color:red' >"+data.msg+"</h3>");                                
                            } 
                          
                         
                               
                       }

                    });
                }else{
                   
                    $(".msg_retorno").html('');                          
                }
            });


            $('#editarEvento').click(function(e) {

                if($("#form-edit-evento").valid()){

                    e.preventDefault();

                    var titulo     = $("#editEvento input[name='titulo']").val();
                    var inicio     = $("#editEvento input[name='inicio']").val();
                    var fim        = $("#editEvento input[name='fim']").val();
                    var horaInicio = $("#editEvento input[name='horaInicio']").val();
                    var horaFim    = $("#editEvento input[name='horaFim']").val();
                    var descricao  = $("#editEvento textarea[name='descricao']").val();
                    var id         = $("#editEvento input[name='googleCalendarId']").val();

                    $.ajax({
                        type:'POST',
                        url: "{{ url('calendario/evento/editar') }}",
                        data:{id:id, titulo:titulo, inicio:inicio, fim:fim, horaInicio:horaInicio, horaFim:horaFim,descricao:descricao},
                        success:function(data){

                            data = JSON.parse(data);
                          
                            if(data.id === true){

                                $('#editEvento').modal('hide');
                                $('#calendar').fullCalendar('refetchEvents');

                                $("#editEvento input[name='titulo']").val('');
                                $("#editEvento input[name='inicio']").val('');
                                $("#editEvento input[name='fim']").val('');
                                $("#editEvento input[name='horaInicio']").val('');
                                $("#editEvento input[name='horaFim']").val('');
                                $("#editEvento textarea[name='descricao']").val('');
                                $("#editEvento input[name='googleCalendarId']").val('');

                                $(".msg_retorno").html(''); 

                            }else{

                                $(".msg_retorno").html("<h3 style='color:red' >"+data.msg+"</h3>");                                
                            } 
                          
                         
                               
                       }

                    });
                }else{
                   
                    $(".msg_retorno").html('');                          
                }
            });
            

            $('#excluirEvento').click(function(e) {

                var id = $("#editEvento input[name='googleCalendarId']").val();
                $('#editEvento').modal('hide');

                $.ajax({
                        type:'POST',
                        url: "{{ url('calendario/evento/excluir') }}",
                        data:{id:id},
                        success:function(data){

                            data = JSON.parse(data);
                          
                            if(data.id === true){

                                $('#calendar').fullCalendar('refetchEvents');

                                $(".msg_retorno").html(''); 

                            }else{

                                $(".msg_retorno").html("<h3 style='color:red' >"+data.msg+"</h3>");                                
                            }       
                       }

                    });
               
            });


            $.validator.addMethod("dateSize",
                function(value, element) {
                    if(value.length != 10 && value.length != 0){
                        return false
                    }else{
                        return true;
                    }
                },
                "Data inválida.");

            $.validator.addMethod("dateFormat",
                function(value, element) {
                    return value.match(/^(?:(?:31(\/)(?:0?[13578]|1[02]))\1|(?:(?:29|30)(\/)(?:0?[1,3-9]|1[0-2])\2))(?:(?:1[6-9]|[2-9]\d)?\d{2})$|^(?:29(\/)0?2\3(?:(?:(?:1[6-9]|[2-9]\d)?(?:0[48]|[2468][048]|[13579][26])|(?:(?:16|[2468][048]|[3579][26])00))))$|^(?:0?[1-9]|1\d|2[0-8])(\/)(?:(?:0?[1-9])|(?:1[0-2]))\4(?:(?:1[6-9]|[2-9]\d)\d{2})$/);
                },
                "Data inválida.");

             $.validator.addMethod("horaSize",
                function(value, element) {                
                    
                    if(value.length != 5 && value.length != 0){
                        return false
                    }else{
                        return true;
                    }
                    
                },
                "Hora inválida.");



            var validobj = $("#form-add-evento").validate({

                    
                    rules : {
                        titulo : {
                            required: true,
                        }, 
                        inicio:{
                            required: true,
                            dateFormat: true,
                            dateSize: true
                        },
                        fim:{                        
                            dateFormat: true,
                            dateSize: true
                        },
                        horaInicio:{
                            horaSize: true
                        },
                        horaFim:{
                            horaSize: true
                        }
                    },
                    // Messages for form validation
                    messages : {
                        titulo : {
                            required : 'Campo Título é Obrigatório'
                        },  
                        inicio:{
                            required: 'Campo Início Obrigatório'
                        },
                    },

            });

            var validobjEdit = $("#form-edit-evento").validate({

                    
                    rules : {
                        titulo : {
                            required: true,
                        }, 
                        inicio:{
                            required: true,
                            dateFormat: true,
                            dateSize: true
                        },
                        fim:{                        
                            dateFormat: true,
                            dateSize: true
                        },
                        horaInicio:{
                            horaSize: true
                        },
                        horaFim:{
                            horaSize: true
                        }
                    },
                    // Messages for form validation
                    messages : {
                        titulo : {
                            required : 'Campo Título é Obrigatório'
                        },  
                        inicio:{
                            required: 'Campo Início Obrigatório'
                        },
                    },

            });

            pageSetUp();
            

                "use strict";
            
                var date = new Date();
                var d = date.getDate();
                var m = date.getMonth();
                var y = date.getFullYear();
            
                var hdr = {
                    left: 'title',
                    center: 'month,agendaWeek,agendaDay',
                    right: 'prev,today,next'
                };
            
                var initDrag = function (e) {
                    // create an Event Object (http://arshaw.com/fullcalendar/docs/event_data/Event_Object/)
                    // it doesn't need to have a start or end
            
                    var eventObject = {
                        title: $.trim(e.children().text()), // use the element's text as the event title
                        description: $.trim(e.children('span').attr('data-description')),
                        icon: $.trim(e.children('span').attr('data-icon')),
                        className: $.trim(e.children('span').attr('class')) // use the element's children as the event class
                    };
                    // store the Event Object in the DOM element so we can get to it later
                    e.data('eventObject', eventObject);
            
                    // make the event draggable using jQuery UI
                    e.draggable({
                        zIndex: 999,
                        revert: true, // will cause the event to go back to its
                        revertDuration: 0 //  original position after the drag
                    });
                };
            
                var addEvent = function (title, priority, description, icon) {
                    title = title.length === 0 ? "Untitled Event" : title;
                    description = description.length === 0 ? "No Description" : description;
                    icon = icon.length === 0 ? " " : icon;
                    priority = priority.length === 0 ? "label label-default" : priority;
            
                    var html = $('<li><span class="' + priority + '" data-description="' + description + '" data-icon="' +
                        icon + '">' + title + '</span></li>').prependTo('ul#external-events').hide().fadeIn();
            
                    $("#event-container").effect("highlight", 800);
            
                    initDrag(html);
                };
            
                /* initialize the external events
                 -----------------------------------------------------------------*/
            
                $('#external-events > li').each(function () {
                    initDrag($(this));
                });
            
                $('#add-event').click(function () {
                    var title = $('#title').val(),
                        priority = $('input:radio[name=priority]:checked').val(),
                        description = $('#description').val(),
                        icon = $('input:radio[name=iconselect]:checked').val();
            
                    addEvent(title, priority, description, icon);
                });
            
                /* initialize the calendar
                 -----------------------------------------------------------------*/
            
                $('#calendar').fullCalendar({
            
                    header: hdr,
                    editable: false,
                    droppable: false, // this allows things to be dropped onto the calendar !!!
                    selectable: true,
                    timeFormat: 'HH:mm',
                    locale: 'pt-br',
                    timezone: 'America/Sao_Paulo',
                    drop: function (date, allDay) { // this function is called when something is dropped
            
                        // retrieve the dropped element's stored Event Object
                        var originalEventObject = $(this).data('eventObject');
            
                        // we need to copy it, so that multiple events don't have a reference to the same object
                        var copiedEventObject = $.extend({}, originalEventObject);
            
                        // assign it the date that was reported
                        copiedEventObject.start = date;
                        copiedEventObject.allDay = allDay;
            
                        // render the event on the calendar
                        // the last `true` argument determines if the event "sticks" (http://arshaw.com/fullcalendar/docs/event_rendering/renderEvent/)
                        $('#calendar').fullCalendar('renderEvent', copiedEventObject, true);
            
                        // is the "remove after drop" checkbox checked?
                        if ($('#drop-remove').is(':checked')) {
                            // if so, remove the element from the "Draggable Events" list
                            $(this).remove();
                        }
            
                    },
                    select: function (start, end, allDay,view) {

                        $('#addEvento input[name=horaFim]').val('');
                        $('#addEvento input[name=horaInicio]').val('');
                        $('#addEvento input[name=inicio]').val('');
                        $('#addEvento input[name=fim]').val('');

                        $('#addEvento input[name=inicio]').val(moment(start).format('DD/MM/Y'));

                        console.log(moment(start));

                        if(view.viewSpec.type == 'month'){
                            $('#addEvento input[name=fim]').val(moment(end).subtract(1, "days").format('DD/MM/Y'));
                        }else{
                            $('#addEvento input[name=fim]').val(moment(end).format('DD/MM/Y'));
                            $('#addEvento input[name=horaInicio]').val(moment(start).format('HH:mm'));
                            $('#addEvento input[name=horaFim]').val(moment(end).format('HH:mm'));
                        }

                        
                        
                        $('#addEvento').modal('show');

                        //calendar.fullCalendar('unselect');
                    },
                    loading: function (bool) {
                        $('.fc-view').loader('show');
                    },
                    eventAfterAllRender: function (view) {
                        $('.fc-view').loader('hide');
                        
                    },
                    events: {
                        url: "{{url('calendario/eventos-por-data')}}",
                        type: 'POST',
                        error: function() {
                            alert('there was an error while fetching events!');
                        },
                        //color: '#71843F',   // a non-ajax option
                        textColor: 'white' // a non-ajax option
                    },                                       
                    eventRender: function (event, element, icon) {
                               
                        var descricao = '';

                        //element.find('.fc-title').append("<span class='excluirEvento'><i class='air air-top-right fa fa-times'></i></span>");

                        if(event.description == null){
                            event.description = '';
                        }

                        if(event.allDay === false && event.end !== null){

                            descricao = '<strong>'+moment(event.start).format('DD/MM/Y HH:mm')+' - '+ moment(event.end).format('DD/MM/Y HH:mm')+'</strong> <br />'+event.description;

                        }else{

                            if(event.end !== null){
                                descricao = '<strong>'+moment(event.start).format('DD/MM/Y')+' - '+ moment(event.end).subtract(1, "days").format('DD/MM/Y')+'</strong> <br />'+event.description;
                            }else{
                                descricao = '<strong>'+moment(event.start).format('DD/MM/Y')+'</strong> <br />'+event.description;
                            }
                        }

                        $(element).popover({
                            html:true,
                            title: event.title,
                            content: descricao,
                            trigger: 'hover',
                            placement: 'top',
                            container: 'body'
                          });

                        // $(element).mouseover(function(){
                        //     if (!event.description == "") {
                        //         element.find('.fc-title').append("<br/><span class='ultra-light'>" + event.description +
                        //           "</span>");
                        //     }
                        // });

                        // $(element).mouseout(function(){
                        //     if (!event.description == "") {
                        //         element.find('.fc-title').append("<br/><span class='ultra-light'>" + event.description +
                        //           "</span>");
                        //     }
                        // });

                        element.find('.fc-time').css('display','inline-block');
                        element.find('.fc-content').css('cursor','pointer');

                        
                        if (!event.icon == "") {
                            element.find('.fc-title').append("<i class='air air-top-right fa " + event.icon +
                                 " '></i>");
                        }
                    },
            
                    windowResize: function (event, ui) {
                        $('#calendar').fullCalendar('render');
                    },
                    eventClick: function (event){


                        

                        if(event.isProcesso === false){

                            $('#editEvento input[name=horaFim]').val('');
                            $('#editEvento input[name=horaInicio]').val('');
                            $('#editEvento input[name=inicio]').val('');
                            $('#editEvento input[name=fim]').val('');
                            $('#editEvento input[name=googleCalendarId]').val('');
                            

                            if(event.start !== null && event.allDay === false){
                                $('#editEvento input[name=horaInicio]').val(moment(event.start).format('HH:mm'));
                            }else{
                                $('#editEvento input[name=horaInicio]').val('');
                            }

                            if(event.allDay === false && event.end !== null){

                               $('#editEvento input[name=fim]').val(moment(event.end).format('DD/MM/Y'));
                               $('#editEvento input[name=horaFim]').val(moment(event.end).format('HH:mm'));

                            }else{

                                if(event.end !== null){
                                    $('#editEvento input[name=fim]').val(moment(event.end).subtract(1, "days").format('DD/MM/Y'));      
                                }
                            }

                            $('#editEvento input[name=inicio]').val(moment(event.start).format('DD/MM/Y'));   
                            $('#editEvento textarea[name=descricao]').val(event.description);  
                            $('#editEvento input[name=titulo]').val(event.title);      
                            $('#editEvento input[name=googleCalendarId]').val(event.googleCalendarId);               
                                                                            
                            $('#editEvento').modal('show');
                        }else{
                            if (event.url) {
                               window.open(event.url, "_blank");
                               return false;
                            }           
                        }
                    },
                    // eventMouseover: function (event,element){
                    
                    //     if (!event.description == "") {
                    //         element.find('.fc-title').append("<br/><span class='ultra-light'>" + event.description +
                    //              "</span>");
                    //     }                         
                    // }
                });
            
                /* hide default buttons */
                $('.fc-right, .fc-center').hide();

            
                $('#calendar-buttons #btn-prev').click(function () {
                    $('.fc-prev-button').click();
                    return false;
                });
                
                $('#calendar-buttons #btn-next').click(function () {
                    $('.fc-next-button').click();
                    return false;
                });
                
                $('#calendar-buttons #btn-today').click(function () {
                    $('.fc-today-button').click();
                    return false;
                });
                
                $('#mt').click(function () {
                    $('#calendar').fullCalendar('changeView', 'month');
                });
                
                $('#ag').click(function () {
                    $('#calendar').fullCalendar('changeView', 'agendaWeek');
                });
                
                $('#td').click(function () {
                    $('#calendar').fullCalendar('changeView', 'agendaDay');
                });        
        })
	</script>
@endsection
@section('stylesheet')
<style type="text/css">
        
    .popover {
        max-width: 400px;
    }
    .popover-title {
        word-wrap: break-word;
    }
    .popover-content {
        word-wrap: break-word;
    }
</style>
@endsection