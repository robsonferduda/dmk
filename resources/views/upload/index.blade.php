@extends('layouts.admin')
@section('content')
<div id="ribbon">
    <ol class="breadcrumb">
        <li>Início</li>
    </ol>
</div>
<div id="content">

    <div class="row">
        <div class="col-xs-12 col-sm-7 col-md-7 col-lg-4">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-home"></i>Painel Administrativo 
            </h1>
        </div>
    </div>
    
    <div class="row">

        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="well">
                 <div id="filepicker">
                <!-- Button Bar -->
                <div class="button-bar">
                    <div class="btn btn-success fileinput">
                        <i class="fa fa-plus"></i> Add files
                        <input type="file" name="files[]" multiple>
                    </div>

                    <button type="button" class="btn btn-primary camera-show">
                        <i class="fa fa-camera"></i> Camera
                    </button>

                    <button type="button" class="btn btn-info start-all">
                        <i class="fa fa-arrow-circle-o-up"></i> Start all
                    </button>

                    <button type="button" class="btn btn-warning cancel-all">
                        <i class="fa fa-ban"></i> Cancel all
                    </button>

                    <button type="button" class="btn btn-danger delete-all">
                        <i class="fa fa-trash-o"></i> Delete all
                    </button>
                </div>

                <!-- Files -->
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th class="column-preview">Preview</th>
                                <th class="column-name">Name</th>
                                <th class="column-size">Size</th>
                                <th class="column-date">Modified</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody class="files"></tbody>
                        <tfoot><tr><td colspan="5">No files where found.</td></tr></tfoot>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="pagination-container text-center"></div>

                <!-- Drop Window -->
                <div class="drop-window">
                    <div class="drop-window-content">
                        <h3><i class="fa fa-upload"></i> Drop files to upload</h3>
                    </div>
                </div>
            </div><!-- end of #filepicker -->
            </div>
        </article>
    </div>
</div>
    <!-- Crop Modal -->
    <div id="crop-modal" class="modal fade" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <span class="close" data-dismiss="modal">&times;</span>
                    <h4 class="modal-title">Make a selection</h4>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning crop-loading">Loading image...</div>
                    <div class="crop-preview"></div>
                </div>
                <div class="modal-footer">
                    <div class="crop-rotate">
                        <button type="button" class="btn btn-default btn-sm crop-rotate-left" title="Rotate left">
                            <i class="fa fa-undo"></i>
                        </button>
                        <button type="button" class="btn btn-default btn-sm crop-flip-horizontal" title="Flip horizontal">
                            <i class="fa fa-arrows-h"></i>
                        </button>
                        <!-- <button type="button" class="btn btn-default btn-sm crop-flip-vertical" title="Flip vertical">
                            <i class="fa fa-arrows-v"></i>
                        </button> -->
                        <button type="button" class="btn btn-default btn-sm crop-rotate-right" title="Rotate right">
                            <i class="fa fa-repeat"></i>
                        </button>
                    </div>
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success crop-save" data-loading-text="Saving...">Save</button>
                </div>
            </div>
        </div>
    </div><!-- end of #crop-modal -->

    <!-- Camera Modal -->
    <div id="camera-modal" class="modal fade" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <span class="close" data-dismiss="modal">&times;</span>
                    <h4 class="modal-title">Take a picture</h4>
                </div>
                <div class="modal-body">
                    <div class="camera-preview"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left camera-hide" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success camera-capture">Take picture</button>
                </div>
            </div>
        </div>
    </div><!-- end of #camera-modal -->
