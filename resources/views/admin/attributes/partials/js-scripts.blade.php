<script>
$(function(){
	var data_types = {!! json_encode($data_types) !!};
	//Valores según el tipo de dato:
	$('#dataTypeSelect').on('change', function(event){
        var option = $('#dataTypeSelect option:selected').val();
        if(data_types[option][1] && !Array.isArray(data_types[option][1])){
        	$('#setValues').slideDown();	

        	//Tipo Color:
        	if(data_types[option][2] == 'color'){
        		$('.typeColor').show();
        		$('.typeOpciones').hide();
        		$('small').hide();

        	//Tipo Opciones:
        	}else{
        		$('.typeColor').hide();
        		$('.typeOpciones').show();
        		$('small').show();
        	}

        }else{
        	$('#setValues').slideUp();	
        }
    });

    //Añadir color:
    $('.btnAddColor').on('click', function(e){
    	e.preventDefault();
    	var color = $('#cPicker').val();
    	$('#colorName').val('');
    	$('#frmSetColor .input-group-addon').css('background-color', color);
    	$('#frmSetColor input[type=text]').focus();
    	$('#colorCode').val(color);
    });
    $('#frmSetColor').on('submit', function(e){
    	e.preventDefault();
    	var values = $('#valuesColors').val();
    	var code = $('#colorCode').val();
    	var name = $('#colorName').val();
    	values += ' '+code+'-'+name+' ';
    	$('#valuesColors').val(values);
    	var color_ = '<span style="background-color: '+code+';">'+code+'-'+name+' <i class="fa fa-times ml-2"></i></span>';
    	$('.colors').append(color_);
    	$('#modal-set-color').modal('toggle');
    });

    //Eliminar color:
    $(document).on('click', '.colors .fa', function(){
        var color = $(this).parent().text();
        var values = $('#valuesColors').val();
        values = values.replace(color, '');
        $('#valuesColors').val(values);
        $(this).parent().remove();
    });
});
</script>