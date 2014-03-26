$(document).ready(function() {
	$(".dashboard_draft").click(function(){
		$("#draft_messages").jqGrid('GridUnload');
		$("#draft_messages").jqGrid({
			url:"ajaxdashboard/draft-messages",
			datatype: "json",
			mtype: "POST",
			colNames:['ID','PID','Date of Service','Last Name','First Name','Subject'],
			colModel:[
				{name:'t_messages_id',index:'t_messages_id',width:1,hidden:true},
				{name:'pid',index:'pid',width:1,hidden:true},
				{name:'t_messages_dos',index:'t_messages_date',width:100,formatter:'date',formatoptions:{srcformat:"ISO8601Long", newformat: "ISO8601Short"}},
				{name:'lastname',index:'lastname',width:100},
				{name:'firstname',index:'firstname',width:100},
				{name:'t_messages_subject',index:'t_messages_subject',width:300}
			],
			rowNum:10,
			rowList:[10,20,30],
			pager: $('#draft_messages_pager'),
			sortname: 't_messages_dos',
		 	viewrecords: true,
		 	sortorder: "desc",
		 	caption:"Telephone Message Drafts",
		 	emptyrecords:"No messages.",
		 	height: "100%",
		 	onSelectRow: function(id) {
		 		var pid = $("#draft_messages").getCell(id,'pid');
		 		var t_messages_id = $("#draft_messages").getCell(id,'t_messages_id');
		 		$.ajax({
					type: "POST",
					url: "ajaxsearch/openchart",
					data: "pid=" + pid,
					success: function(data){
						$.ajax({
							type: "POST",
							url: "ajaxsearch/tmessagesidset",
							data: "t_messages_id=" + t_messages_id,
							dataType: "json",
							success: function(data){
								window.location = data.url;
							}
						});
					}
				});
		 	},
		 	jsonReader: { repeatitems : false, id: "0" }
		}).navGrid('#draft_messages_pager',{search:false,edit:false,add:false,del:false});
		$("#draft_encounters").jqGrid('GridUnload');
		$("#draft_encounters").jqGrid({
			url:"ajaxdashboard/draft-encounters",
			datatype: "json",
			mtype: "POST",
			colNames:['ID','PID','Date of Service','Last Name','First Name','Chief Complaint'],
			colModel:[
				{name:'eid',index:'eid',width:1,hidden:true},
				{name:'pid',index:'pid',width:1,hidden:true},
				{name:'encounter_DOS',index:'encounter_DOS',width:100,formatter:'date',formatoptions:{srcformat:"ISO8601Long", newformat: "ISO8601Short"}},
				{name:'lastname',index:'lastname',width:100},
				{name:'firstname',index:'firstname',width:100},
				{name:'encounter_cc',index:'encounter_cc',width:300}
			],
			rowNum:10,
			rowList:[10,20,30],
			pager: $('#draft_encounters_pager'),
			sortname: 'encounter_DOS',
		 	viewrecords: true,
		 	sortorder: "desc",
		 	caption:"Encounter Drafts - Click to open encounter",
		 	emptyrecords:"No encounters.",
		 	height: "100%",
		 	onSelectRow: function(id) {
		 		var pid = $("#draft_encounters").getCell(id,'pid');
		 		var eid = $("#draft_encounters").getCell(id,'eid');
		 		$.ajax({
					type: "POST",
					url: "ajaxsearch/openchart",
					data: "pid=" + pid,
					dataType: "json",
					success: function(data){
						$.ajax({
							type: "POST",
							url: "ajaxsearch/eidset",
							data: "eid=" + eid,
							dataType: "json",
							success: function(data) {
								window.location = data.url;
							}
						});
					}
				});
		 	},
		 	jsonReader: { repeatitems : false, id: "0" }
		}).navGrid('#draft_encounters_pager',{search:false,edit:false,add:false,del:false});
		$("#draft_div").show();
		$("#alert_div").hide();
		$("#mtm_alert_div").hide();
	});
	$(".dashboard_alerts").click(function(){
		$("#dashboard_alert").jqGrid('GridUnload');
		$("#dashboard_alert").jqGrid({
			url:"ajaxdashboard/alerts",
			datatype: "json",
			mtype: "POST",
			colNames:['ID','PID','Due Date','Last Name','First Name','Alert','Description'],
			colModel:[
				{name:'alert_id',index:'alert_id',width:1,hidden:true},
				{name:'pid',index:'pid',width:1,hidden:true},
				{name:'alert_date_active',index:'alert_date_active',width:100,formatter:'date',formatoptions:{srcformat:"ISO8601Long", newformat: "ISO8601Short"}},
				{name:'lastname',index:'lastname',width:100},
				{name:'firstname',index:'firstname',width:100},
				{name:'alert',index:'alert',width:100},
				{name:'alert_description',index:'alert',width:200}
			],
			rowNum:10,
			rowList:[10,20,30],
			pager: $('#dashboard_alert_pager'),
			sortname: 'alert_date_active',
		 	viewrecords: true,
		 	sortorder: "asc",
		 	caption:"Reminders - Click to open chart",
		 	emptyrecords:"No reminders.",
		 	height: "100%",
		 	onSelectRow: function(id) {
		 		var pid = $("#dashboard_alert").getCell(id,'pid');
		 		var alert_id = $("#dashboard_alert").getCell(id,'alert_id');
		 		$.ajax({
					type: "POST",
					url: "ajaxsearch/openchart",
					data: "pid=" + pid,
					success: function(data){
						$.ajax({
							type: "POST",
							url: "ajaxsearch/alertidset",
							data: "alert_id=" + alert_id,
							dataType: "json",
							success: function(data){
								window.location = data.url;
							}
						});
					}
				});
		 	},
		 	jsonReader: { repeatitems : false, id: "0" }
		}).navGrid('#dashboard_alert_pager',{search:false,edit:false,add:false,del:false});
		$("#alert_div").show();
		$("#draft_div").hide();
		$("#mtm_alert_div").hide();
	});
	$("#provider_mtm_alerts").click(function(){
		$("#dashboard_mtm_alert").jqGrid('GridUnload');
		$("#dashboard_mtm_alert").jqGrid({
			url:'ajaxdashboard/mtm-alerts',
			datatype: "json",
			mtype: "POST",
			colNames:['ID','PID','Last Name','First Name'],
			colModel:[
				{name:'alert_id',index:'alert_id',width:1,hidden:true},
				{name:'pid',index:'pid',width:1,hidden:true},
				{name:'lastname',index:'lastname',width:250},
				{name:'firstname',index:'firstname',width:250}
			],
			rowNum:10,
			rowList:[10,20,30],
			pager: $('#dashboard_mtm_alert_pager'),
			sortname: 'lastname',
		 	viewrecords: true,
		 	sortorder: "asc",
		 	caption:"Medication Therapy Managment Patient Roster - Click to open chart",
		 	emptyrecords:"No patients.",
		 	height: "100%",
		 	onSelectRow: function(id) {
		 		var pid = $("#dashboard_mtm_alert").getCell(id,'pid');
		 		var alert_id = $("#dashboard_mtm_alert").getCell(id,'alert_id');
		 		$.ajax({
					type: "POST",
					url: "ajaxsearch/openchart",
					data: "pid=" + pid,
					success: function(data){
						$.ajax({
							type: "POST",
							url: "ajaxsearch/alertidset",
							data: "alert_id=" + alert_id,
							success: function(data){
								window.location = data.url;
							}
						});
					}
				});
		 	},
		 	jsonReader: { repeatitems : false, id: "0" }
		}).navGrid('#dashboard_mtm_alert_pager',{search:false,edit:false,add:false,del:false});
		$("#mtm_alert_div").show();
		$("#alert_div").hide();
		$("#draft_div").hide();
	});
	$("#change_password_dialog").dialog({ 
		bgiframe: true, 
		autoOpen: false, 
		height: 300, 
		width: 800, 
		modal: true,
		overlay: {
			backgroundColor: '#000',
			opacity: 50
		},
		open: function () {
			$.ajax({
				type: "POST",
				url: "ajaxlogin/get-secret",
				dataType: "json",
				success: function(data){
					$("#secret_question").val(data.secret_question);
					$("#secret_answer").val(data.secret_answer);
				}
			});
		},
		buttons: {
			'OK': function() {
				var a = $("#old_password");
				var b = $("#new_password");
				var c = $("#new_password2");
				var d = $("#secret_question");
				var e = $("#secret_answer");
				var bValid = true;
				bValid = bValid && checkEmpty(a,"Old Password");
				bValid = bValid && checkEmpty(b,"New Password");
				bValid = bValid && checkEmpty(c,"Confirm New Password");
				bValid = bValid && checkEmpty(d,"Secret Question");
				bValid = bValid && checkEmpty(e,"Secret Answer");
				if (bValid) {
					var f = $("#new_password").val();
					var g = $("#new_password2").val();
					if (f != g) {
						$.jGrowl("New passwords do not match!");
						$("#change_password_form").clearForm();
					} else {
						var str = $("#change_password_form").serialize();
						if(str){
							$.ajax({
								type: "POST",
								url: "ajaxlogin/change-password1",
								data: str,
								success: function(data){
									if (data == "Your old password is incorrect!") {
										$.jGrowl(data);
										$("#change_password_form").clearForm();
									} else {
										$.jGrowl(data);
										$("#change_password_form").clearForm();
										$("#change_password_dialog").dialog('close');
									}
								}
							});
						} else {
							$.jGrowl("Please complete the form");
						}
					}
				}
			},
			Cancel: function() {
				$("#change_password_form").clearForm();
				$("#change_password_dialog").dialog('close');
			}
		}
	});
	$("#change_password").click(function(){
		$("#change_password_dialog").dialog('open');
	});
	var secret_question = {"What was your childhood nickname?":"What was your childhood nickname?","In what city did you meet your spouse/significant other?":"In what city did you meet your spouse/significant other?","What is the name of your favorite childhood friend?":"What is the name of your favorite childhood friend?","What street did you live on in third grade?":"What street did you live on in third grade?","What is your oldest sibling’s birthday month and year? (e.g., January 1900)":"What is your oldest sibling’s birthday month and year? (e.g., January 1900)","What is the middle name of your oldest child?":"What is the middle name of your oldest child?","What is your oldest sibling's middle name?":"What is your oldest sibling's middle name?","What school did you attend for sixth grade?":"What is your oldest sibling's middle name?","What was your childhood phone number including area code? (e.g., 000-000-0000)":"What was your childhood phone number including area code? (e.g., 000-000-0000)","What is your oldest cousin's first and last name?":"What is your oldest cousin's first and last name?","What was the name of your first stuffed animal?":"What was the name of your first stuffed animal?","In what city or town did your mother and father meet?":"In what city or town did your mother and father meet?","Where were you when you had your first kiss?":"Where were you when you had your first kiss?","What is the first name of the boy or girl that you first kissed?":"What is the first name of the boy or girl that you first kissed?","What was the last name of your third grade teacher?":"What was the last name of your third grade teacher?","In what city does your nearest sibling live?":"In what city does your nearest sibling live?","What is your oldest brother’s birthday month and year? (e.g., January 1900)":"What is your oldest brother’s birthday month and year? (e.g., January 1900)","What is your maternal grandmother's maiden name?":"What is your maternal grandmother's maiden name?","In what city or town was your first job?":"In what city or town was your first job?","What is the name of the place your wedding reception was held?":"What is the name of the place your wedding reception was held?","What is the name of a college you applied to but didn't attend?":"What is the name of a college you applied to but didn't attend?"};
	$("#secret_question").addOption(secret_question, false);
	$("#secret_question1").addOption(secret_question, false);
	$.ajax({
		type: "POST",
		url: "ajaxlogin/check-secret",
		success: function(data){
			if (data == "Need secret question and answer!") {
				$.jGrowl(data);
				$("#change_secret_answer_dialog").dialog('open');
			}
		}
	});
	$("#change_secret_answer_dialog").dialog({ 
		bgiframe: true, 
		autoOpen: false, 
		height: 300, 
		width: 800, 
		modal: true,
		overlay: {
			backgroundColor: '#000',
			opacity: 50
		},
		buttons: {
			'OK': function() {
				var a = $("#secret_question1");
				var b = $("#secret_answer1");
				var bValid = true;
				bValid = bValid && checkEmpty(a,"Secret Question");
				bValid = bValid && checkEmpty(b,"Secret Answer");
				if (bValid) {
					var str = $("#change_secret_answer_form").serialize();
					if(str){
						$.ajax({
							type: "POST",
							url: "ajaxlogin/set-secret",
							data: str,
							success: function(data){
								$.jGrowl(data);
								$("#change_secret_answer_form").clearForm();
								$("#change_secret_answer_dialog").dialog('close');
							}
						});
					} else {
						$.jGrowl("Please complete the form");
					}
				}
			},
			Cancel: function() {
				$("#change_secret_answer_form").clearForm();
				$("#change_secret_answer_dialog").dialog('close');
			}
		}
	});
	$('.sigPad').signaturePad({drawOnly:true});
	$("#provider_info_dialog").dialog({ 
		bgiframe: true, 
		autoOpen: false, 
		height: 580, 
		width: 800, 
		modal: true,
		overlay: {
			backgroundColor: '#000',
			opacity: 50
		},
		closeOnEscape: false,
		dialogClass: "noclose",
		open: function() {
			$("#provider_info_specialty").autocomplete({
				source: function (req, add){
					$.ajax({
						url: "ajaxsearch/specialty",
						dataType: "json",
						type: "POST",
						data: req,
						success: function(data){
							if(data.response =='true'){
								add(data.message);
							}
						}
					});
				},
				minLength: 3
			});
			$("#provider_info_accordion").accordion({ heightStyle: "content" });
			$.ajax({
				type: "POST",
				url: "ajaxdashboard/provider-info",
				dataType: "json",
				success: function(data){
					$.each(data, function(key, value){
						$("#provider_info_form :input[name='" + key + "']").val(value);
					});
				}
			});
			$.ajax({
				type: "POST",
				url: "ajaxdashboard/check-rcopia",
				success: function(data){
					if (data == 'y') {
						$('#rcopia_username_div').show();
					} else {
						$('#rcopia_username_div').hide();
					}
				}
			});
			$.ajax({
				type: "POST",
				url: "ajaxdashboard/preview-signature",
				success: function(data){
					$("#preview_signature").html(data);
				}
			});
		},
		buttons: {
			'Save': function() {
				var str = $("#provider_info_form").serialize();
				$.ajax({
					type: "POST",
					url: "ajaxdashboard/provider-info1",
					data: str,
					success: function(data){
						$.jGrowl(data);
							$("#provider_info_form").clearForm();
							$("#provider_info_dialog").dialog('close');
					}
				});
			},
			Cancel: function() {
				$("#provider_info_form").clearForm();
				$("#provider_info_dialog").dialog('close');
			}
		}
	});
	$("#provider_info").click(function(){
		$("#provider_info_dialog").dialog('open');
	});
	$("#provider_info_license_state").addOption({"":"","AL":"Alabama","AK":"Alaska","AS":"America Samoa","AZ":"Arizona","AR":"Arkansas","CA":"California","CO":"Colorado","CT":"Connecticut","DE":"Delaware","DC":"District of Columbia","FM":"Federated States of Micronesia","FL":"Florida","GA":"Georgia","GU":"Guam","HI":"Hawaii","ID":"Idaho","IL":"Illinois","IN":"Indiana","IA":"Iowa","KS":"Kansas","KY":"Kentucky","LA":"Louisiana","ME":"Maine","MH":"Marshall Islands","MD":"Maryland","MA":"Massachusetts","MI":"Michigan","MN":"Minnesota","MS":"Mississippi","MO":"Missouri","MT":"Montana","NE":"Nebraska","NV":"Nevada","NH":"New Hampshire","NJ":"New Jersey","NM":"New Mexico","NY":"New York","NC":"North Carolina","ND":"North Dakota","OH":"Ohio","OK":"Oklahoma","OR":"Oregon","PW":"Palau","PA":"Pennsylvania","PR":"Puerto Rico","RI":"Rhode Island","SC":"South Carolina","SD":"South Dakota","TN":"Tennessee","TX":"Texas","UT":"Utah","VT":"Vermont","VI":"Virgin Island","VA":"Virginia","WA":"Washington","WV":"West Virginia","WI":"Wisconsin","WY":"Wyoming"}, false);
	$("#provider_info_upin").mask("aa9999999");
	$("#provider_info_tax_id").mask("99-9999999");
	$("#change_signature").button().click(function(){
		var str = $("#signature_form").serialize();
		$.ajax({
			type: "POST",
			url: "ajaxdashboard/change-signature",
			data: str,
			success: function(data){
				$.jGrowl(data);
				$.ajax({
					type: "POST",
					url: "ajaxdashboard/preview-signature",
					success: function(data){
						$("#preview_signature").html(data);
					}
				});
			}
		});
	});
	
	$("#restore_database_dialog").dialog({ 
		bgiframe: true, 
		autoOpen: false, 
		height: 320, 
		width: 500, 
		modal: true,
		overlay: {
			backgroundColor: '#000',
			opacity: 50
		}
	});
	$("#restore_database_link").click(function(){
		$.ajax({
			type: "POST",
			url: "ajaxdashboard/findbackups",
			dataType: 'json',
			success: function(data){
				$("#backup_select").addOption(data.options);
			}
		});
		$("#restore_database_dialog").dialog('open');
	});
	$("#restore_backup_button").button().click(function(){
		var a = $("#backup_select").val();
		$.ajax({
			type: "POST",
			url: "ajaxdashboard/backuprestore",
			data: "file=" + a,
			success: function(data){
				$.jGrowl(data);
				$("#restore_database_dialog").dialog('close');
			}
		});
	});
	$(".dashboard_test_reconcile").click(function(){
		$("#tests_reconcile_dialog").dialog('open');
	});
	$("#tests_reconcile_dialog").dialog({ 
		bgiframe: true, 
		autoOpen: false, 
		height: 500, 
		width: 800,
		open: function(event, ui) {
			$("#reconcile_test_patient_search1").autocomplete({
				source: function (req, add){
					$.ajax({
						url: "ajaxsearch/search",
						dataType: "json",
						type: "POST",
						data: req,
						success: function(data){
							if(data.response =='true'){
								add(data.message);
							}
						}
					});
				},
				minLength: 1,
				select: function(event, ui){
					$("#reconcile_tests_pid").val(ui.item.id);
				}
			});
			$("#tests_reconcile_list").jqGrid('GridUnload');
			$("#tests_reconcile_list").jqGrid({
				url:"ajaxdashboard/tests",
				datatype: "json",
				mtype: "POST",
				colNames:['ID','Date','Patient','Test','Result','Unit','Normal','Flags','Type'],
				colModel:[
					{name:'tests_id',index:'tests_id',width:1,hidden:true},
					{name:'test_datetime',index:'test_datetime',width:75,formatter:'date',formatoptions:{srcformat:"ISO8601Long", newformat: "ISO8601Short"}},
					{name:'test_unassigned',index:'test_unassigned',width:110},
					{name:'test_name',index:'test_name',width:200},
					{name:'test_result',index:'test_result',width:120},
					{name:'test_units',index:'test_units',width:50},
					{name:'test_reference',index:'test_reference',width:100},
					{name:'test_flags',index:'test_flags',width:50,
						cellattr: function (rowId, val, rawObject, cm, rdata) {
							if (rawObject.test_flags == "L") {
								var response = "Below low normal";
							}
							if (rawObject.test_flags == "H") {
								var response = "Above high normal";
							}
							if (rawObject.test_flags == "LL") {
								var response = "Below low panic limits";
							}
							if (rawObject.test_flags == "HH") {
								var response = "Above high panic limits";
							}
							if (rawObject.test_flags == "<") {
								var response = "Below absolute low-off instrument scale";
							}
							if (rawObject.test_flags == ">") {
								var response = "Above absolute high-off instrument scale";
							}
							if (rawObject.test_flags == "N") {
								var response = "Normal";
							}
							if (rawObject.test_flags == "A") {
								var response = "Abnormal";
							}
							if (rawObject.test_flags == "AA") {
								var response = "Very abnormal";
							}
							if (rawObject.test_flags == "U") {
								var response = "Significant change up";
							}
							if (rawObject.test_flags == "D") {
								var response = "Significant change down";
							}
							if (rawObject.test_flags == "B") {
								var response = "Better";
							}
							if (rawObject.test_flags == "W") {
								var response = "Worse";
							}
							if (rawObject.test_flags == "S") {
								var response = "Susceptible";
							}
							if (rawObject.test_flags == "R") {
								var response = "Resistant";
							}
							if (rawObject.test_flags == "I") {
								var response = "Intermediate";
							}
							if (rawObject.test_flags == "MS") {
								var response = "Moderately susceptible";
							}
							if (rawObject.test_flags == "VS") {
								var response = "Very susceptible";
							}
							if (rawObject.test_flags == "") {
								var response = "";
							}
							return 'title="' + response + '"';
						}
					},
					{name:'test_type',index:'test_type',width:1,hidden:true}
				],
				rowNum:10,
				rowList:[10,20,30],
				pager: $('#tests_reconcile_list_pager'),
				sortname: 'test_datetime',
			 	viewrecords: true,
			 	sortorder: "desc",
			 	caption:"Test Results",
			 	height: "100%",
			 	gridview: true,
			 	multiselect: true,
				multiboxonly: true,
			 	rowattr: function (rd) {
					if (rd.test_flags == "HH" || rd.test_flags == "LL" || rd.test_flags == "H" || rd.test_flags == "L") {
						return {"class": "myAltRowClass"};
					}
				},
			 	jsonReader: { repeatitems : false, id: "0" }
			}).navGrid('#tests_reconcile_list_pager',{search:false,edit:false,add:false,del:false});
		}
	});
	$("#reconcile_tests").button({icons: {primary: "ui-icon-disk"}}).click(function(){
		var click_id = $("#tests_reconcile_list").getGridParam('selarrrow');
		if(click_id.length > 0){
			$("#reconcile_tests_pid").val('');
			$("#scan_patient_search1").val('');
			$("#reconcile_tests_div").show();
			$("#reconcile_test_patient_search1").focus();
		} else {
			$.jGrowl("Choose test to reconcile!");
		}
	});
	$("#reconcile_tests_send").click(function(){
		var click_id = $("#tests_reconcile_list").getGridParam('selarrrow');
		var pid = $("#reconcile_tests_pid").val();
		if(click_id){
			var json_flat = JSON.stringify(click_id);
			$.ajax({
				type: "POST",
				url: "ajaxdashboard/tests-import",
				data: "tests_id_array=" + json_flat + "&pid=" + pid,
				success: function(data){
					$.jGrowl('Imported ' + data + ' tests!');
					$("#reconcile_tests_pid").val('');
					$("#reconcile_test_patient_search1").val('');
					$("#reconcile_tests_div").hide();
					reload_grid("tests_reconcile_list");
				}
			});
		}
	});
	$("#reconcile_tests_cancel").click(function(){
		$("#reconcile_tests_pid").val('');
			$("#reconcile_test_patient_search1").val('');
			$("#reconcile_tests_div").hide();
	});
	$("#delete_tests").click(function(){
		var click_id = $("#tests_reconcile_list").getGridParam('selarrrow');
		if(click_id.length > 0){
			if(confirm('Are you sure you want to delete the selected tests?')){ 
				var count = click_id.length;
				for (var i = 0; i < count; i++) {
					$.ajax({
						type: "POST",
						url: "ajaxdashboard/delete-tests",
						data: "tests_id=" + click_id[i],
						success: function(data){
						}
					});
				}
				$.jGrowl('Deleted ' + i + ' tests!');
				reload_grid("tests_reconcile_list");
			}
		} else {
			$.jGrowl("Please select test to delete!");
		}
	});
	$("#print_entire_charts_progressbar").progressbar({
		value: false,
		change: function() {
			var value = $("#print_entire_charts_progressbar").progressbar("option", "value");
			$(".print_entire_charts_progressbar_label").text(value + "%" );
		},
		complete: function() {
			$(".print_entire_charts_progressbar_label").text( "Complete!" );
		}
	});
	$("#print_entire_charts").click(function(){
		$.ajax({
			type: "POST",
			url: "ajaxdashboard/check-print-entire-chart",
			dataType: "json",
			success: function(data){
				if (data.response == true) {
					$("#print_entire_charts_progress_div").show();
					$.ajax({
						type: "POST",
						url: "ajaxdashboard/print_entire_chart",
						dataType: 'json',
						success: function(data1){
							if (data1.response == true) {
								$("#print_download").html(data1.html);
							}
						}
					});
					setTimeout(print_chart_progress, 1000);
				} else {
					$.jGrowl(data.message);
				}
			}
		});
	}).tooltip({ content: "Clicking on this will create a ZIP file with individual PDF files of complete medical records for every patient in your practice." });
	function print_chart_progress() {
		var val = $("#print_entire_charts_progressbar").progressbar("option", "value" ) || 0;
		$.ajax({
			type: "POST",
			url: "ajaxdashboard/print-entire-chart-progress",
			success: function(data){
				$("#print_entire_charts_progressbar").progressbar("option","value", parseInt(data));
				if (data < 99) {
					setTimeout(print_chart_progress, 1000);
				}
			}
		});
	}
	$("#generate_csv_patient_demographics").click(function(){
		$.ajax({
			type: "POST",
			url: "ajaxdashboard/check-csv-patient-demographics",
			dataType: "json",
			success: function(data){
				if (data.response == true) {
					$("#print_entire_charts_progress_div").show();
					$.ajax({
						type: "POST",
						url: "ajaxdashboard/generate-csv-patient-demographics",
						dataType: 'json',
						success: function(data1){
							if (data1.message="OK") {
								$("#print_download").html(data1.html);
							}
						}
					});
					setTimeout(csv_progress, 1000);
				} else {
					$.jGrowl(data);
				}
			}
		});
	}).tooltip({ content: "Clicking on this will create a CSV file of demographic information for every patient in your practice." });
	function csv_progress() {
		var val = $("#print_entire_charts_progressbar").progressbar("option", "value" ) || 0;
		$.ajax({
			type: "POST",
			url: "ajaxdashboard/csv-progress",
			success: function(data){
				$("#print_entire_charts_progressbar").progressbar("option","value", parseInt(data));
				if (data < 99) {
					setTimeout(csv_progress, 1000);
				}
			}
		});
	}
});