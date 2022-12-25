/* ***************** FUNCTIONS: ***************** */
// File input:
$(function () {
    // Create the close button
    var closebtn = $('<button/>', {
        type: "button",
        text: 'x',
        id: 'close-preview',
        style: 'font-size: initial;',
    });
    closebtn.attr("class", "close pull-right");
    // Set the popover default content
    $('.image-preview').popover({
        trigger: 'manual',
        html: true,
        title: "<strong>" + txtPreview + "</strong>" + $(closebtn)[0].outerHTML,
        content: "There's no image",
        placement: 'bottom'
    });
    // Clear event
    $('.image-preview-clear').click(function () {
        var parent = $(this).closest('.image-preview');

        // $('.image-preview').attr("data-content","").popover('hide');
        // $('.image-preview-filename').val("");
        // $('.image-preview-clear').hide();
        // $('.image-preview-input input:file').val("");
        // $(".image-preview-input-title").text(txtBuscar);

        parent.find('.image-preview').attr("data-content", "").popover('hide');
        parent.find('.image-preview-filename').val("");
        parent.find('.image-preview-clear').hide();
        parent.find('.image-preview-input input:file').val("");
        parent.find(".image-preview-input-title").text(txtBuscar);
    });
    // Create the preview image
    $(".image-preview-input input:file").change(function () {
        var parent = $(this).closest('.image-preview');

        var img = $('<img/>', {
            id: 'dynamic',
            width: 150
            //height:100
        });
        var file = this.files[0];
        var reader = new FileReader();
        // Set preview image into the popover data-content
        reader.onload = function (e) {
            // $(".image-preview-input-title").text(txtCambiar);
            // $(".image-preview-clear").show();
            // $(".image-preview-filename").val(file.name);
            // img.attr('src', e.target.result);
            // $(".image-preview").attr("data-content",$(img)[0].outerHTML).popover("show");

            parent.find(".image-preview-input-title").text(txtCambiar);
            parent.find(".image-preview-clear").show();
            parent.find(".image-preview-filename").val(file.name);
            img.attr('src', e.target.result);
            parent.attr("data-content", $(img)[0].outerHTML).popover("show");
        }
        reader.readAsDataURL(file);
    });
});

// Select 2. ¡Atención! la declaración genérica de este componente NO puede ir ni en el document Ready ni en window Load, pues en ese caso no funcionan las declaraciones específicas del componente en los modal.
if ($('.select2').length > 0) {
    $('.select2').select2({
        tags: true,
        width: '100%',
        language: {
            noResults: function () {
                return txtResultadosNoEncontrados;
            },
            searching: function () {
                return txtBuscando + '...';
            }
        }
    });

    /*$('.select2').on('change', function (e){
        var data = $('.select2').select2("val");
        //@this.set('ottPlatform', data);
    });*/
}

// Mensajes de alerta.
function divMsg(msg, danger = false) {
    var css = danger ? 'alert-danger' : 'alert-success';
    $('.alert').remove();
    $('#mainContent').prepend('<div class="alert ' + css + '" role="alert">' + msg + '</div>');
}

// Set cookie.
let setCookie = (name, value, days) => {
    let expires = "";
    if (days) {
        let date = new Date();
        date.setTime(date.getTime() + days * 24 * 60 * 60 * 1000);
        expires = "; expires=" + date.toUTCString();
    }
    document.cookie = name + "=" + (value || "") + expires + "; path=/";
};

// Get cookie.
let getCookie = name => {
    let nameEQ = name + "=";
    let ca = document.cookie.split(";");
    for (var i = 0; i < ca.length; i++) {
        let c = ca[i];
        while (c.charAt(0) === " ") c = c.substring(1, c.length);
        if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length);
    }
    return null;
};

// Rotación:
jQuery.fn.rotate = function (degrees) {
    $(this).css({
        '-webkit-transform': 'rotate(' + degrees + 'deg)',
        '-moz-transform': 'rotate(' + degrees + 'deg)',
        '-ms-transform': 'rotate(' + degrees + 'deg)',
        'transform': 'rotate(' + degrees + 'deg)'
    });
    return $(this);
};

// Abreviaturas días de la semana:
function weekDays(day = false){
    var days = {1: _lunes, 2: _martes, 3: _miercoles, 4: _jueves, 5: _viernes, 6: _sabado, 7: _domingo};

    if(day){
        return days[day];
    }else{
        return days;
    }
}

