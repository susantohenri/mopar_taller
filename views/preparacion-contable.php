<?php
$inserted = false;
$updated = false;

?>

<?php include 'header.php'; ?>

<div class="box pr-4">
	<div class="box-header mb-4">
		<h2 class="font-weight-light text-center text-muted float-left"> Preparación Contable</h2>
		<select style="float: right;">
			<option>January</option>
		</select>
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
						<td></td>
						<td class="text-center" style="white-space: nowrap;">
							<button class="btn btn-warning btnProceedWithoutIngreso" data-toggle="tooltip" title="Iniciar Cotización"><i class="fa fa-list"></i></button>
							<button class="btn btn-danger btnMotivo" data-toggle="tooltip" title="Descartar"><i class="fa fa-plus"></i></button>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</div>

<script>
	$(document).ready(function() {

		<?php if ($inserted) { ?>
			$.alert({
				type: 'green',
				title: false,
				content: 'Solicitud ingresada correctamente'
			})
		<?php } ?>


		<?php if ($updated) { ?>
			$.alert({
				type: 'green',
				title: false,
				content: 'Solicitud actualizada correctamente'
			})
		<?php } ?>

		<?php if ($inserted || $updated) { ?>
			location.href = '<?php bloginfo('wpurl') ?>/wp-admin/admin.php?page=preparacion-contable';
		<?php } ?>


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