<?php
$inserted = false;
$updated = false;

if ($_POST) {
	global $wpdb;

	$array_insert = [
		'cliente_id' => $_POST['cliente'],
		'vehiculo_id' => $_POST['vehiculo'],
		'estado' => 1,
		'solicitud' => $_POST['solicitud']
	];

	if ($_POST['action'] == 'insertar_solicitud') {
		if ($wpdb->insert('solicitud', $array_insert)) {
			$inserted = true;
		}
	}

	if ($_POST['action'] == 'editar_solicitud') {
		$before_update = (array) Mopar::getOneSolicitud($_POST['solicitud_id']);
		$posted_attr = array_keys($array_insert);
		$before_update = array_filter($before_update, function ($value, $attr) use ($posted_attr) {
			return in_array($attr, $posted_attr);
		}, ARRAY_FILTER_USE_BOTH);
		if ($before_update !== $array_insert) $array_insert['upddate'] = date('Y-m-d H:i:s');

		if ($wpdb->update('solicitud', $array_insert, ['id' => $_POST['solicitud_id']])) {
			$updated = true;
		}
	}
}
?>

<?php include 'header.php'; ?>

<div class="box pr-4">
	<div class="box-header mb-4">
		<h2 class="font-weight-light text-center text-muted float-left"> Solicitudes de Servicio </h2>
		<button type="button" class="btn btn-primary float-right" data-toggle="modal" data-target="#modalNewSolicitud">Nueva Solicitud</button>

		<div class="clearfix"></div>
	</div>
	<div class="box-body">
		<table class="table table-striped table-bordered" id="tabla_solicituds">
			<thead>
				<tr>
					<th>#</th>
					<th> Cliente </th>
					<th> Vehiculo </th>
					<th> Estado </th>
					<th class="text-center">Acciones</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($solicituds as $solicitud) : ?>
					<tr data-regid="<?php echo $solicitud->id; ?>">
						<td data-regid="<?php echo $solicitud->id; ?>"> <?php echo $solicitud->id; ?> </td>
						<td data-cliente="<?php echo $solicitud->cliente_id; ?>"> <?php echo Mopar::getNombreCliente($solicitud->cliente_id, false) ?> </td>
						<td data-vehiculo="<?php echo $solicitud->vehiculo_id; ?>"> <?php if (0 != $solicitud->vehiculo_id) echo Mopar::getNombreVehiculo($solicitud->vehiculo_id) ?> </td>
						<td data-estado="<?php echo $solicitud->estado; ?>" class="text-center align-middle">
							<?php if (1 == $solicitud->estado) : ?>
								<a class="btnComplete">
									<i class="fa fa-circle text-danger"></i>
								</a>
							<?php elseif (in_array($solicitud->estado, [2, 4])) : ?>
								<a target="_blank" href="<?php bloginfo('wpurl') ?>/wp-content/plugins/mopar_taller/solicitud-pdf.php?id=<?php echo $solicitud->id; ?>">
									<i class="fa fa-circle text-success"></i>
								</a>
							<?php endif; ?>
						</td>
						<td class="text-center" style="white-space: nowrap;">
							<button type="button" class="btn btn-success btnEdit" data-regid="<?php echo $solicitud->id; ?>" data-toggle="tooltip" title="Editar"><i class="fa fa-pencil"></i></button>
							<a href="<?php bloginfo('wpurl') ?>/wp-content/plugins/mopar_taller/solicitud-pdf.php?id=<?php echo $solicitud->id; ?>" target="_blank" class="btn btn-info" data-toggle="tooltip" title="Ver"><i class="fa fa-search"></i></a>
							<button class="btn btn-danger btnDelete" data-toggle="tooltip" title="Eliminar"><i class="fa fa-trash-o"></i></button>
							<button class="btn btn-warning btnProceed" data-toggle="tooltip" title="Ingresar a Taller"><i class="fa fa-car"></i></button>
							<button class="btn btn-warning btnProceed" data-toggle="tooltip" title="Iniciar Cotización"><i class="fa fa-list"></i></button>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</div>




<!-- Nuevo Solicitud -->
<div class="modal fade" id="modalNewSolicitud" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
	<form method="post" id="formNuevoSolicitud" enctype="multipart/form-data">
		<input type="hidden" name="action" value="insertar_solicitud">
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
					<button type="submit" class="btn btn-success btnGuardar">Guardar <i class="fa fa-save"></i> </button>
				</div>
			</div>
		</div>
	</form>
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
					<button type="submit" class="btn btn-success btnGuardar">Guardar <i class="fa fa-save"></i> </button>
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

		if (location.hash == "#new") {
			$('#modalNewOT').modal('show');
		}

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

		$(".btnDelete").click(function() {
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
								data: 'action=eliminar_solicitud&regid=' + regid,
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

		$(".btnComplete").click(function() {
			tr = $(this).closest('tr');
			regid = tr.data('regid');

			$.confirm({
				title: 'Completar Solicitud!',
				content: '¿Quiere completar el trabajo para esta cotizacion?',
				type: 'green',
				icon: 'fa fa-success',
				buttons: {
					NO: {
						text: 'Cancelar',
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
								data: 'action=completar_solicitud&regid=' + regid,
								beforeSend: function() {},
								success: function(json) {
									if (`ERROR` === json.status) {
										$.alert({
											title: false,
											type: 'red',
											content: json.message
										});
									} else {
										$.alert({
											title: false,
											type: 'green',
											content: 'Cotizacion borrada correctamente'
										});
										window.location.reload()
									}
								}
							})
						}
					}
				}
			});
		});



		$("#formNuevoSolicitud").submit(function(e) {
			$(".overlay").show();
			e.preventDefault();
			$("#formNuevoSolicitud")[0].submit();
		});


		$("#formEditSolicitud").submit(function(e) {
			$(".overlay").show();
			e.preventDefault();
			$("#formEditSolicitud")[0].submit();
		});


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
			location.href = '<?php bloginfo('wpurl') ?>/wp-admin/admin.php?page=mopar-solicitudes-de-servicio';
		<?php } ?>


		$('#tabla_solicituds').DataTable({
			"ordering": false
		});
	});
</script>

<?php include 'footer.php'; ?>