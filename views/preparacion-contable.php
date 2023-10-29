<?php include 'header.php'; ?>

<div class="box pr-4">
	<div class="box-header mb-4">
		<h2 class="font-weight-light text-center text-muted float-left"> Preparación Contable</h2>
		<form style="float: right;" method="POST">
			<select name="filter_month">
				<?php foreach ([1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12] as $month) : ?>
					<option value="<?= $month ?>" <?= $month == $filter_month ? 'selected' : '' ?>><?= Mopar::getNombreMes($month) ?></option>
				<?php endforeach ?>
			</select>
			<select name="filter_year">
				<?php for ($year = $min_year; $year <= $max_year; $year++) : ?>
					<option value="<?= $year ?>" <?= $year == $filter_year ? 'selected' : '' ?>><?= $year ?></option>
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
							<button class="btn btn-warning" data-toggle="tooltip"><i class="fa fa-list"></i></button>
							<button class="btn btn-success btn-add-expense" data-toggle="tooltip"><i class="fa fa-plus"></i></button>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</div>

<div class="modal fade" id="modalAddExpense" tabindex="-1" role="dialog" aria-labelledby="addExpenseLabel" aria-hidden="true">
	<form method="POST">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Agregar Gasto</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="form-row">
						<div class="form-group col-md-6">
							<div class="input-group">
								<div class="input-group-prepend">
									<span class="input-group-text">Proveedor</span>
								</div>
								<input type="text" name="proveedor" class="form-control" required>
							</div>
						</div>
						<div class="form-group col-md-6">
							<div class="input-group">
								<div class="input-group-prepend">
									<span class="input-group-text">Monto</span>
								</div>
								<input type="text" name="monto" class="form-control" required>
							</div>
						</div>
						<div class="form-group col-md-12">
							<div class="input-group">
								<div class="input-group-prepend">
									<span class="input-group-text">Detaile</span>
								</div>
								<textarea class="form-control" name="detaile" required></textarea>
							</div>
						</div>
						<div class="form-group col-md-1">
						</div>
						<div class="form-group col-md-11">
							<?php foreach (['FACTURA', 'BOLETA', 'SIN COMPROBANTE'] as $tipo_de_documento) : ?>
								<br><input type="radio" name="tipo_de_documento" value="<?= $tipo_de_documento ?>" required><?= $tipo_de_documento ?>
							<?php endforeach ?>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<input type="hidden" name="solicitud_id">
					<button type="button" class="btn btn-secondary" data-dismiss="modal"> <i class="fa fa-times"></i> Cerrar y volver</button>
					<button type="submit" class="btn btn-success" name="add_expense">Guardar <i class="fa fa-save"></i> </button>
				</div>
			</div>
		</div>
	</form>
</div>

<script>
	$(document).ready(function() {
		<?php if (!empty($alert)) : ?>
			$.alert({
				title: false,
				type: '<?= $alert['type'] ?>',
				content: '<?= $alert['content'] ?>'
			});
		<?php endif ?>

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

		const add_expense_modal = $(`#modalAddExpense`)
		const monto = $(`[name=monto]`)
		monto.keyup(() => {
			var typed = monto.val()
			typed = typed.replace(`$`, ``).replace(`,`, ``)
			typed = parseInt(typed)
			if (isNaN(typed)) typed = 0
			typed = typed.toString()
			typed = typed.replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,")
			monto.val(`$${typed}`)
		})

		$(`.btn-add-expense`).click(function() {
			add_expense_modal.find(`[type=text], textarea`).val(``)
			add_expense_modal.find(`[type=radio]`).prop(`checked`, false)
			const solicitud_id = $(this).parents(`tr`).attr(`data-regid`)
			add_expense_modal.find(` [name = solicitud_id] `).val(solicitud_id)
			add_expense_modal.modal(`show`)
		})

		add_expense_modal.find(`form`).submit(() => {
			monto.val(monto.val().replace(`$`, ``).replace(`,`, ``))
		})
	});
</script>

<?php include 'footer.php'; ?>