<?
defined('BW') or die("Acesso negado!");

echo bwAdm::createHtmlSubMenu(1);
?>

<?= bwButton::redirect('Criar novo usuário', 'adm.php?com=usuarios&view=cadastro'); ?>

<table id="dataTable01">
	<thead>
		<tr>
			<th class="tac" style="width: 50px;">ID</th>
			<th>Nome</th>
			<th>Usuário/Login</th>
			<th style="width: 300px;">E-mail</th>
			<th style="width: 300px;">Grupo</th>
			<th class="tac" style="width: 25px;">Status</th>
		</tr>
	</thead>
	<tbody>
	</tbody>
</table>

<script type="text/javascript">
	$(document).ready(function() {

		oTable = $('#dataTable01').dataTable($.extend($.dataTableSettings, {
			
			// Fixbug
			aoColumnDefs: [{
				sClass: "tac", aTargets: [0, 4]
			}],
				sAjaxSource: "<?= bwRouter::_('adm.php?com=usuarios&task=usuariosLista&' .bwRequest::getToken(). '=1') ?>"
				
		}));
		
	});
</script>

