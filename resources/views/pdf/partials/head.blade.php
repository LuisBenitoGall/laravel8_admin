<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>{!! $header_title !!}</title>

	<style type="text/css">
	@font-face {
  		font-family: 'Montserrat';
  		font-style: normal;
  		font-weight: normal;
  		src: url(/fonts/montserrat/Montserrat-Medium.ttf)  format('truetype');
	}

	@page{
		margin: 230px 0 25px 0; 
	}
	body{
		font-family: 'Helvetica', sans-serif;
		font-size: 14px;
		margin: 0px;
		position: block;
	}
	header{
		height: 60px;
		left: 0;
		padding: 20px 25px;
		position: fixed;
		right: 0;
		top: -230px;
	}	
		header .logo{
			float: left;
			max-height: 50px;
		}
		header .address{
			color: #FFF;
			display: inline-block;
			float: right;
			font-size: .7rem !important;
			line-height: 1.1rem;
			text-align: right;
			vertical-align: top;
			width: 500px;
		}

	#subheader{
		height: 80px;
		left: 0;
		padding: 20px;
		position: fixed;
		right: 0;
		top: -160px;
	}

	.clearfix{
		clear: both;
		display: block;
		float: none;
	}

	h1{
		font-size: 1rem;
		font-weight: bold;	
		margin-bottom: 15px;
		padding-top: 8px;
		text-transform: uppercase;
	}

	h2{
		font-size: .9rem;
		margin: 0 !important;	
	}

	.text-center{
		text-align: center;
	}
	.text-left{
		text-align: left;
	}
	.text-right{
		text-align: right;
	}
	.fwNormal{
		font-weight: normal !important;
	}

	main{
		display: block;
		padding: 15px 25px;
	}
		table{
			border-collapse: collapse;
			width: 100%;
		}
		.tblHeader{
			margin: 0 5px 30px 5px;
		}
			.tblHeader th{
				border: 0 !important;
				text-align: left;
				vertical-align: top;
			}
			.tblHeader div{
				display: block;
				font-size: .7rem;
				font-weight: normal;
				height: 20px;
				width: 100%;
			}
				.tbl-header-left{
					padding-right: 40px;
					width: 300px;	
				}
				.tbl-header-right{
					padding-left: 40px;	
					width: 350px;
				}
				.tbl-header-left div .item-left{
					float: left;
					line-height: 90%;
					text-align: left;
				}
				.tbl-header-left div .item-right{
					float: right;
					text-align: left;
				}

		main .tblBody{
			margin-top: 10px;
		}
			main .tblBody thead th{
				color: #FFF;
				font-size: .7rem;
				padding: 7px;
				text-align: left;
			}
			main .tblBody tbody td{
				border-bottom: 1px solid #EEE;
				font-size: .7rem;
				padding: 7px;
				text-align: left;
			}
				main .tblBody tbody td.td-success{
					background-color: #D4EDDA;
				}
			main .tblBody tfoot td{
				background-color: #EEE;
				color: #666;
				font-size: .7rem;
				font-weight: bold;
				padding: 7px;
			}

	.textos{
		font-size: 10px;
		font-weight: normal;
		margin-top: 80px;
	}
		.textos td{
			padding: 20px 0;
			vertical-align: top;
		}

	p{
		margin: 0 0 4px 0;
	}

	ul{
		margin: 0 !important;
	}
		ul li{
			list-style: none !important;
		}

	.cap-letters{
		text-transform: uppercase;
	}

	footer{
/*		bottom: 0px;*/
		height: auto;
		left: 0;
		margin-top: 60px;
		padding: 10px 25px;
		position: absolute;
		right: 0;
	}
		footer .tblFooter{
			margin: 0;
		}
			footer .tblFooter thead th{
				color: #111;
				font-size: .7rem;
				font-weight: bold;
				padding: 7px 20px 7px 7px;
			}
			footer .tblFooter tbody td{
				color: #FFF;
				font-size: .85rem;
				font-weight: bold;
				padding: 7px 20px 7px 7px;
			}
		footer .footer-textos{
			font-size: 10px;
			font-weight: normal;
			margin: 40px 25px 15px 25px;	
		}
		footer .tblFoot{
			color: #FFF;
			font-size: .7rem;
			margin: 0;
			padding: 20px;
		}
			footer .tblFoot tbody td{
				color: #FFF;
				font-size: 10px;
			}
	</style>
</head>