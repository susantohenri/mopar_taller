<?php include 'header.php'; ?>

<div class="box pr-4">
	<div class="box-header mb-4">
		<h2 class="font-weight-light text-center text-muted float-left"> Preparación Contable</h2>
		<form style="float: right;" method="POST">
			<select name="filter_month">
				<?php foreach ([1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12] as $month) : ?>
					<option value="<?= $month ?>" <?= $month == $filter_month ? 'selected':'' ?>><?= Mopar::getNombreMes($month) ?></option>
				<?php endforeach ?>
			</select>
			<select name="filter_year">
				<?php for ($year = $min_year; $year <= $max_year; $year++) : ?>
					<option value="<?= $year ?>" <?= $year == $filter_year ? 'selected':'' ?>><?= $year ?></option>
				<?php endfor ?>
			</select>
			<button>Filter</button>
			<button name="filter_reset">Reset</button>
		</form>
		<div class="clearfix"></div>
	</div>
	<div class="box-body">
		<table class="table table-striped table-bordered" id="tabla_solicituds">
			<thead>
				<tr>
					<th>#</th>
					<th> FECHA </th>
					<th> CLIENTE </th>
					<th> VEHICULO </th>
					<th> TOTAL </th>
					<th> GASTOS </th>
					<th> TIPO DE DOCUMENTO </th>
					<th class="text-center">ACCIONES</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($solicituds as $solicitud) : ?>
					<tr data-regid="<?php echo $solicitud->id; ?>">
						<td data-regid="<?php echo $solicitud->id; ?>"> <?php echo $solicitud->id; ?> </td>
						<td><?= date('d-m-Y', strtotime($solicitud->regdate)) ?></td>
						<td data-cliente="<?php echo $solicitud->cliente_id; ?>"> <?php echo Mopar::getNombreCliente($solicitud->cliente_id, false) ?> </td>
						<td data-vehiculo="<?php echo $solicitud->vehiculo_id; ?>"> <?php if (0 != $solicitud->vehiculo_id) echo Mopar::getNombreVehiculo($solicitud->vehiculo_id) ?> </td>
						<td style="text-align: right;">$ <?= $solicitud->total ?></td>
						<td style="text-align: right;">$ <?= $solicitud->gastos ?></td>
						<td><?= $solicitud->tipo_de_documento ?></td>
						<td class="text-center" style="white-space: nowrap;">
							<button class="btn btn-warning" data-toggle="tooltip" title="Iniciar Cotización"><i class="fa fa-list"></i></button>
							<button class="btn btn-success" data-toggle="tooltip" title="Descartar"><i class="fa fa-plus"></i></button>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</div>

<script>
	$(document).ready(function() {
		$('#tabla_solicituds').DataTable({
			"ordering": false,
			"columnDefs": [{
				"width": "10%",
				"targets": 1
			}, {
				"width": "20%",
				"targets": 3
			}, {
				"width": "15%",
				"targets": 6
			}]
		});
	});
</script>

<?php include 'footer.php'; ?>