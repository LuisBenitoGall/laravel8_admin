<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

	<title></title>

	<style type="text/css">
	body{
		font-family: sans-serif;
	}
	#main{
		border-collapse: collapse;
		margin: 30px auto;
		max-width: 700px;
		padding: 10px;
		width: 96%;
	}	
	thead tr th{
		background-color: #CCC;
		color: #FFF;
		font-size: 24px;
		padding: 15px;
		text-align: left;
		vertical-align: middle;
		width: 100%;
	}
		thead tr th img{
			margin-right: 25px;
		}
		thead tr th label{
			
		}

	.logo{
		max-width: 150px;
	}

	.content{
		padding: 25px 15px;
	}

	tfoot tr th{
		background-color: #CCC;
		color: #151515;
		font-size: 16px;
		padding: 15px;
		text-align: center;
	}
	</style>
</head>

<body>
	<table id="main">
		<thead>
			<tr>
				<th style="width: 200px;">
					@php
                    $logo = $company->logo? 'img/companies/'.$company->logo:'img/logo.png';
                    @endphp

                    <img class="logo" src="{!! asset($logo) !!}">
                </th>

				<th>
					@if(isset($company))
						{{ $company->name }}
					@else
						{{ config('constants.COMPANY_') }}
					@endif
				</th>
			</tr>
		</thead>	

		<tbody>
			<tr>
				<td colspan="2">
					<div class="content">
						
					

