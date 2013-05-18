$(function(){

	$("td .row-actions span").hide();
	
	$("table.listing tr").hover(
		function(){
			$(this).css("background","#fff");
			$(this).children("td").children(".row-actions").children("span").show();
		}, 
		function(){
			$(this).css("background","transparent");
			$(this).children("td").children(".row-actions").children("span").hide();
		}
	);
	
	$("a.confirm_action").click(function(){
		return confirm( $(this).attr("title") );
	});
	
	$(".date_picker").datepicker({
		dateFormat: 'mm/dd/yy',
		buttonImage: '/images/icon_calendar.png',
		buttonText: 'Select a Date',
		buttonImageOnly: true,
		showOn: 'both'
	});
	$(".time_date_picker").datepicker({
		dateFormat: 'mm/dd/yy',
		buttonImage: '/images/icon_calendar.png',
		buttonText: 'Select a Date',
		buttonImageOnly: true,
		showOn: 'both'
	});
	
	function verify_mgr_start() {
		var err = '';
		if ( $("#time_entry form.start_form select").val() == '' ) {
			err += 'Select a project.';
		}
		if ( err != '' ) {
			alert(err);
			return false;
		}
		return true;
	}
	
	$("#time_entry form.start_form .requires_project").hide();
	$("#time_entry form.start_form #project_id").change(function(){
		if ( $(this).val() != '' ) $("#time_entry form.start_form .requires_project").show();
	});
	$("#time_entry form.start_form").submit(function(){
		return verify_mgr_start();
	});
	$("#time_entry form.start_form .view_time").click(function(){
		if ( verify_mgr_start() ) {
			$("#time_entry form.start_form input[type='submit']").val("Review Time");
			$("#time_entry form.start_form").submit();
		}
		return false;
	});
	
});