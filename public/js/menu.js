$(document).ready(function() {
			
	var _location = document.location.toString();
    var applicationNameIndex = _location.indexOf('/', _location.indexOf('://') + 3);
    var applicationName = _location.substring(0, applicationNameIndex) + '/';
    var webFolderIndex = _location.indexOf('/', _location.indexOf(applicationName) + applicationName.length);
    var webFolderFullPath = _location.substring(0, webFolderIndex);

    // ============== Comportamento do menu lateral (INICIO) =================

        $(".item_pai").click(function(){

            var titulo = $(this).attr("id");
            $.get(webFolderFullPath+"/public/configuracao/menu/"+titulo, function(data) {
                console.log(data);
            });

        });

    // ============== Comportamento do menu lateral (FIM) =================

});