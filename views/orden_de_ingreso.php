<?php
$inserted = false;
$updated = false;
?>
<?php include 'header.php'; ?>

<div class="box pr-4">
	<div class="box-header mb-4">
		<h2 class="font-weight-light text-center text-muted float-left"> Orden de Ingreso </h2>
		<div class="clearfix"></div>
	</div>
	<div class="box-body">
		<table class="table table-striped table-bordered" id="tabla_solicituds">
			<thead>
				<tr>
					<th>#</th>
					<th> Cliente </th>
					<th> Vehiculo </th>
					<th class="text-center">Acciones</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($solicituds as $solicitud) : ?>
					<tr data-regid="<?php echo $solicitud->id; ?>">
						<td data-regid="<?php echo $solicitud->id; ?>"> <?php echo $solicitud->id; ?> </td>
						<td data-cliente="<?php echo $solicitud->cliente_id; ?>"> <?php echo Mopar::getNombreCliente($solicitud->cliente_id, false) ?> </td>
						<td data-vehiculo="<?php echo $solicitud->vehiculo_id; ?>"> <?php echo Mopar::getNombreVehiculo($solicitud->vehiculo_id) ?> </td>
						<td class="text-center" style="white-space: nowrap;">
							<button type="button" class="btn btn-success btnEdit" data-regid="<?php echo $solicitud->id; ?>" data-toggle="tooltip" title="Editar"><i class="fa fa-pencil"></i></button>
							<a href="<?php bloginfo('wpurl') ?>/wp-content/plugins/mopar_taller/solicitud-pdf.php?id=<?php echo $solicitud->id; ?>" target="_blank" class="btn btn-info" data-toggle="tooltip" title="Ver"><i class="fa fa-search"></i></a>
							<button class="btn btn-danger btnUncomplete" data-toggle="tooltip" title="Eliminar"><i class="fa fa-trash-o"></i></button>
							<button class="btn btn-warning btnProceed" data-toggle="tooltip" title="Iniciar Cotización"><i class="fa fa-list"></i></button>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</div>

<!-- EDITAR Solicitud -->
<div class="modal fade" id="modalEditSolicitud" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
	<form method="post" id="formEditSolicitud" enctype="multipart/form-data">
		<input type="hidden" name="action" value="editar_solicitud">
		<input type="hidden" name="solicitud_id" value="">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Datos de la Solicitud</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="form-row">
						<div class="form-group col-md-6">
							<div class="input-group">
								<div class="input-group-prepend">
									<span class="input-group-text">Cliente</span>
								</div>
								<select name="cliente" class="form-control">
									<option value="">Seleccione</option>
								</select>
							</div>
						</div>
						<div class="form-group col-md-6">
							<div class="input-group">
								<div class="input-group-prepend">
									<span class="input-group-text">Vehiculo</span>
								</div>
								<select name="vehiculo" class="form-control" disabled>
									<option value="">Seleccione Cliente primero</option>
								</select>
							</div>
						</div>
						<div class="form-group col-md-12">
							<div class="input-group">
								<div class="input-group-prepend">
									<span class="input-group-text">Solicitud</span>
								</div>
								<textarea class="form-control" name="solicitud"></textarea>
							</div>
						</div>
					</div>

				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal"> <i class="fa fa-times"></i> Cerrar y volver</button>
				</div>
			</div>
		</div>
	</form>
</div>