// Bucle de opciones para tipo de jornada:
function dayTypesOptions(data, num, elem, text){
    var html = '<div class="day-types-options">';
        html += '<select class="form-control select2" name="" id="comboDayTypes" data-num="'+num+'">';
            html += '<option value="">'+text+'</option>';
            for(i=0; i<data.length; i++){
                html += '<option value="'+data[i].id+'">'+data[i].name+'</option>';
            }
        html += '</select>';

        html += '<a href="#" data-num="'+num+'">'
            html += '<i class="fa fa-times"></i>';
        html += '</a>';
    html += '</div>';

    elem.append(html);
}

// ************************************ DOCUMENT READY: ************************************
$(document).ready(function () {
    /* ***************** INDICE DE MÉTODOS: *****************
    1. Tooltips.
    2. Editor texto.
    3. Botón animado.
    4. Scroll top horizontal.
        4.1. Scroll top vertical.
    5. Popover.
    6. Botón Ladda.
    7. Bloquear doble submit en formularios.
    8. Bloqueo de botones tras 1 click.
    9. Menú opciones top responsive.
    */

    //Hora del control horario:
    horaActual();

    //window.location.reload(true);

    // 1. Tooltips:
    $('body').tooltip({selector: '[data-toggle="tooltip"], [rel="tooltip"]'});
    //$('.progress').tooltip('show');

    // 2. Editor texto:
    if ($('.editor').length > 0) {
        $('.editor').trumbowyg({
            lang: idm,
            autogrow: true,
            svgPath: '/plugins/trumbowyg/ui/icons.svg'
            // btns: [
            //     ['viewHTML'],
            //     ['undo', 'redo'], // Only supported in Blink browsers
            //     ['fontsize'],
            //     ['formatting'],
            //     ['strong', 'em', 'del'],
            //     ['superscript', 'subscript'],
            //     ['link'],
            //     ['insertImage'],
            //     ['justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'],
            //     ['unorderedList', 'orderedList'],
            //     ['horizontalRule'],
            //     ['removeformat'],
            //     ['fullscreen']
            // ],
            // plugins: {
            //     fontsize: {
            //         sizeList: [
            //             '11px',
            //             '12px',
            //             '14px',
            //             '18px',
            //             '22px'
            //         ],
            //         allowCustomSize: false
            //     }
            // }
        });
    }

    // 3. Botón animado:
    /*$('.btn').on('click', function(){
        $('.fa-spinner').remove();
        if(!$(this).hasClass('no-spinner')){
            $(this).append('<i class="ml-2 fa fa-spinner fa-spin"></i>');
            if($(this).hasClass('close-animation')){
                setTimeout(function(){
                    $('.fa-spinner').delay(2000).remove();
                }, 2000);
            }
        }
    });*/
    $(document).on('click', '.swal-button--cancel, button.close, .modal', function () {
        $('.fa-spinner').remove();
    });

    // 4. Scroll top horizontal.
    $(".scroll-top").scroll(function () {
        $(".scroll-bottom").scrollLeft($(".scroll-top").scrollLeft());
    });
    $(".scroll-bottom").scroll(function () {
        $(".scroll-top").scrollLeft($(".scroll-bottom").scrollLeft());
    });

    // 4.1. Scroll top vertical.
    $('.scroll-to-top').on('click', function(e){
        e.preventDefault();
        $('html, body').animate({scrollTop : 0}, 1000);
    });

    // 5. Popover.
    $('[data-toggle="popover"]').popover({
        html: true
    });

    // 6. Botón Ladda.
    var l = $('.ladda-button_').ladda();
    l.click(function () {
        // Start loading
        l.ladda('start');

        // Do something in backend and then stop ladda
        // setTimeout() is only for demo purpose
        setTimeout(function () {
            l.ladda('stop');
        }, 3000);
    });

    // 7. Bloquear doble submit en formularios.
    $(document).on('submit', '.frm-one-click', function () {
        $(":submit").prop("disabled", true);
    });

    // 8. Bloqueo de botones tras 1 click.
    $(document).on('click', '.one-click', function () {
        $('.one-click').each(function (index) {
            $(this).prop("disabled", true);
        });
    });

    // 9. Menú opciones top responsive.
    $('.btn-top-links').on('click', function(e){
        e.preventDefault();
        $(".navbar-top-links").toggleClass('d-none');
    });
});

