$(function(){

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
	
	$("td.options input.delete").click(function(){
		var $row = $(this).parent().parent();
		var $td_options = $(this).parent();
		var entry_id = $row.attr("id");
		entry_id = entry_id.split('_');
		entry_id = entry_id[1];
		var deleteme = confirm('Are you sure you wish to delete this entry?');
		if ( deleteme ) {
			return delete_row(entry_id);
		}
		return false;
	});
	
	$("td.options input.edit").click(function(){
		var $row = $(this).parent().parent();
		var $td_options = $(this).parent();
		var entry_id = $row.attr("id");
		
		entry_id = entry_id.split('_');
		entry_id = entry_id[1];
		
		var the_project_id = $("#project_id").val();
		
		// hide edit button.
		$(this).hide();
		
		// create and add behaviors to save/cancel
		$td_options.prepend('<input type="button" class="button save" value="Save" /><input type="button" class="button cancel" value="Cancel" />')
		$(".cancel", $td_options).click(function() {
			revert_row(entry_id);
		});
		$(".save", $td_options).click(function() {
			save_row(entry_id);
		});
		
		// hide the display data
		$("td .data", $row).hide();
		
		// employee drop-down
		$("td.employee", $row).prepend('<select><option>Choose Employee...</option></select>');
		$.ajax({
			url: '/managers/ajax/',
			data: { action: 'employees', project_id: the_project_id },
			success: function(data) {
				var response = eval('(' + data + ')');
				var select = $("td.employee select", $row).get(0);
				var selected = false;
				for ( var i = 0; i < response.length; i++ ) {
					if ( $("td.employee input.employee", $row).val() == response[i].ID ) {
						selected = true;
					}
					select.options[select.options.length] = new Option( response[i].name, response[i].ID, false, selected );
					selected = false;
				}
			}
		});
		
		// tasks drop-down
		$("td.task", $row).prepend('<select><option>Choose Task...</option></select>');
		$.ajax({
			url: '/managers/ajax/',
			data: { action: 'tasks', project_id: the_project_id },
			success: function(data) {
				var response = eval('(' + data + ')');
				var select = $("td.task select", $row).get(0);
				var selected = false;
				for ( var i = 0; i < response.length; i++ ) {
					if ( $("td.task input.task", $row).val() == response[i].ID ) {
						selected = true;
					}
					select.options[select.options.length] = new Option( response[i].name, response[i].ID, false, selected );
					selected = false;
				}
			}
		});
		
		// hours field
		$("td.hours", $row).prepend('<input type="text" size="2" value="" />');
		$("td.hours input[type='text']").val( $("td.hours .hours", $row).val() );

		// per diem checkbox
		per_diem_value = $("td.per_diem .data", $row).html();	
		if (per_diem_value == "Yes") {
			$("td.per_diem", $row).prepend('<input type="checkbox" value="1" checked="checked" />');
		} else {
			$("td.per_diem", $row).prepend('<input type="checkbox" value="1" />');		
		}
		//$("td.per_diem input[type='checkbox']").val( $("td.per_diem .per_diem", $row).val() );
	});
	
	// Cancel button action for a row.
	function revert_row( entry_id ) {
		var $row = $("tr#entry_" + entry_id);
		$("td select", $row).remove();
		$("td textarea", $row).remove();
		$("td.hours input[type='text']", $row).remove();
		$("td.per_diem input[type='checkbox']", $row).remove();
		$("td.options .save", $row).remove();
		$("td.options .cancel", $row).remove();
		$("td.options .edit", $row).show();
		$("td .data", $row).show();
	}
	
	// Save button action for a row.
	function save_row( entry_id ) {
		var $row = $("tr#entry_" + entry_id);

		var $employee = $("td.employee", $row);
		var old_employee = $("input.employee", $employee).val();
		var new_employee = $("select", $employee).val();
		
		var $task = $("td.task", $row);
		var old_task = $("input.task", $task).val();
		var new_task = $("select", $task).val();
		
		var $hours = $("td.hours", $row);
		var new_hours = $("input:eq(0)", $hours).val();
		new_hours = parseFloat(new_hours);
// 7/2/10 baw - if zero, convert to 0.0 to insert
		if (new_hours == 0) new_hours = 0.0;

		var $per_diem = $("td.per_diem", $row);
		var per_diem_check = $("input:checkbox", $per_diem).attr('checked');
		if (per_diem_check) {
			new_per_diem = 1;
			new_per_diem_text = "Yes";
		} else {
			new_per_diem = 0;
			new_per_diem_text = "No";
		}
			
		var errors = '';
		
		// verify non-empty
		if ( new_employee == '' ) {
			errors += 'You must choose an employee.\n';
		}
		if ( new_task == '' ) {
			errors += 'You must choose a task.\n';
		}

// 7/2/10 baw - allow zero hours
		if ( new_hours < 0 ) {
			errors += 'You must enter a valid amount of hours worked.\n';
		}
		// does it exist?
		if ( errors == '' && (new_employee != old_employee || new_task != old_task) ) {
			$.ajax({
				url: '/managers/ajax/',
				data: {
					action: 'entry_exists',
					work_date: $("#work_date").val(),
					project_id: $("#project_id").val(),
					user_id: new_employee,
					task_id: new_task
				},
				success: function(data){
					var result = eval('(' + data + ')');
					if ( result.status ) {
						result.message = result.message.split('. ');
						alert('Could Not Save\n' + result.message[0]);
						return false;
					}
				}
			});
		}
		if ( errors == '' ) {
			// save, update and revert row
			$.ajax({
				url: '/managers/ajax/',
				data: {
					action: 'update_entry',
					work_date: $("#work_date").val(),
					project_id: $("#project_id").val(),
					user_id: new_employee,
					task_id: new_task,
					hours: new_hours,
					per_diem: new_per_diem,
					prev_user_id: old_employee,
					prev_task_id: old_task
				},
				success: function(data) {
						//alert(data);

					var response = eval('(' + data + ')');
					if ( response.status ) {
						// get employee name
						$("input.employee", $employee).val( new_employee );
						var sel = $("select", $employee).get(0);
						$(".data", $employee).html( sel.options[sel.selectedIndex].text );
						$("select", $employee).remove();
						// get task name
						$("input.task", $task).val( new_task );
						sel = $("select", $task).get(0);
						$(".data", $task).html( sel.options[sel.selectedIndex].text );
						$("select", $task).remove();
						// hours
						$("input.hours", $hours).val( new_hours );
						$(".data", $hours).html( new_hours );
						$("input:eq(0)", $hours).remove();
						// per diem						
						$("input.per_diem", $per_diem).val( new_per_diem );
						$(".data", $per_diem).html( new_per_diem_text );
						$("input:checkbox", $per_diem).remove();
						
						var entry_id = $("input.employee", $employee).attr("id");
						entry_id = entry_id.split('_');
						
						revert_row( entry_id[1] );
						return true;
					}
					else {
						alert( response.message );
						return false;
					}
				}
			});
		}
		else {
			alert(errors);
		}
	}
	
	function delete_row( entry_id ) {
		$row = $("tr#entry_" + entry_id);
		$.ajax({
			url: '/managers/ajax/',
			data: {
				action: 'delete_entry',
				work_date: $("#work_date").val(),
				project_id: $("#project_id").val(),
				user_id: $("td.employee input.employee", $row).val(),
				task_id: $("td.task input.task", $row).val(),
				row: entry_id
			},
			success: function(data) {
				var response = eval('(' + data + ')');
				if ( response.status ) {
					$row.fadeOut().remove();
					return true;
				}
				else {
					alert('There was an error and this row could not be deleted.');
					return false;
				}
			}
		});
	}
	
	function revert_note() {
		var $note = $("#the_note");
		var new_note = $("textarea", $note).val();
		var $note_hidden = $("#note_value", $note);
		var $note_display = $("span.data", $note);
		$note_hidden.val( new_note );
		$note_display.html( new_note.replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1'+ '<br />' +'$2') ).show();
		$("textarea", $note).remove();
		$("input.edit", $note).show();
		$("input.save", $note).remove();
		$("input.cancel", $note).remove();
	}
	
	$("#the_note .edit").click(function(){
		$(this).hide();
		$("#the_note .buttons").append('<input type="button" class="save" value="Save" /><input type="button" class="cancel" value="Cancel" />');
		$("#the_note span.data").hide();
		$("#the_note .note_text").prepend('<textarea></textarea>');
		$("#the_note textarea").val( $("#the_note #note_value").val() );
		$("#the_note .cancel").click(function(){
			revert_note();
		});
		$("#the_note .save").click(function(){
			
			$.ajax({
				url: '/managers/ajax/',
				data: {
					action: 'set_note',
					work_date: $("#work_date").val(),
					project_id: $("#project_id").val(),
					note: $("#the_note textarea").val()
				},
				success: function(data) {
					var response = eval('(' + data + ')');
					if ( response.status ) {
						revert_note();
						return true;
					}
					else {
						alert('There was an error and your new note could not be saved.');
						return false;
					}
				}
			});
			
		});
	});
	
});