@endsection
@section('script')
    <script type="text/javascript">

         $('#filepicker').filePicker({
            url: '../filepicker',
            ui: {
                autoUpload: false
            },
            data: {
                _token: "{{ csrf_token() }}"
            },
            plugins: ['ui', 'drop', 'camera', 'crop']

        })
        .on('done.filepicker', function (e, data) {
            alert(data.files[0].name);
        });

        // Replace timeago strings.
        if ($.fn.timeago) {
            $.timeago.settings.strings = $.extend({}, $.timeago.settings.strings , {
                seconds: 'few seconds', minute: 'a minute',
                hour: 'an hour', hours: '%d hours', day: 'a day',
                days: '%d days', month: 'a month', year: 'a year'
            });
        }

    </script>

     <script type="text/x-tmpl" id="uploadTemplate">
        <tr class="upload-template">
            <td class="column-preview">
                <div class="preview">
                    <span class="fa file-icon-{%= o.file.extension %}"></span>
                </div>
            </td>
            <td class="column-name">
                <p class="name">{%= o.file.name %}</p>
                <span class="text-danger error">{%= o.file.error || '' %}</span>
            </td>
            <td colspan="2">
                <p>{%= o.file.sizeFormatted || '' %}</p>
                <div class="progress">
                    <div class="progress-bar progress-bar-striped active"></div>
                </div>
            </td>
            <td>
                {% if (!o.file.autoUpload && !o.file.error) { %}
                    <a href="#" class="action action-primary start" title="Upload">
                        <i class="fa fa-arrow-circle-o-up"></i>
                    </a>
                {% } %}
                <a href="#" class="action action-warning cancel" title="Cancel">
                    <i class="fa fa-ban"></i>
                </a>
            </td>
        </tr>
    </script><!-- end of #uploadTemplate -->

    <!-- Download Template -->
    <script type="text/x-tmpl" id="downloadTemplate">
        {% o.timestamp = function (src) {
            return (src += (src.indexOf('?') > -1 ? '&' : '?') + new Date().getTime());
        }; %}
        <tr class="download-template">
            <td class="column-preview">
                <div class="preview">
                    {% if (o.file.versions && o.file.versions.thumb) { %}
                        <a href="{%= o.file.url %}" target="_blank">
                            <img src="{%= o.timestamp(o.file.versions.thumb.url) %}" width="64" height="64"></a>
                        </a>
                    {% } else { %}
                        <span class="fa file-icon-{%= o.file.extension %}"></span>
                    {% } %}
                </div>
            </td>
            <td class="column-name">
                <p class="name">
                    {% if (o.file.url) { %}
                        <a href="{%= o.file.url %}" target="_blank">{%= o.file.name %}</a>
                    {% } else { %}
                        {%= o.file.name %}
                    {% } %}
                </p>
                {% if (o.file.error) { %}
                    <span class="text-danger">{%= o.file.error %}</span>
                {% } %}
            </td>
            <td class="column-size"><p>{%= o.file.sizeFormatted %}</p></td>
            <td class="column-date">
                {% if (o.file.time) { %}
                    <time datetime="{%= o.file.timeISOString() %}">
                        {%= o.file.timeFormatted %}
                    </time>
                {% } %}
            </td>
            <td>
                {% if (o.file.imageFile && !o.file.error) { %}
                    <a href="#" class="action action-primary crop" title="Crop">
                        <i class="fa fa-crop"></i>
                    </a>
                {% } %}
                {% if (o.file.error) { %}
                    <a href="#" class="action action-warning cancel" title="Cancel">
                        <i class="fa fa-ban"></i>
                    </a>
                {% } else { %}
                    <a href="#" class="action action-danger delete" title="Delete">
                        <i class="fa fa-trash-o"></i>
                    </a>
                {% } %}
            </td>
        </tr>
    </script><!-- end of #downloadTemplate -->

    <!-- Pagination Template -->
    <script type="text/x-tmpl" id="paginationTemplate">
        {% if (o.lastPage > 1) { %}
            <ul class="pagination pagination-sm">
                <li {% if (o.currentPage === 1) { %} class="disabled" {% } %}>
                    <a href="#!page={%= o.prevPage %}" data-page="{%= o.prevPage %}" title="Previous">&laquo;</a>
                </li>

                {% if (o.firstAdjacentPage > 1) { %}
                    <li><a href="#!page=1" data-page="1">1</a></li>
                    {% if (o.firstAdjacentPage > 2) { %}
                       <li class="disabled"><a>...</a></li>
                    {% } %}
                {% } %}

                {% for (var i = o.firstAdjacentPage; i <= o.lastAdjacentPage; i++) { %}
                    <li {% if (o.currentPage === i) { %} class="active" {% } %}>
                        <a href="#!page={%= i %}" data-page="{%= i %}">{%= i %}</a>
                    </li>
                {% } %}

                {% if (o.lastAdjacentPage < o.lastPage) { %}
                    {% if (o.lastAdjacentPage < o.lastPage - 1) { %}
                        <li class="disabled"><a>...</a></li>
                    {% } %}
                    <li><a href="#!page={%= o.lastPage %}" data-page="{%= o.lastPage %}">{%= o.lastPage %}</a></li>
                {% } %}

                <li {% if (o.currentPage === o.lastPage) { %} class="disabled" {% } %}>
                    <a href="#!page={%= o.nextPage %}" data-page="{%= o.nextPage %}" title="Next">&raquo</a>
                </li>
            </ul>
        {% } %}
    </script><!-- end of #paginationTemplate -->
@endsection