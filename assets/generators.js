$(document).ready(function(){

	// DB Table Selectors -
	// Show the table to form form...
	$('.db_table_select').change(function(){
		var _table = $(this).val();

		$('#db_table_form_details').load(
			'/admin/developer/builder/render_db_to_form/' + _table
		);
	});
	
	//--------------------------------------------------------------------
	
	// DB Table Rules Button...
	$('.rule-button').live('click', function(e){
		e.preventDefault();
		
		$(this).parent().parent().next('.rule-list').toggle();
	});

});