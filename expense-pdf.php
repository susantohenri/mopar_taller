<?php

include '../../../wp-load.php';
$solicitud = Mopar::getOneSolicitud($_GET['id']);
foreach (['total', 'iva_debito', 'iva_credito', 'gastos', 'utilidad'] as $currency) $solicitud->$currency = number_format($solicitud->$currency, 0);

$expenses = '';
foreach (json_decode($solicitud->expense) as $expense) {
    $expense->monto = number_format($expense->monto, 0);
    $expenses .= "
        <tr>
            <td style='width: 295px; border: 1px solid black'>
                <center>{$expense->proveedor}</center>
            </td>
            <td style='width: 295px; border: 1px solid black'>
                <center>$ {$expense->monto}</center>
            </td>
            <td style='width: 295px; border: 1px solid black'>
                <center>{$expense->tipo_de_documento}</center>
            </td>
            <td style='width: 295px; border: 1px solid black'>
                <center>{$expense->detalle}</center>
            </td>
        </tr>
    ";
}

$html = "
	<page backtop='7mm' backbottom='7mm' backleft='10mm' backright='10mm'>

		<table style='width: 590px;'>
			<tbody>
                <tr>
                    <td style='width: 590px;font-size: 32px;'>
                        <center><u>Conciliación Contable</u></center>
                    </td>
                </tr>
				<tr>
					<td style='width: 590px;text-align: center;font-size: 20px;'>
						<b>TRABAJO N° 000{$solicitud->id}</b>
					</td>
				</tr>
			</tbody>
            <tfoot>
                <tr>
                    <td>
                        &nbsp;
                    </td>
                </tr>
            </tfoot>
		</table>

		<table style='width: 590px; border-collapse: collapse'>
			<tbody>
                <tr>
                    <td style='width: 295px; border: 1px solid black'>
                        <b><center>VALOR TOTAL</center></b>
                    </td>
                    <td style='width: 295px; border: 1px solid black'>
                        <b><center>$ {$solicitud->total}</center></b>
                    </td>
                </tr>
                <tr>
                    <td style='width: 295px; border: 1px solid black'>
                        <b><center>IVA DEBITO</center></b>
                    </td>
                    <td style='width: 295px; border: 1px solid black'>
                        <b><center>$ {$solicitud->iva_debito}</center></b>
                    </td>
                </tr>
			</tbody>
            <tfoot>
                <tr>
                    <td colspan='2'>
                        &nbsp;
                    </td>
                </tr>
            </tfoot>
		</table>

		<table style='width: 590px; border-collapse: collapse'>
            <thead>
                <tr>
                    <th style='width: 295px; border: 1px solid black'>
                        <b><center>PROVEEDOR</center></b>
                    </th>
                    <th style='width: 295px; border: 1px solid black'>
                        <b><center>MONTO</center></b>
                    </th>
                    <th style='width: 295px; border: 1px solid black'>
                        <b><center>TIPO DE DOCUMENTO</center></b>
                    </th>
                    <th style='width: 295px; border: 1px solid black'>
                        <b><center>DETALLE</center></b>
                    </th>
                </tr>
            </thead>
			<tbody>
                {$expenses}
			</tbody>
            <tfoot>
                <tr>
                    <td colspan='4'>
                        &nbsp;
                    </td>
                </tr>
            </tfoot>
		</table>

		<table style='width: 590px; border-collapse: collapse'>
			<tbody>
                <tr>
                    <td style='width: 295px; border: 1px solid black'>
                        <b><center>TOTAL GASTOS</center></b>
                    </td>
                    <td style='width: 295px; border: 1px solid black'>
                        <b><center>$ {$solicitud->gastos}</center></b>
                    </td>
                </tr>
                <tr>
                    <td style='width: 295px; border: 1px solid black'>
                        <b><center>IVA CREDITO</center></b>
                    </td>
                    <td style='width: 295px; border: 1px solid black'>
                        <b><center>$ {$solicitud->iva_credito}</center></b>
                    </td>
                </tr>
			</tbody>
            <tfoot>
                <tr>
                    <td colspan='2'>
                        &nbsp;
                    </td>
                </tr>
            </tfoot>
		</table>

		<table style='width: 590px; border-collapse: collapse'>
			<tbody>
                <tr>
                    <td style='width: 295px; border: 1px solid black'>
                        <b><center>RESULTADO</center></b>
                    </td>
                    <td style='width: 295px; border: 1px solid black'>
                        <b><center>$ {$solicitud->utilidad}</center></b>
                    </td>
                </tr>
			</tbody>
		</table>

    </page>
";

require_once('html2pdf/html2pdf.class.php');
$html2pdf = new HTML2PDF($orientation, 'LETTER', 'es');
$html2pdf->WriteHTML($html);
$html2pdf->Output('Conciliación_000' . $solicitud->id . '.pdf');
