$(function(){

//	$("h2 input").change(function(){
//		var the_date = $(this).val();
//		var months = ['January','February','March','April','May','June','July','August','September','October','November','December'];
//		$("form").attr("action", "?date=" + the_date);
//		the_date = the_date.split('/');
//		$("#work_date").val(the_date[2] + the_date[0] + the_date[1]);
//		$("h2 .work_date").html(months[parseInt(the_date[0]-1)] + ' ' + parseInt(the_date[1]) + ', ' + the_date[2]);
//	});
	// used for checkboxes
	var counter = 1;
	
	function set_buttons() {
		$("form .add_button").unbind("click").click(function(){
			var $new = $('.line:eq(0)').clone();
			$new.removeClass("highlight");
			$('select', $new).each(function(index){
			var numItems = $('.employee select').length;
				if (index == 0) {
					// need to default employee to previous employee
					$(this).get(0).selectedIndex = $('.employee select').eq(numItems-1).prop("selectedIndex");
				} else {
					$(this).get(0).selectedIndex = 0;				
				}
			});			

			$('input.text', $new).val("");
			
			$('input.checkbox', $new).attr('checked');
			$('input.checkbox', $new).val(counter);
			$('.add', $new).append('<span class="remove remove_button">X Remove</span>');
			$("#time_entry #lines").append( $new );
			set_buttons();
		});
		$("form .remove_button").unbind("click").click(function(){
			$(this).parent().parent().remove();
		});
		counter++;
	}
	
	set_buttons();
	
	$("#jumper select").change(function(){
		if ( $(this).val() != '' ) {
			window.location = $(this).val() + '?date=' + $("#work_date").val();
		}
	});
	$("#jumper .date_picker").change(function(){
		if ( $(this).val() != '' ) {
			window.location = '?date=' + $(this).val();
		}
	});
	
	$("#time_entry form").submit(function(){
		var errors = '';
		var entries = '';
		$(".line", this).removeClass("highlight").each(function(){
			var the_line = this;
			var ok = true;
			ok = ok && $(".employee select", this).val() != '';
			ok = ok && $(".task select", this).val() != '';
			ok = ok && $("input.text", this).val() != '';
			
			var pair = $(".employee select", this).val() + ',' + $(".task select", this).val();
			if ( ok && entries.indexOf(pair) != -1 ) {
				ok = false;
				errors = 'One or more entries are duplicate values for an employee/task combination.';
				return false;
			}
			else entries += "|" + pair;
			
			// check for existing
			if ( ok ) {
				var ajax_params = {
						action: 'entry_exists',
						project_id: $("input#project_id").val(),
						work_date: $("input#work_date").val(),
						user_id: $(".employee select", this).val(),
						task_id: $(".task select", this).val()
					};
				$.ajax({
					url: '/managers/ajax/',
					data: ajax_params,
					success: function(data) {
						var $response = eval('(' + data + ')');
						if ( $response.status ) {
							$(the_line).prepend('<div class="inline_error">' + $response.message + '</div>');
						}
					}
				});
			}
			
			if ( !ok ) {
				$(this).addClass("highlight");
				if ( errors == '' ) errors = 'One or more entries is incomplete. They have been highlighted. Please correct these entries or remove them before submitting.';
			}
		});
		if ( errors != '' ) {
			$('.errors', this).remove();
			$(this).prepend('<div class="errors" style="display: none;"><p>' + errors + '</p></div>');
			$('.errors', this).fadeIn("slow");
			return false;
		}
		else {
			return true;
		}
	});
	
});