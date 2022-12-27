@if(!Auth::user()->joke)
    <div class="modal fade" id="modal-joke" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">FATAL ERROR!!</h4>
                </div>

                <div class="modal-body">
                    <div class="form-group row justify-content-md-center">
                        {{-- STEP 1 --}}
                        <div class="col-8 text-center joke-step mt-5" id="step1">
                            <img src="{{ asset('img/joke/icon-error.png') }}" alt="">
                            <h3 class="d-inline-block text-bold ml-2 mb-5">FATAL ERROR: exception fckng666</h3>
                            <p>Se ha detectado una intrusión GRAVE por virus que puede estar afectando al disco duro de tu equipo. 
                            <br>
                            Por favor, sigue los siguientes pasos para ejecutar el anti-virus.
                            </p>
                            <br><br>

                            <button class="btn btn-danger btn-step1">CONTINUAR</button>
                        </div>    

                        {{-- STEP 2 --}}
                        <div class="col-8 text-center joke-step mt-5" id="step2">
                            <h3>ANALIZANDO...</h3>
                            <div class="progress progress-striped active mb-2">
                                <div class="progress-bar progress-bar-danger">
                                    <span class="sr-only">40% Complete (success)</span>
                                </div>
                            </div>

                            <div class="step2-messages mb-4">                         
                            </div>

                            <div class="step2-messages2 mb-4 text-bold text-danger"> 
                                <b>Se ha hallado un malware de categoría DEFCON 1, exception fckng666.
                                <br>
                                Podrían haberse eliminado archivos importantes de la carpeta<br> 'Mis Documentos'.
                                <br>
                                Vamos a proceder con los siguientes pasos para tratar de recuperar los archivos extraviados. </b>                      
                            </div>
                                
                            <button class="btn btn-warning btn-step2">EMPEZAR</button>
                        </div>  

                        {{-- STEP 3 --}}
                        <div class="col-8 text-center joke-step mt-5" id="step3">
                            <h3>Debes responder a las siguientes cuestiones para conocer los archivos que debemos recuperar.</h3>
                            <br>

                            <p>¿Has visitado websites del tipo... ya sabes... de los que no debes visitar delante de menores?</p>
                            <br>

                            <button class="btn btn-info btn-step3">SI</button>
                            <button class="btn btn-danger btn-step3">SI</button>
                        </div>  

                        {{-- STEP 4 --}}
                        <div class="col-8 text-center joke-step mt-5" id="step4">
                            <h3>Vaya... canallita. Y aún te extrañarás de que tu equipo se haya infectado con un virus.</h3>
                            <br>

                            <p>¿Escuchas reggaeton, trap o electro-latino en este equipo?</p>
                            <br>

                            <button class="btn btn-info btn-step4">NI DE COÑA</button>
                            <button class="btn btn-danger btn-step4_">CLARO, BRO!</button>
                        </div>  

                        {{-- STEP 5 --}}
                        <div class="col-8 text-center joke-step mt-5" id="step5_1">
                            <h3>¡Buffff...! Menos mal. </h3>
                            <br>

                            <img src="{{ asset('img/joke/ufff.gif') }}" alt="" class="explosion">
                            <br><br>

                            <button class="btn btn-info btn-step5">CONTINUAR</button>
                        </div>

                        <div class="col-8 text-center joke-step mt-5" id="step5_2">
                            <h3 class="text-danger">¡Insensato! tu equipo explotará en 2 segundos</h3>
                            <br>

                            <img src="{{ asset('img/joke/laptop_explosion.gif') }}" alt="" class="explosion">
                            <br><br>

                            <button class="btn btn-info btn-step5">CONTINUAR</button>
                        </div>

                        {{-- STEP 6 --}}
                        <div class="col-8 text-center joke-step mt-5" id="step6">
                            <h3>¿Te ha quedado más o menos esta cara al ver el aviso de<br>FATAL ERROR?</h3>
                            <br>

                            <img src="{{ asset('img/joke/horror_scream.gif') }}" alt="">
                            <br><br>

                            <button class="btn btn-info btn-step6">CONTINUAR</button>
                        </div>

                        {{-- STEP 7 --}}
                        <div class="col-8 text-center joke-step mt-5" id="step7">
                            <h3>¡Feliz día de los Santos Inocentes!</h3>
                            <br>

                            <img src="{{ asset('img/joke/icono-inocente.png') }}" alt="">
                            <br><br>

                            <button class="btn btn-info btn-step7">FIN</button>
                        </div>
                    </div>                  
                       
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    $(function(){
        //To Step 2:
        $('.btn-step1').on('click', function(){
            $('#step1').fadeOut(function(){   
                $('#step2').fadeIn();
            });

            $('.progress-bar')
                .delay(2000)
                .addClass('expand', 10000);

            //Mensajes:
            var textChanges = [
                { delay: 2000,    text: 'Analizando unidades en busca de malware...'},
                { delay: 5000,    text: 'Analizando directorios principales...'},
                { delay: 8000,    text: 'Analizando subdirectorios de condensador de fluzo...'}
            ];
            function task(i) {
                setTimeout(function() {
                    $('.step2-messages').text(textChanges[i].text);
                }, textChanges[i].delay);
            }
            for(let i=0; i<textChanges.length; i++){
                task(i);
            }

            $('.step2-messages2, .btn-step2').delay(13000).fadeIn();
        });

        //To Step 3:
        $('.btn-step2').on('click', function(){
            $('#step2').fadeOut(function(){   
                $('#step3').fadeIn();
            });
        });

        //To Step 4:
        $('.btn-step3').on('click', function(){
            $('#step3').fadeOut(function(){   
                $('#step4').fadeIn();
            });
        });

        //To Step 5:
        $('.btn-step4').on('click', function(){
            $('#step4').fadeOut(function(){   
                $('#step5_1').fadeIn();
            });
        });
        $('.btn-step4_').on('click', function(){
            $('#step4').fadeOut(function(){   
                $('#step5_2').fadeIn();
                $('.explosion').delay(3000).fadeIn();
            });
        });

        //To Step 6:
        $('.btn-step5').on('click', function(){
            $('#step5_1, #step5_2').fadeOut(function(){   
                $('#step6').fadeIn();
            });
        });
        
        //To Step 7:
        $('.btn-step6').on('click', function(){
            $('#step6').fadeOut(function(){   
                $('#step7').fadeIn();
            });
        });

        //Close:
        $('.btn-step7').on('click', function(){
            $('#modal-joke').modal('toggle');

            $.get("admin/close-joke");
        });
    });
    </script>
    @endpush
@endif
