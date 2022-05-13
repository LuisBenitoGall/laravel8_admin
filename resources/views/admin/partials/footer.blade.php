				{{-- Modals --}}
				{{-- Translations: --}}
				@include('admin.languages.partials.modal-translations')

				{{-- Session Company: --}}
				@include('admin.companies.partials.modal-select-company')

				{{-- Notes --}}
				@include('admin.notes.partials.modal-add-note')
			</div>
			{{-- End Page Content --}}

			<footer class="footer">
				<div class="float-right">
					Powered by ME
				</div>
				<div>
					&copy; {{ date('Y') }}
				</div>	
			</footer>
		</div>
		{{-- End page-wrapper --}}
	</div>
    {{-- End wrapper --}}

    {{-- Scripts: --}}
	{{-- Translations: --}}
	@include('admin.partials.translations-js')

	{{-- Mostrar modal de formulario con errores: --}}
	@if($errors && !$errors->isEmpty() && $errors->first('accion'))
		<script>
		var msg_errores_ = '{{ $errors->first('accion') }}';
		</script>
	@endif

    <script>
	//Variables globales:
	var idm = '{{ App::getLocale() }}';     		//Idioma
	var base_url = '{{ url()->current() }}';		//URL
	var companyCurrency = "{{ isset($currency)? $currency:config('constants.CURRENCY_') }}";
	var color_primary = '#23c6c8';
	var color_danger = '#ed5565';
	var color_info = '#19ABD4';
	var color_success = '#18a689';
	var color_warning = '#f8ac59';
	var color_chart_success = "#a3e1d4";
	var color_chart_warning = "#e1d4a3";
	var color_chart_danger = "#f16688";
	var color_chart_grey = "#dedede";
	</script>

    <script src="{{ asset('js/popper.min.js') }}"></script>
    {{-- JQuery UI 
		Atención a la posición que debe ocupar UI respecto a bootstrap.min.js, y al script bridge que va a continuación. En otro orden se producen conflictos entre ambas librerías.
    --}}
    <script src="{{ asset('js/jquery-ui/jquery-ui.min.js') }}"></script>
    <script>
	$.widget.bridge('uibutton', $.ui.button);
	$.widget.bridge('uitooltip', $.ui.tooltip);
	</script>
	<script src="{{ asset('js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/plugins/metisMenu/jquery.metisMenu.js') }}"></script>
    <script src="{{ asset('js/plugins/slimscroll/jquery.slimscroll.min.js') }}"></script>

    {{-- Datatables --}}
    {{-- <script src="{{ asset('js/plugins/dataTables/datatables.min.js') }}" defer></script> --}}
    <script src="{{ asset('js/plugins/dataTables/jquery.dataTables.min.js') }}" defer></script>
    <script src="{{ asset('js/plugins/dataTables/dataTables.bootstrap4.min.js') }}" defer></script>
	<script src="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js" defer></script>
    <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.flash.min.js" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js" defer></script>
    <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.html5.min.js" defer></script>
    <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.print.min.js " defer></script> 
    <script src="https://cdn.datatables.net/rowreorder/1.2.8/js/dataTables.rowReorder.min.js" defer></script>
    <script src="{{ asset('js/custom-datatables.js') }}"></script>

    {{-- Select 2: --}}
	<script src="{{ asset('js/plugins/select2/select2.full.min.js') }}"></script>

	{{-- Datepicker: --}}
	<script src="{{ asset('js/plugins/datapicker/bootstrap-datepicker.js') }}"></script>
	<script src="{{ asset('js/plugins/datapicker/locales/bootstrap-datepicker.'.App::getLocale().'.js') }}" charset="UTF-8"></script>

	{{-- Trumbowyg --}}
	<script src="{{ asset('plugins/trumbowyg/trumbowyg.min.js') }}"></script>
	<script src="{{ asset('plugins/trumbowyg/plugins/upload/trumbowyg.upload.min.js') }}"></script>

	{{-- Sweet Alert 2: --}}
	<script src="{{ asset('js/plugins/sweetalert/sweetalert.min.js') }}"></script>

    {{-- Ladda --}}
    <script src="{{ asset('js/plugins/ladda/spin.min.js') }}"></script>
    <script src="{{ asset('js/plugins/ladda/ladda.min.js') }}"></script>
    <script src="{{ asset('js/plugins/ladda/ladda.jquery.min.js') }}"></script>
    <script src="{{ asset('js/app.js') }}"></script>

    @livewireScripts

    @stack('scripts')
    
	<script src="{{ asset('js/custom.js') }}"></script>

	<script>
	/* Spinner: */
	if($('#spinner_container').length > 0){
		window.onload = function(){
			var contenedor = document.getElementById('spinner_container');
			contenedor.style.visibility = 'hidden';
			contenedor.style.opacity = '0';
		}
	}
	</script>
</body>
</html>