<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css">
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
	$(document).ready(function() {
		$(`[name="cliente"]`).css(`display`, `none`).select2({
			theme: `bootstrap4`,
			minimumInputLength: 3,
			ajax: {
				url: `../wp-json/mopar-taller/v1/clientes`
			}
		})

		$(".btnEdit").click(function() {
			solicitud_id = $(this).data('regid');
			$.ajax({
				type: 'POST',
				url: '<?php echo admin_url('admin-ajax.php'); ?>',
				dataType: 'json',
				data: 'action=get_solicitud&solicitud_id=' + solicitud_id,
				beforeSend: function() {
					$(".overlay").show();
				},
				success: function(json) {

					$(".overlay").hide();
					$('#modalEditSolicitud [name=solicitud_id]').val(json.solicitud.id);

					$('#modalEditSolicitud [name=cliente]').html(`<option value="${json.solicitud.cliente_id}" selected>${json.cliente.nombres} ${json.cliente.apellidoPaterno} ${json.cliente.apellidoMaterno}</option>`)

					$('[name=vehiculo]').empty();
					$.each(json.vehiculos, function(k, v) {
						$('[name=vehiculo]').append(new Option(v.marca + " - " + v.modelo + " - " + v.ano, v.id));
					})
					$("[name=vehiculo]").removeAttr('disabled');
					$("[name=vehiculo]").val(json.solicitud.vehiculo_id);
					$('#modalEditSolicitud [name=solicitud]').val(json.solicitud.solicitud);

					$('#modalEditSolicitud').modal('show');
				}
			})
		})

		$("[name=cliente]").change(function() {
			cliente_id = $(this).val();
			$.ajax({
				type: 'POST',
				url: '<?php echo admin_url('admin-ajax.php'); ?>',
				dataType: 'json',
				data: 'action=get_vehiculos_by_cliente&cliente_id=' + cliente_id,
				beforeSend: function() {
					$("[name=vehiculo]").html('<option value="">Cargando Vehiculos...</option>');
				},
				success: function(json) {
					$('[name=vehiculo]').empty();
					$.each(json.vehiculos, function(k, v) {
						$('[name=vehiculo]').append(new Option(v.marca + " - " + v.modelo + " - " + v.ano, v.id));
					})
					$("[name=vehiculo]").removeAttr('disabled');
				}
			})
		})

		$(".btnUncomplete").click(function() {
			tr = $(this).closest('tr');
			regid = tr.data('regid');

			$.confirm({
				title: 'Eliminar Solicitud!',
				content: '¿Desea eliminar la Solicitud seleccionada?',
				type: 'red',
				icon: 'fa fa-warning',
				buttons: {
					NO: {
						text: 'No',
						btnClass: 'btn-red',
					},
					SI: {
						text: 'Si',
						btnClass: 'btn-green',
						action: function() {
							$.ajax({
								type: 'POST',
								url: '<?php echo admin_url('admin-ajax.php'); ?>',
								dataType: 'json',
								data: 'action=uncompletar_solicitud&regid=' + regid,
								beforeSend: function() {},
								success: function(json) {
									$.alert({
										title: false,
										type: 'green',
										content: 'Solicitud borrado correctamente'
									});
									tr.fadeOut(400);
								}
							})
						}
					}
				}
			});
		});

		$(".btnProceed").click(function() {
			tr = $(this).closest('tr');
			regid = tr.data('regid');

			$.confirm({
				title: 'Proceed Solicitud!',
				content: '¿Desea convertir esta Orden de Ingreso en una Cotización?',
				type: 'red',
				icon: 'fa fa-warning',
				buttons: {
					NO: {
						text: 'No',
						btnClass: 'btn-red',
					},
					SI: {
						text: 'Si',
						btnClass: 'btn-green',
						action: function() {
							$.ajax({
								type: 'POST',
								url: '<?php echo admin_url('admin-ajax.php'); ?>',
								dataType: 'json',
								data: 'action=proceed_solicitud&regid=' + regid,
								beforeSend: function() {},
								success: function(json) {
									$.alert({
										title: false,
										type: 'green',
										content: 'Solicitud borrado correctamente'
									});
									window.location.reload()
								}
							})
						}
					}
				}
			});
		});

		$('#tabla_solicituds').DataTable({
			order: [
				[1, 'asc']
			]
		});
	});
</script>

<?php include 'footer.php'; ?>