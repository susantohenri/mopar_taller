<?php include 'header.php'; ?>

<style type="text/css">
.botonera a{
	display: block;
    background: #6aa1ca;
    color: #fff;
    height: 100px;
    border-radius: 20px;
    line-height: 100px;
    font-size: 20px;
}

.botonera a i.fa{
	font-size: 60px;
    color: #b2cbde;
    float: left;
    margin: 20px 30px;
    -webkit-transition: all 0.5s ease;
	-moz-transition: all 0.5s ease;
	-o-transition: all 0.5s ease;
	transition: all 0.5s ease;
}

.botonera a:hover{
	background: #4281af;
	text-decoration: none;
}

.botonera a:hover i.fa{
	color: #fff;
	transform: scale(1.2);
}

</style>

<div class="container" style="float: left; margin-top: 40px;"> 
	<div class="row botonera">
		<div class="col-3">
			<a href="admin.php?page=mopar-vehiculos">
				<i class="fa fa-car"></i>
				Vehiculos
			</a>
		</div>
		<div class="col-3">
			<a href="admin.php?page=mopar-clientes">
				<i class="fa fa-address-card"></i>
				Clientes
			</a>
		</div>
		<div class="col-3">
			<a href="admin.php?page=mopar-ot">
				<i class="fa fa-file-text"></i>
				OTs
			</a>
		</div>
		<div class="col-3">
			<a href="admin.php?page=mopar-modelos">
				<i class="fa fa-suitcase"></i>
				Modelos
			</a>
		</div>
	</div>	
</div>

<?php include 'footer.php'; ?>