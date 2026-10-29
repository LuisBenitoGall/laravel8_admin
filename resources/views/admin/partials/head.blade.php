<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel 8 Admin') }}</title>

    <meta name="description" content="">
    <meta name="author" content="Luis Benito Gall @lluisbg">

    {{-- Obliga al uso de Javascript, si no envía fuera, en este caso tras 1" --}}
    <noscript>
        <meta http-equiv=refresh content="1; url=/logout"/>
    </noscript>

    {{-- Favicon --}}
    <link rel="shortcut icon" href="{{ asset('img/favicon.ico') }}">

    {{-- <link rel="apple-touch-icon" sizes="57x57" href="/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192"  href="/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff"> --}}

    {{-- Fonts --}}
    <!-- <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap"> -->

    {{-- Datatables --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.1/css/bootstrap.css" rel="stylesheet" type="text/css">
    <link href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css">
    <link href="https://cdn.datatables.net/buttons/1.5.2/css/buttons.dataTables.min.css" rel="stylesheet"/>
    <link href="https://cdn.datatables.net/rowreorder/1.2.8/css/rowReorder.dataTables.min.css" rel="stylesheet"/>

    {{-- Styles --}}
    {{-- <link rel="stylesheet" href="{!! asset('font-awesome/css/font-awesome.css') !!}"> --}}
    <link rel="stylesheet" href="{!! asset('font-awesome/v5/css/all.css') !!}">
    {{-- <link rel="stylesheet" href="{!! asset('css/plugins/iCheck/custom.css') !!}"> --}}
    <link rel="stylesheet" href="{!! asset('css/plugins/ladda/ladda-themeless.min.css') !!}">

    {{-- Select 2 --}}
    <link rel="stylesheet" href="{{ asset('css/plugins/select2/select2.min.css') }}">

    {{-- Datepicker --}}
    <link rel="stylesheet" href="{{ asset('css/plugins/datapicker/datepicker3.css') }}">

    {{-- Trumbowyg --}}
    <link rel="stylesheet" href="{{ asset('plugins/trumbowyg/ui/trumbowyg.min.css') }}">

    @stack('styles')

    <link rel="stylesheet" href="{!! asset('css/animate.css') !!}">
    <link rel="stylesheet" href="{!! mix('css/app.css') !!}">

    @livewireStyles

    <script>
    //Hora actual:
    function horaActual() {
        var f = new Date();
        var hours = (f.getHours() < 10) ? '0' + f.getHours() : f.getHours();
        var minutes = (f.getMinutes() < 10) ? '0' + f.getMinutes() : f.getMinutes();
        var seconds = (f.getSeconds() < 10) ? '0' + f.getSeconds() : f.getSeconds();
        hora = hours + ":" + minutes + ":" + seconds;
        if (document.getElementById('currentHour') !== null) {
            document.getElementById('currentHour').innerHTML = hora;
        }
        setTimeout("horaActual()", 1000);
    }

    //Variables globales:
    var idm = '{{ App::getLocale() }}';             //Idioma
    var base_url = '{{ url()->current() }}';        //URL
    var companyCurrency = "{{ isset($currency)? $currency:config('constants.CURRENCY_') }}";
    var color_primary = "{{ config('constants.COLOR_PRIMARY_') }}";
    var color_danger = "{{ config('constants.COLOR_DANGER_') }}";
    var color_info = "{{ config('constants.COLOR_INFO_') }}";
    var color_success = "{{ config('constants.COLOR_SUCCESS_') }}";
    var color_warning = "{{ config('constants.COLOR_WARNING_') }}";
    var color_chart_success = "{{ config('constants.COLOR_CHART_SUCCESS_') }}";
    var color_chart_warning = "{{ config('constants.COLOR_CHART_WARNING_') }}";
    var color_chart_danger = "{{ config('constants.COLOR_CHART_DANGER_') }}";
    var color_chart_grey = "{{ config('constants.COLOR_CHART_GREY_') }}";
    @if(session('currentCompany'))
        var currentCompany_ = {{ session('currentCompany') }};
    @endif
    </script>

    {{-- JQuery --}}
    {{-- <script src="{{ asset('js/jquery-3.1.1.min.js') }}"></script> --}}
    <script src="{{ asset('js/jquery-3.5.1.min.js') }}"></script>
    {{-- <script src="{{ asset('js/jquery-2.1.1.js') }}"></script> --}}

    {{-- PUSHER --}}
    <script src="https://js.pusher.com/7.0/pusher.min.js"></script>

    {{-- CDN Alpine --}}
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.8.2/dist/alpine.min.js" defer></script>

{{--
    <script src="https://cdn.jsdelivr.net/npm/js-cookie@3.0.1/dist/js.cookie.min.js"></script>
    @if(!auth()->user()->devicePush->onesignal)
        <script>
            let token = '{{csrf_token()}}';
            let user_id = '{{auth()->id()}}';
            let userDevice = Cookies.get('tokenDevice')
            var data = {user_id: user_id, _token: token, token: userDevice};
            $.ajax({
                type: "post",
                url: "{{route('save.push')}}",
                data: data,
                success: function (msg) {
                    console.log('token actualizado', userDevice);
                }
            });
        </script>
    @endif--}}

</head>