// ************************************ WINDOW LOAD: ************************************
$(window).on('load', function () {
    /* ***************** INDICE DE MÉTODOS: *****************
    1. Eliminar preview imagen seleccionada.
    2. Modal traducciones.
        2.1. Guardar traducciones de strings.
    3. Activar / desactivar item.
    4. Confirmación de acciones.
        4.1. Confirmación de acción por formulario.
    5. Modals flash.
    6. Toggle vista lista - cuadrícula.
        6.1. Leer cookie productos.
        6.2. Leer cookie proyectos.
        6.3. Leer cookie usuarios.
    7. Copiar a portapapeles.
        7.1. Copiar a portapapeles desde link.
    8. Destacar imagen.
        8.1. Imagen pública.
    9. Seleccionar empresa.
    10. Modal selector empresa inicial.
    11. Provincias por país.
    12. Poblaciones por provincia.
    13. Habilitar buscador Select2 en modals.
    14. Valor máximo admitido en un campo.
    15. Validar decimales.
    16. Añadir segmento a formulario de generación de patrones.
    17. Búsqueda nuevas notificaciones  + mensajes chat.
    18. Rotación caret arrow.
    19. Producto favorito.
    20. Mini-modal.
        20.1. Modificar opción mini-modal.
    21. Vuelta a top page.
        21.1. Mostrar - ocultar botón de top page.
    22. Máscara para horas.
    23. Histórico de horas trabajadas en tarea.
    24. Selector de modalidad de presupuesto.
    25. Seleccionar icono.
    26. Modal editar item.
    27. Aplicar descuento por defecto del cliente a líneas del pedido, tanto nueva como edición.
    28. Cálculo precio + precio con iva en gastos.
    29. Guardar preferencia.
    30. Selector cuenta de gastos por estructura.
    31. Desplegar bloque de permisos.
    */

    // 1. Eliminar preview imagen seleccionada:
    $(document).on('click', '#close-preview', function () {
        //$('.image-preview').popover('hide');
        $(this).closest('.popover').popover('hide');
        // Hover before close the preview
        /*$('.image-preview').hover(
            function () {
               $('.image-preview').popover('show');
            },
             function () {
               $('.image-preview').popover('hide');
            }
        ); */
    });

    // 2. Modal traducciones:
    $(document).on('click', '.btn-translations', function () {
        var id = $(this).data('id');
        var schema = $(this).data('schema');
        $('.input-translation').val('');

        $('#objId').val(id);
        $('#schema').val(schema);

        $.get("/string_translation/" + id + "/" + schema, function (response, state) {
            if (response) {
                for (var i in response) {
                    $('#lang-' + i).val(response[i]);
                }
            }
        });
    });

    // 2.1. Guardar traducciones de strings:
    $(document).on('submit', '#frm-translations', function (e) {
        e.preventDefault();
        var data = $(this).serialize();
        $('#modal-translations').modal('toggle');

        $.ajax({
            url: "/languages/store_string",
            type: "POST",
            data: data,
            cache: false,
            datatype: "JSON",
            success: function (response) {
                divMsg(response);
            }
        });
    });

    // 3. Activar / desactivar item:
    $(document).on('click', '.chk-action', function () {
        var status = '';
        var elem = $(this);
        //Atención!!: detecta el estado previo a la acción, de modo que :checked es si queda deseleccionado:
        if (elem.siblings('input').is(':checked')) {
            status = 0;
        } else {
            status = 1;
        }
        var id = $(this).data('id');
        var ctrl = $(this).data('ctrl');
        $.get(ctrl + "/" + id + "/" + status + "", function (response, state) {
            if (response) {
                $('.check-ko').remove();
                elem.parent('label').before('<i class="fa fa-check check-ok"></i>');
                $('.check-ok').delay(1000).fadeOut();
            } else {
                elem.parent('label').before('<i class="fa fa-exclamation-triangle check-ko"></i>');
            }
        });
    });

    // 4. Confirmación de acciones:
    $(document).on('click', '.btn-confirm', function (e) {
        e.preventDefault();
        var linkURL = $(this).attr("href");
        var msg = $(this).data('msg');

        swal.fire({
            title: msg,
            text: "",
            icon: 'warning',
            iconColor: color_warning,
            showCancelButton: true,
            confirmButtonColor: color_primary,
            cancelButtonColor: color_danger,
            confirmButtonText: txtContinuar,
            cancelButtonText: txtCancelar
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = linkURL;
            }
        });
    });

    // 4.1. Confirmación de acción por formulario:
    $(document).on('click', '.frm-confirm', function (e) {
        e.preventDefault();
        var msg = $(this).data('msg');
        var form = $(this).closest('form');

        swal.fire({
            title: msg,
            text: "",
            icon: 'warning',
            iconColor: color_warning,
            showCancelButton: true,
            confirmButtonColor: color_primary,
            cancelButtonColor: color_danger,
            confirmButtonText: txtContinuar,
            cancelButtonText: txtCancelar
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });

    // 4.2.
    $(document).on('click', '.btnDelete', function (e) {
        e.preventDefault();
        var msg = $(this).data('msg');
        var model = $(this).data('model');
        var id = $(this).data('id');

        swal.fire({
            title: msg,
            text: "",
            icon: 'warning',
            iconColor: color_warning,
            showCancelButton: true,
            confirmButtonColor: color_primary,
            cancelButtonColor: color_danger,
            confirmButtonText: txtEliminar,
            cancelButtonText: txtCancelar
        }).then((result) => {
            if (result.isConfirmed) {

            }
        });
    });

    // 5. Modals flash.
    if (typeof msg_errores_ != 'undefined' && msg_errores_) {
        $('.invalid-feedback').css('display', 'block').show();
        $('.modal.' + msg_errores_).modal({
            show: true
        });
    }

    // 6. Toggle vista lista - cuadrícula.
    $('.btn-toggle.list').on('click', function () {
        $(this).addClass('active');
        $('.btn-toggle.grid').removeClass('active');
        $('.toggle-view-grid').fadeOut();
        $('.toggle-view-list').delay(400).fadeIn();

        //Cookie:
        var cookie = $(this).data('cookie');
        setCookie(cookie + "View", "list");
    });
    $('.btn-toggle.grid').on('click', function () {
        $(this).addClass('active');
        $('.btn-toggle.list').removeClass('active');
        $('.toggle-view-list').fadeOut();
        $('.toggle-view-grid').delay(400).fadeIn();

        //Cookie:
        var cookie = $(this).data('cookie');
        setCookie(cookie + "View", "grid");
    });

    function ifCookie(cookie) {
        if (cookie == 'grid') {
            $('.btn-toggle.list').removeClass('active');
            $('.btn-toggle.grid').addClass('active');
            $('.toggle-view-list').hide();
            $('.toggle-view-grid').fadeIn();
        }
    }

    // 6.1. Leer cookie productos.
    if ($('#tblProducts').length > 0) {
        var cookie = getCookie('productsView');
        ifCookie(cookie);
    }

    // 6.2. Leer cookie proyectos.
    if ($('#tblProjects').length > 0) {
        var cookie = getCookie('projectsView');
        ifCookie(cookie);
    }

    // 6.3. Leer cookie usuarios.
    if ($('#tblUsers').length > 0) {
        var cookie = getCookie('usersView');
        ifCookie(cookie);
    }

    // 7. Copiar a portapapeles.
    $('.copia').on('click', function () {
        var elem = $(this);
        var text1 = $(this).text();
        text = text1.trim();
        var $temp = $("<input>");
        //lo agregamos a nuestro body
        $("body").append($temp);
        //agregamos en el atributo value del input el contenido html encontrado
        //en el td que se dio click
        //y seleccionamos el input temporal
        $temp.val(text).select();
        //ejecutamos la funcion de copiado
        document.execCommand("copy");
        //eliminamos el input temporal
        $temp.remove();
        elem.append('<span class="warning-copy">' + txtTextoCopiado + '</span>');
        $('.warning-copy').delay(1000).fadeOut();
    });

    // 7.1. Copiar a portapapeles desde link.
    $('.copia2').on('click', function (e) {
        e.preventDefault();
        var elem = $(this);
        var text1 = elem.data('text');
        text = text1.trim();
        var $temp = $("<input>");
        //lo agregamos a nuestro body
        $("body").append($temp);
        //agregamos en el atributo value del input el contenido html encontrado
        //en el td que se dio click
        //y seleccionamos el input temporal
        $temp.val(text).select();
        //ejecutamos la funcion de copiado
        document.execCommand("copy");
        //eliminamos el input temporal
        $temp.remove();
        elem.append('<span class="warning-copy">' + txtTextoCopiado + '</span>');
        $('.warning-copy').delay(1000).fadeOut();
    });

    $('.copia3').on('click', function (e) {
        e.preventDefault();
        var copyText = $(this).data('text');

        /* Select the text field */
        //copyText.select();
        //copyText.setSelectionRange(0, 99999); /* For mobile devices */

        /* Copy the text inside the text field */
        navigator.clipboard.writeText(copyText);

        /* Alert the copied text */
        alert("Copied the text: " + copyText);
    });

    // 8. Destacar imagen.
    $('.btn-featured').on('click', function (e) {
        e.preventDefault();
        var este = $(this);
        var img = $(this).data('img');
        var modulo = $(this).data('modulo');

        if (img && modulo) {
            $.get("/admin/" + modulo + "/set-featured/" + img, function (response, state) {
                $('.btn-featured').removeClass('featured');
                este.addClass('featured');
            });
        }
    });

    // 8.1. Imagen pública.
    $('.btn-public').on('click', function (e) {
        e.preventDefault();
        var este = $(this);
        var img = $(this).data('img');
        var modulo = $(this).data('modulo');

        if (img && modulo) {
            $.get("/admin/" + modulo + "/set-public/" + img, function (response, state) {
                if (response.public) {
                    este.addClass('public');
                } else {
                    este.removeClass('public');
                }
            });
        }
    });

    // 9. Seleccionar empresa.
    $('.select-company').on('change', function (event) {
        var id = event.target.value;
        window.location.href = '/admin/companies/' + id + '/select';
    });

    // 10. Modal selector empresa inicial.
    if ($('#modal-select-company, #modal-modality').length > 0) {
        $('#modal-select-company, #modal-modality').modal({
            backdrop: 'static',
            keyboard: false,
            toggle: true
        });
    }

    // 11. Provincias por país.
    $('#country_id').on('change', function (event) {
        //Limpiamos posibles valores de provincia y población:
        $('#province_id').empty().val('');
        $('#town_id').html('');
        $('#country_id option:selected').each(function () {
            var id = event.target.value;

            if (id == null || id == 0) {
                $('#province_id').attr('disabled', 'disabled');
            } else {
                $('#province_id').removeAttr('disabled');
                $.get("/country_provinces/" + id + "", function (response, state) {
                    $("#province_id").append('<option value="">' + txtProvinciaSelec + '</option>');
                    for (i = 0; i < response.length; i++) {
                        $("#province_id").append('<option value="' + response[i].id + '">' + response[i].name + '</option>');
                    }
                });
            }
        });
    });

    // 12. Poblaciones por provincia.
    $('#province_id').on('change', function (event) {
        //Limpiamos posibles valores de población:
        $('#town_id').empty().val('');
        $('#province_id option:selected').each(function () {
            var id = event.target.value;

            if (id == null || id == 0) {
                $('#town_id').attr('disabled', 'disabled');
            } else {
                $('#town_id').removeAttr('disabled');
                $.get("/province-towns/" + id + "", function (response, state) {
                    $("#town_id").append('<option value="">' + txtPoblacionSelec + '</option>');
                    for (i = 0; i < response.length; i++) {
                        $("#town_id").append('<option value="' + response[i].id + '">' + response[i].name + '</option>');
                    }
                });
            }
        });
    });

    // 13. Habilitar buscador Select2 en modals.
    $('.modal').on('shown.bs.modal', function () {
        $('.modal').removeAttr("tabindex");
    });

    // 14. Valor máximo admitido en un campo.
    $('.max-value').on('change', function () {
        var value = $(this).val();
        var max = $(this).data('maxvalue');

        if (value > max) {
            swal.fire({
                title: txtValorNoSuperiorA + ' ' + max,
                text: "",
                icon: 'warning',
                iconColor: color_warning,
                showCancelButton: false,
                confirmButtonColor: color_danger,
                cancelButtonColor: color_danger,
                confirmButtonText: txtCancelar,
                cancelButtonText: txtCancelar
            }).then((result) => {
                $(this).val('');
                return false;
            });
        }
    });

    // 15. Validar decimales.
    $(document).on('change', '.set-decimals', function () {
        var value = $(this).val();
        //Reemplazamos comas por puntos:
        var value = value.replace(/,/g, '.');
        //Patrón de formato correcto con 2 decimales:
        var regexp = /^-?\d*(\.\d{1})?\d{0,1}$/;
        //7/12/22: se permite incluir valor 0 ( value == '0' || )
        if (!regexp.test(value) ||  value == '') {
            swal.fire({
                title: txtValorIncorrecto,
                text: "",
                icon: 'warning',
                iconColor: color_warning,
                showCancelButton: false,
                confirmButtonColor: color_danger,
                cancelButtonColor: color_danger,
                confirmButtonText: txtCancelar,
                cancelButtonText: txtCancelar
            }).then((result) => {
                $(this).val('');
                return false;
            });
        } else {
            $(this).val(value);
        }
    });

    // 16. Añadir segmento a formulario de generación de patrones.
    $(document).on('click', '.btnAddSegment', function (e) {
        e.preventDefault();
        $('.fa-spinner').remove();

        //Comprobamos si la opción de digits ya ha sido incluida en el patrón:
        var texto = true;
        $('.segment-options').each(function () {
            //console.log($(this).val());
            if ($(this).val() == 'digits') {
                texto = false;
            }
        });

        var segment = $('.clones').html();
        $('.segments').append(segment);
    });
    $(document).on('click', '.btnRemoveSegment', function () {
        $(this).closest('.segment').remove();
    });
    $(document).on('change', '.segment-options', function (event) {
        var elem = $(this).closest('.segment');
        var option = $(this).find('option:selected').val();
        if (option == 'text') {
            elem.find('.segment_text').prop('disabled', false).focus();
        } else {
            if (option == 'digits') {
                $('#submitPattern').prop('disabled', false);
            }
            elem.find('.segment_text').prop('disabled', true);
        }
    });

    // 17. Búsqueda nuevas notificaciones + mensajes chat.
    // var newNotifications = setInterval(function(){
    //     //Notificaciones:
    //     $.get("/admin/new-notifications", function(response, state){
    //         if(response){
    //             $('#notificationsList .notification_').remove();
    //             var html = '';
    //             var qty = 0;

    //             for(var notif in response){
    //                 html += '<li class="notification_">';
    //                     html += '<a href="/admin/notifications/'+response[notif].id+'/show">';
    //                         html += '<i class="fa fa-envelope"></i> ';
    //                         html += response[notif].data.title;
    //                         html += '<small class="text-warning pull-right">';
    //                             html += response[notif].time_ago;
    //                         html += '</small>';
    //                     html += '</a>';
    //                 html += '</li>';

    //                 qty++;
    //             }

    //             $('#withoutNotifications').fadeOut();
    //             $('#notificationsList').prepend(html);
    //             $('#notificationsQty').text(qty).fadeIn();
    //         }
    //     });

    //     //Mensajes Chat:
    //     $.get("/admin/chat/new-messages", function(response, state){
    //         if(response){
    //             $('#chat-messages-num').text(response).fadeIn();
    //         }
    //     });
    // }, 30000);

    // 18. Rotación caret arrow.
    $(document).on('click', '.toggle-arrow', function () {
        elem = $(this);
        $('.toggle-arrow .fa').not(this).rotate(0);
        if (elem.hasClass('collapsed')) {
            elem.find('.fa').rotate(0);
        } else {
            elem.find('.fa').rotate(180);
        }
    });

    // 19. Producto favorito.
    $(document).on('click', '.btnAddFavorite', function () {
        elem = $(this);
        var product = $(this).data('product');
        $.get("/admin/product-favorites/" + product + "/add-remove", function (response, state) {
            if (response == 'save') {
                elem.html('<i class="fa fa-star destacado"></i> ' + txtFavorito);
            } else {
                elem.html('<i class="fa fa-star"></i> ' + txtFavoritosAdd);
            }
        });
    });

    // 20. Mini-modal.
    $(document).on('click', '.btnMiniModal', function () {
        var elem = $(this);
        var next = elem.next('.mini-modal-content');
        //Cerrar:
        if (elem.hasClass('opened')) {
            elem.next('.mini-modal-content').animate({
                width: 0,
                height: 0,
            }, 500);
            next.find('.mini-modal-items').fadeOut();
            elem.removeClass('opened btn-warning');
            //Abrir:
        } else {
            //Contamos número de opciones para obtener altura del div:
            var h = parseInt(next.find('p').length * 35) + parseInt(20);

            $('.mini-modal-content').animate({
                width: 0,
                height: 0,
            }, 500);
            $('.btnMiniModal').removeClass('opened btn-warning');
            $('.mini-modal-items').fadeOut();

            next.animate({
                width: "180px",
                height: h + "px",
            }, 500);
            next.css('z-indez', '999');
            next.find('.mini-modal-items').fadeIn();
            elem.addClass('opened btn-warning');
        }
    });

    // 20.1. Modificar opción mini-modal.
    $(document).on('click', '.mm-option', function () {
        var elem = $(this);
        var id = elem.data('id');
        var option = elem.data('option');
        var text = elem.text();
        var controller = $('#dtController').val();
        $.get("/admin/" + controller + "/" + id + "/status/" + option, function (response, state) {
            if (response) {
                elem.siblings().removeClass('selected');
                elem.addClass('selected');
                $('.xoption-' + id).text(text);

                $('.mini-modal-content').animate({
                    width: 0,
                    height: 0,
                }, 500);
                $('.btnMiniModal').removeClass('opened btn-warning');
                $('.mini-modal-items').fadeOut();
            }
        });
    });

    // 21. Vuelta a top page.
    $("#toTop").click(function (e) {
        e.preventDefault();
        $("html, body").animate({scrollTop: 0}, 1000);
    });

    // 21.1. Mostrar - ocultar botón de top page.
    $(document).scroll(function () {
        var y = $(this).scrollTop();
        if (y > 400) {
            $('#toTop').fadeIn();
        } else {
            $('#toTop').fadeOut();
        }
    });

    // 22. Máscara para horas.
    $(document).on('change', '.maskHours', function () {
        var hour = $(this).val();
        if (hour.includes(':')) {
            var p = hour.split(':');
            var hours = parseInt(p[0]) > 0 && parseInt(p[0]) < 24 ? p[0] < 10 ? '0' + p[0] : p[0] : '00';
            //if(hours != '00' && !hour.includes('0') && hours < 10) hours = '0'+hours;
            var minutes = parseInt(p[1]) > 0 && parseInt(p[1]) < 60 ? p[1] : '00';
            if (minutes != '00' && !minutes.includes('0') && minutes < 10) minutes = '0' + minutes;
            var new_hour = hours + ':' + minutes;
            $(this).val(new_hour);

        } else if (Number.isInteger(parseInt(hour))) {
            //Eliminamos 0 a la izquierda:
            hour = Number(hour);
            if (hour < 23) {
                if (hour < 10) hour = '0' + hour;
                $(this).val(hour + ':00');
            } else {
                $(this).val('');
            }

        } else {
            $(this).val('');
        }
    });

    // 23. Histórico de horas trabajadas en tarea.
    $(document).on('click', '.btnWorkingTime', function () {
        var task_id = $(this).data('task');
        $('#workingTimeTaskId').val(task_id);
        $('#working-time-historical').empty().append('<i class="ml-2 fa fa-spinner fa-spin"></i>').fadeIn();
        $.get("/admin/tasks/" + task_id + "/working-time", function (response, state) {
            if (response) {
                var total = 0;

                var html = '<div class="row"><table class="table">';
                html += '<thead><tr><th class="text-center">' + txtFecha + '</th><th class="text-center">' + txtHoras + '</th><th class="text-center">' + txtAcciones + '</th></tr></thead>';
                html += '<tbody>';
                for (j = 0; j < response.length; j++) {
                    total = parseInt(total) + parseInt(response[j].time_);
                    var hours = response[j].time_ / 60;
                    html += '<tr>';
                    html += '<td class="text-right">' + response[j].date_ + '</td>';
                    html += '<td class="text-right">' + hours + 'h.</td>';
                    html += '<td class="text-right">';
                    html += '<a href="/admin/tasks/' + task_id + '/delete-working-time/' + j + '" class="btn btn-xs btn-danger btn-confirm" rel="tooltip" title="' + txtEliminar + '" data-msg="' + txtHorasConfirmEliminar + '"><i class="fa fa-trash"></i></a>';
                    html += '</td>';
                    html += '</tr>';
                }
                html += '</tbody>';
                html += '<tfoot>';
                html += '<tr>';
                total = total / 60;
                html += '<td class="text-right">' + txtTotal + '</td>';
                html += '<td class="text-right">' + total + 'h.</td>';
                html += '<td></td>';
                html += '</tr>';
                html += '</tfoot>';
                html += '</table></div>';

                if (total > 0) {
                    $('#working-time-historical').html(html);
                } else {
                    $('#working-time-historical').html('<span class="text-warning">' + txtResultadosNoEncontrados + '</span>');
                }

            } else {
                $('.fa-spinner').remove();
                $('#working-time-historical').append('<span class="text-warning">' + txtResultadosNoEncontrados + '</span>');
            }
        });
    });

    // 24. Selector de modalidad de presupuesto.
    $('#selectBudgetModality').on('change', function (event) {
        var option = event.target.value;

        $('#budgetForms, #noBudgetForms, #activeModule').fadeOut();
        $('#sbtNewBudget').prop('disabled', false);

        //Si la opción seleccionada es por formulario (option 10), comprobamos que existan:
        if (option == 10) {
            //Comprobamos que el cliente no tenga una formulario vinculado por defecto:
            var customer_id = $('#formCustomerId').val();

            if (customer_id != '') {
                $.get("/admin/customers/own-budget-form/" + customer_id + "/", function (response, state) {
                    if (response.budget_form_id) {
                        //Marcamos formulario por defecto:
                        var arr = [response.budget_form_id];

                        //Atención al modo de marcar como selected las opciones en un Select2:
                        $('#budgetForm').val(arr);
                        $('#budgetForm').trigger('change');
                    }

                    var numforms = $('#numForms').val();
                    if (numforms > 0) {
                        $('#budgetForms').slideDown(500);

                    } else {
                        $('#sbtNewBudget').prop('disabled', true);
                        $('#noBudgetForms').fadeIn();
                    }
                });
            }
        }
    });

    // 25. Seleccionar icono.
    $('.iconGallery').on('click', function () {
        $('.iconGallery').removeClass('selected');
        $(this).addClass('selected');
        var icono = $(this).data('icono');
        $('.iconValue').val(icono);
    });

    if (typeof blocked_timetables != 'undefined' && blocked_timetables) {
        var stopInterval = setInterval(function () {
            var f = new Date();
            var hours = (f.getHours() < 10) ? '0' + f.getHours() : f.getHours();
            var minutes = (f.getMinutes() < 10) ? '0' + f.getMinutes() : f.getMinutes();
            var seconds = (f.getSeconds() < 10) ? '0' + f.getSeconds() : f.getSeconds();
            hora = hours + ":" + minutes + ":" + seconds;

            if (hora > blocked_timetables) {
                $('.blocked-hour').remove();
                $('.HourlyControl.subItems_').fadeIn();
                $('.blocked-enter').fadeOut();
                // $('.btnHourlyControl').each(function(index){
                //     $(this).removeClass('one-click');
                // });

                //Paramos el setInterval:
                clearInterval(stopInterval);
            }
        }, 2500);
    }

    // 26. Modal editar item.
    $(document).on('click', '.btnEditItem', function () {
        var id = $(this).data('id');
        $('#frmEditItem').trigger("reset");
        $('#itemObservations_').trumbowyg('html', '');
        $.get("/admin/items/" + id + "/edit", function (response, state) {
            if (response) {
                console.log(response);
                $('#itemId_').val(id);
                $('#itemConcept_').val(response.concept);
                $('#itemObservations_').trumbowyg('html', response.observations);

                //Precio por porcentaje:
                if (response.price == '0' && response.calculated_by_percent > 0) {
                    $('.price-fixed_').fadeOut(400, function () {
                        $('.price-by-percent_').fadeIn();
                    });
                    $('#itemCalculatedByPercent_').val(response.calculated_by_percent);
                    $('#itemPrice_').val('');

                    //Precio fijo:
                } else {
                    $('.price-by-percent_').fadeOut(400, function () {
                        $('.price-fixed_').fadeIn();
                    });
                    $('#itemQty_').val(response.qty);
                    $('#itemDays_').val(response.days);
                    $('#itemPrice_').val(response.price);
                    $('#itemDiscount_').val(response.discount);
                    $('#itemIva_').val(response.iva);
                    $('#itemCalculatedByPercent_').val('');
                }
            }
        });
    });

    // 27. Aplicar descuento por defecto del cliente a líneas del pedido, tanto nueva como edición.
    $('.btnApplyDiscount').on('click', function (e) {
        e.preventDefault();
        var discount = $(this).data('discount');
        $('#productDiscount').val(discount);
    });
    $('.btnApplyDiscount_').on('click', function (e) {
        e.preventDefault();
        var discount = $(this).data('discount');
        $('#itemDiscount_').val(discount);
    });

    // 28. Cálculo precio + precio con iva en gastos.
    $('#expenseAmount').on('change', function () {
        var iva = $('#expenseIva').val();
        var amount = $('#expenseAmount').val();
        if (iva > 0 && !isNaN(amount)) {
            var amount_with_iva = amount * iva / 100;
            var total = parseFloat(amount) + parseFloat(amount_with_iva);
            $('#expenseAmount_with_iva').val(total.toFixed(2));
        }
    });
    $('#expenseAmount_with_iva').on('change', function () {
        var iva = $('#expenseIva').val();
        var amount_with_iva = $('#expenseAmount_with_iva').val();
        if (iva > 0 && !isNaN(amount_with_iva)) {
            var amount = amount_with_iva / (parseFloat(iva / 100) + parseInt(1));
            $('#expenseAmount').val(amount.toFixed(2));
        }
    });

    // 29. Guardar preferencia.
    $('#preferenceName').on('change', function(e){
        e.preventDefault();
        var data = $('#frm_preference').serialize();
        $('#preferenceName').val('');

        $.ajax({
            url: "/admin/preferences/store",
            type: "POST",
            data: data,
            cache: false,
            datatype: "JSON",
            success: function(response){
                //showSuccess(response);
            }
        });
    });

    // 30. Selector cuenta de gastos por estructura.
    $('#accountingStructure').on('change', function (event) {
        //Limpiamos posibles valores heredados:
        $('#accountingConcept').empty().val('');
        $('#accountingStructure option:selected').each(function () {
            var id = event.target.value;

            if (id == null || id == 0) {
                $('#accountingConcept').attr('disabled', 'disabled');
            } else {
                $('#accountingConcept').removeAttr('disabled');
                $.get("/admin/accounting-concepts/filter/" + id + "", function (response, state) {
                    $("#accountingConcept").append('<option value="" selected>' + txtConceptoSelec + '</option>');
                    for (i = 0; i < response.length; i++) {
                        $("#accountingConcept").append('<option value="' + response[i].id + '">' + response[i].name + '</option>');
                    }
                });
            }
        });
    });

    // 31. Desplegar bloque de permisos.
    $('.btnSlideAllPermissions').on('change', function(){
        if($(this).is(':checked')){
            $('.ibox').each(function(){
                $(this).addClass('border-bottom');
                $(this).find('.ibox-content').slideDown();
            });
            
        }else{
            $('.ibox').each(function(){
                $(this).removeClass('border-bottom');
                $(this).find('.ibox-content').slideUp();
            });
        } 
    });

});
