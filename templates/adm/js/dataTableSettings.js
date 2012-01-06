$(function(){	
	// Configuração padrão do dataTable
	$.dataTableSettings = {
		
		"bStateSave": true,
		
		// Fixbug
		"aoColumnDefs": [{
			"aTargets": [1] 
		}],

		// Template
		"bJQueryUI": true,
		"sPaginationType": "full_numbers",

		// Tradução
		"oLanguage": {
			"sProcessing": "Buscando registros...",
			"sLengthMenu": "Exibir _MENU_ registros por página",
			"sZeroRecords": "Nenhum registro foi encontrado!",
			"sEmptyTable": "Não há dados disponíveis na tabela",
			"sInfo": "Exibindo _START_ de _END_ em _TOTAL_ registros",
			"sInfoEmpty": "Exibindo 0 de 0 em 0 registros",
			"sInfoFiltered": "(filtrado dos _MAX_ registros)",
			"sInfoPostFix": "",
			"sSearch": "Buscar:",
			"sUrl": "",
			"oPaginate": {
				"sFirst":    "Primeira",
				"sPrevious": "Anterior",
				"sNext":     "Próxima",
				"sLast":     "Última"
			}
		},
		
		// Ordem
		"aaSorting": [[ 1, 'asc' ]],
		
		// Server Side
		"bProcessing": true,
		"bServerSide": true,
		
		"sAjaxSource": false
	};

	$.dataTableSettingsNoAjax = {
		
		"bStateSave": true,
		
		// Fixbug
		"aoColumnDefs": [{
			"aTargets": [1] 
		}],

		// Template
		"bJQueryUI": true,
		"sPaginationType": "full_numbers",

		// Tradução
		"oLanguage": {
			"sProcessing": "Buscando registros...",
			"sLengthMenu": "Exibir _MENU_ registros por página",
			"sZeroRecords": "Nenhum registro foi encontrado!",
			"sEmptyTable": "Não há dados disponíveis na tabela",
			"sInfo": "Exibindo _START_ de _END_ em _TOTAL_ registros",
			"sInfoEmpty": "Exibindo 0 de 0 em 0 registros",
			"sInfoFiltered": "(filtrado dos _MAX_ registros)",
			"sInfoPostFix": "",
			"sSearch": "Buscar:",
			"sUrl": "",
			"oPaginate": {
				"sFirst":    "Primeira",
				"sPrevious": "Anterior",
				"sNext":     "Próxima",
				"sLast":     "Última"
			}
		},
		
		// Ordem
		"aaSorting": [[ 0, 'asc' ]]
	};	    
    
});
