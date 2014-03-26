<?php

class AjaxInstallController extends BaseController {

	/**
	* NOSH ChartingSystem Installation Ajax Functions
	*/
	
	public function postInstallProcess()
	{
		set_time_limit(0);
		ini_set('memory_limit','196M');
		
		// Configure database settings
		$db_name = 'nosh';
		$db_username = Input::get('db_username');
		$db_password = Input::get('db_password');
		$connect = mysqli_connect('localhost', $db_username, $db_password);
		if ($connect) {
			$database_filename = "/var/laravel/.env.php";
			$database_config['mysql_database'] = $db_name;
			$database_config['mysql_username'] = $db_username;
			$database_config['mysql_password'] = $db_password;
			file_put_contents($database_filename, '<?php return ' . var_export($database_config, true) . ";\n");
			if (!mysqli_query($connect, "CREATE DATABASE " . $db_name)) {
				echo 'Error creating database: ' . mysqli_error($connect);
				exit (0);
			}
			mysqli_close($connect);
		} else {
			echo "Incorrect username/password for your MySQL database.  Try again.";
			exit (0);
		}
		Session::put('install_progress', 10);
		Session::put('install_note', 'Database created...');
		$smtp_user = Input::get('smtp_user');
		$smtp_pass = Input::get('smtp_pass');
		$username = Input::get('username');
		$password = Hash::make(Input::get('password'));
		$email = Input::get('email');
		$practice_name = Input::get('practice_name');
		$street_address1 = Input::get('street_address1');
		$street_address2 = Input::get('street_address2');
		$city = Input::get('city');
		$state = Input::get('state');
		$zip = Input::get('zip');
		$phone = Input::get('phone');
		$fax = Input::get('fax');
		$documents_dir = Input::get('documents_dir');
		
		// Create database schema
		Artisan::call('migrate:install');
		Artisan::call('migrate');
		Session::put('install_progress', 20);
		Session::put('install_note', 'Database tables created...');
		
		// Insert core database files
		$template_sql_file = "/var/www/nosh/import/templates.sql";
		$template_command = "mysql -u " . $db_username . " -p". $db_password . " " . $db_name. " < " . $template_sql_file;
		system($template_command);
		$orderslist1_sql_file = "/var/www/nosh/import/orderslist1.sql";
		$orderslist1_command = "mysql -u " . $db_username . " -p". $db_password . " " . $db_name. " < " . $orderslist1_sql_file;
		system($orderslist1_command);
		Session::put('install_progress', 30);
		Session::put('install_note', 'Templates installed...');
		$meds_sql_file = "/var/www/nosh/import/meds_full.sql";
		$meds_command = "mysql -u " . $db_username . " -p". $db_password . " " . $db_name. " < " . $meds_sql_file;
		system($meds_command);
		$meds1_sql_file = "/var/www/nosh/import/meds_full_package.sql";
		$meds1_command = "mysql -u " . $db_username . " -p". $db_password . " " . $db_name. " < " . $meds1_sql_file;
		system($meds1_command);
		$supplements_file = "/var/www/nosh/import/supplements_list.sql";
		$supplements_command = "mysql -u " . $db_username . " -p". $db_password . " " . $db_name. " < " . $supplements_file;
		system($supplements_command);
		Session::put('install_progress', 40);
		Session::put('install_note', 'Medications and supplements installed...');
		$icd_file = "/var/www/nosh/import/icd9.sql";
		$icd_command = "mysql -u " . $db_username . " -p". $db_password . " " . $db_name. " < " . $icd_file;
		system($icd_command);
		Session::put('install_progress', 50);
		Session::put('install_note', 'ICD codes installed...');
		$cpt_file = "/var/www/nosh/import/cpt.sql";
		$cpt_command = "mysql -u " . $db_username . " -p". $db_password . " " . $db_name. " < " . $cpt_file;
		system($cpt_command);
		Session::put('install_progress', 60);
		Session::put('install_note', 'CPT codes installed...');
		$cvx_file = "/var/www/nosh/import/cvx.sql";
		$cvx_command = "mysql -u " . $db_username . " -p". $db_password . " " . $db_name. " < " . $cvx_file;
		system($cvx_command);
		$gc_file = "/var/www/nosh/import/gc.sql";
		$gc_command = "mysql -u " . $db_username. " -p". $db_password . " " . $db_name. " < " . $gc_file;
		system($gc_command);
		Session::put('install_progress', 70);
		Session::put('install_note', 'Growth charts installed...');
		$role_csv = "/var/www/nosh/import/familyrole.csv";
		if (($role_handle = fopen($role_csv, "r")) !== FALSE) {
			while (($role1 = fgetcsv($role_handle, 0, ",")) !== FALSE) {
				if ($role1[0] != '') {
					$role_description = ucfirst($role1[1]);
					$role_data = array (
						'code' => $role1[0],
						'description' => $role_description
					);
					DB::table('guardian_roles')->insert($role_data);
				}
			}
			fclose($role_csv);
		}
		$lang_csv = "/var/www/nosh/import/lang.csv";
		if (($lang_handle = fopen($lang_csv, "r")) !== FALSE) {
			while (($lang1 = fgetcsv($lang_handle, 0, "\t")) !== FALSE) {
				if ($lang1[0] != '') {
					$lang_data = array (
						'code' => $lang1[0],
						'description' => $lang1[6]
					);
					DB::table('lang')->insert($lang_data);
				}
			}
			fclose($lang_csv);
		}
		$npi_csv = '/var/www/nosh/import/npi_taxonomy.csv';
		if (($npi_handle = fopen($npi_csv, "r")) !== FALSE) {
			while (($npi1 = fgetcsv($npi_handle, 0, ",", '"')) !== FALSE) {
				if ($npi1[0] != '' || $npi1[0] != 'Code') {
					$npi_data = array (
						'code' => $npi1[0],
						'type' => $npi1[1],
						'classification' => $npi1[2],
						'specialization' => $npi1[3]
					);
					DB::table('npi')->insert($npi_data);
				}
			}
			fclose($npi_csv);
		}
		$pos_csv = '/var/www/nosh/import/pos.csv';
		if (($pos_handle = fopen($pos_csv, "r")) !== FALSE) {
			while (($pos1 = fgetcsv($pos_handle, 0, ",")) !== FALSE) {
				if ($pos1[0] != '') {
					$pos_data = array (
						'pos_id' => $pos1[0],
						'pos_description' => $pos1[1]
					);
					DB::table('pos')->insert($pos_data);
				}
			}
			fclose($pos_csv);
		}
		Session::put('install_progress', 80);
		Session::put('install_note', 'NPI, CVX, language, and POS codes installed...');
		
		// Insert Administrator
		$data1 = array(
			'username' => $username,
			'password' => $password,
			'email' => $email,
			'group_id' => '1',
			'displayname' => 'Administrator',
			'active' => '1',
			'practice_id' => '1'
		);
		$user_id = DB::table('users')->insertGetId($data1);
		
		// Insert practice
		$data2 = array(
			'practice_name' => $practice_name,
			'street_address1' => $street_address1,
			'street_address2' => $street_address2,
			'city' => $city,
			'state' => $state,
			'zip' => $zip,
			'phone' => $phone,
			'fax' => $fax,
			'documents_dir' => $documents_dir,
			'fax_type' => '',
			'smtp_user' => $smtp_user,
			'smtp_pass' => $smtp_pass,
			'vivacare' => '',
			'version' => '1.8.0',
			'active' => 'Y'
		);
		DB::table('practiceinfo')->insert($data2);
		
		// Clean up documents directory string
		$check_string = substr($documents_dir, -1);
		if ($check_string != '/') {
			$documents_dir .= '/';
		}
		
		// Insert groups
		$data3 = array(
			'id' => '1',
			'title' => 'admin',
			'description' => 'Administrator'
		);
		$data4 = array(
			'id' => '2',
			'title' => 'provider',
			'description' => 'Provider'
		);
		$data5 = array(
			'id' => '3',
			'title' => 'assistant',
			'description' => 'Assistant'
		);
		$data6 = array(
			'id' => '4',
			'title' => 'billing',
			'description' => 'Billing'
		);
		$data7 = array(
			'id' => '100',
			'title' => 'patient',
			'description' => 'Patient'
		);
		DB::table('groups')->insert(array($data3,$data4,$data5,$data6,$data7));
		
		// Insert default calendar class
		$data8 = array(
			'visit_type' => 'Closed',
			'classname' => 'colorblack',
			'active' => 'y',
			'practice_id' => '1'
		);
		DB::table('calendar')->insert($data8);
		
		// Insert default values for procedure template
		$procedurelist_data_array = array();
		$procedurelist_data_array[] = array(
			'procedure_type' => 'Laceration repair',
			'procedure_description' => '',
			'procedure_complications' => 'None.',
			'procedure_ebl' => 'Less than 5 mL.'
		);
		$procedurelist_data_array[] = array(
			'procedure_type' => 'Excision - lesion completely removed',
			'procedure_description' => '',
			'procedure_complications' => 'None.',
			'procedure_ebl' => 'Less than 5 mL.'
		);
		$procedurelist_data_array[] = array(
			'procedure_type' => 'Shave - no penetration of fat, no sutures',
			'procedure_description' => '',
			'procedure_complications' => 'None.',
			'procedure_ebl' => 'Less than 5 mL.'
		);
		$procedurelist_data_array[] = array(
			'procedure_type' => 'Biopsy - lesion partially removed',
			'procedure_description' => '',
			'procedure_complications' => 'None.',
			'procedure_ebl' => 'Less than 5 mL.'
		);
		$procedurelist_data_array[] = array(
			'procedure_type' => 'Skin tag removal',
			'procedure_description' => '' ,
			'procedure_complications' => 'None.',
			'procedure_ebl' => 'Less than 5 mL.'
		);
		$procedurelist_data_array[] = array(
			'procedure_type' => 'Cryotherapy',
			'procedure_description' => '',
			'procedure_complications' => 'None.',
			'procedure_ebl' => 'Less than 5 mL.'
		);
		DB::table('procedurelist')->insert(array($procedurelist_data_array));
		
		// Insert default values for orders template
		$orderslist_data_array = array();
		$orderslist_data_array[] = array(
			'orders_category' => 'Laboratory',
			'orders_description' => 'Comprehensive metabolic panel (CMP)',
			'snomed' => '167209002'
		);
		$orderslist_data_array[] = array(
			'orders_category' => 'Laboratory',
			'orders_description' => 'Complete blood count with platelets and differential (CBC)',
			'snomed' => '117356000'
		);
		$orderslist_data_array[] = array(
			'orders_category' => 'Laboratory',
			'orders_description' => 'Antinuclear antibody panel (ANA)',
			'snomed' => '394977005'
		);
		$orderslist_data_array[] = array(
			'orders_category' => 'Laboratory',
			'orders_description' => 'Fasting lipid panel',
			'snomed' => '394977005'
		);
		$orderslist_data_array[] = array(
			'orders_category' => 'Laboratory',
			'orders_description' => 'Erythrocyte sedimentation rate (ESR)',
			'snomed' => '104155006'
		);
		$orderslist_data_array[] = array(
			'orders_category' => 'Laboratory',
			'orders_description' => 'Hemoglobin A1c (HgbA1c)',
			'snomed' => '166902009'
		);
		$orderslist_data_array[] = array(
			'orders_category' => 'Laboratory',
			'orders_description' => 'INR',
			'snomed' => '440685005'
		);
		$orderslist_data_array[] = array(
			'orders_category' => 'Laboratory',
			'orders_description' => 'Liver function panel (LFT)',
			'snomed' => '143927001'
		);
		$orderslist_data_array[] = array(
			'orders_category' => 'Laboratory',
			'orders_description' => 'Pap smear with HPV testing',
			'snomed' => '119252009'
		);
		$orderslist_data_array[] = array(
			'orders_category' => 'Laboratory',
			'orders_description' => 'Pap smear',
			'snomed' => '119252009'
		);
		$orderslist_data_array[] = array(
			'orders_category' => 'Laboratory',
			'orders_description' => 'Prostate specific antigen (PSA)',
			'snomed' => '143526001'
		);
		$orderslist_data_array[] = array(
			'orders_category' => 'Laboratory',
			'orders_description' => 'Hepatitis C antibody',
			'snomed' => '166123004'
		);
		$orderslist_data_array[] = array(
			'orders_category' => 'Laboratory',
			'orders_description' => 'RPR',
			'snomed' => '19869000'
		);
		$orderslist_data_array[] = array(
			'orders_category' => 'Laboratory',
			'orders_description' => 'Peripheral smear',
			'snomed' => '104130000'
		);
		$orderslist_data_array[] = array(
			'orders_category' => 'Laboratory',
			'orders_description' => 'Follicle stimulating hormone (FSH)',
			'snomed' => '273971007'
		);
		$orderslist_data_array[] = array(
			'orders_category' => 'Laboratory',
			'orders_description' => 'Luteinizing hormone (LH)',
			'snomed' => '69527006'
		);
		$orderslist_data_array[] = array(
			'orders_category' => 'Laboratory',
			'orders_description' => 'Follicle stimulating hormone and Leutinizing hormone (FSH and LH)',
			'snomed' => '250660006'
		);
		$orderslist_data_array[] = array(
			'orders_category' => 'Laboratory',
			'orders_description' => 'Gonorrhea and Chlamydia GenProbe (GC/Chl PCR)',
			'snomed' => '399143002'
		);
		$orderslist_data_array[] = array(
			'orders_category' => 'Laboratory',
			'orders_description' => 'Thyroid stimulating hormone (TSH)',
			'snomed' => '313440008'
		);
		$orderslist_data_array[] = array(
			'orders_category' => 'Laboratory',
			'orders_description' => 'Thyroid panel (TSH, T3, Free T4)',
			'snomed' => '35650009'
		);
		$orderslist_data_array[] = array(
			'orders_category' => 'Laboratory',
			'orders_description' => 'Urinalysis',
			'snomed' => '53853004'
		);
		$orderslist_data_array[] = array(
			'orders_category' => 'Laboratory',
			'orders_description' => 'Urine culture',
			'snomed' => '144792004'
		);
		$orderslist_data_array[] = array(
			'orders_category' => 'Laboratory',
			'orders_description' => 'Wound culture',
			'snomed' => '77601007'
		);
		$orderslist_data_array[] = array(
			'orders_category' => 'Laboratory',
			'orders_description' => 'Respiratory Allergen Testing',
			'snomed' => '388464003'
		);
		$orderslist_data_array[] = array(
			'orders_category' => 'Laboratory',
			'orders_description' => 'Herpes Type 2 antibody',
			'snomed' => '117739006'
		);
		$orderslist_data_array[] = array(
			'orders_category' => 'Radiology',
			'orders_description' => 'CT of the abdomen with contrast',
			'snomed' => '32962002'
		);
		$orderslist_data_array[] = array(
			'orders_category' => 'Radiology',
			'orders_description' => 'CT of the abdomen without contrast',
			'snomed' => '169070004'
		);
		$orderslist_data_array[] = array(
			'orders_category' => 'Radiology',
			'orders_description' => 'CT of the chest with contrast',
			'snomed' => '75385009'
		);
		$orderslist_data_array[] = array(
			'orders_category' => 'Radiology',
			'orders_description' => 'CT of the chest without contrast',
			'snomed' => '169069000'
		);
		$orderslist_data_array[] = array(
			'orders_category' => 'Radiology',
			'orders_description' => 'CT of the head with contrast',
			'snomed' => '396207002'
		);
		$orderslist_data_array[] = array(
			'orders_category' => 'Radiology',
			'orders_description' => 'CT of the head without contrast',
			'snomed' => '396205005'
		);
		$orderslist_data_array[] = array(
			'orders_category' => 'Radiology',
			'orders_description' => 'CT of the sinuses',
			'snomed' => '431247005'
		);
		$orderslist_data_array[] = array(
			'orders_category' => 'Radiology',
			'orders_description' => 'CT of the neck with contrast',
			'snomed' => '431326009'
		);
		$orderslist_data_array[] = array(
			'orders_category' => 'Radiology',
			'orders_description' => 'CT of the neck without contrast',
			'snomed' => '169068008'
		);
		$orderslist_data_array[] = array(
			'orders_category' => 'Radiology',
			'orders_description' => 'DEXA scan',
			'snomed' => '300004007'
		);
		$orderslist_data_array[] = array(
			'orders_category' => 'Radiology',
			'orders_description' => 'Bilateral screening mammogram',
			'snomed' => '275980005'
		);
		$orderslist_data_array[] = array(
			'orders_category' => 'Frequency',
			'orders_description' => 'Once a week'
		);
		$orderslist_data_array[] = array(
			'orders_category' => 'Frequency',
			'orders_description' => 'Two times a week'
		);
		$orderslist_data_array[] = array(
			'orders_category' => 'Frequency',
			'orders_description' => 'Three times a week'
		);
		DB::table('orderslist')->insert($orderslist_data_array);
		$orderslist_data = array(
			'user_id' => '0'
		);
		DB::table('orderslist')->update($orderslist_data);
		Session::put('install_progress', 90);
		Session::put('install_note', 'Default values installed...');
		
		// Insert templates
		$template_array = array();
		$template_array[] = array(
			'category' => 'letter',
			'json' => '{"html":[{"type":"div","class":"letter_buttonset","id":"letter_school_absence_1_div","html":[{"type":"span","html":"Start Date:"},{"type":"br"},{"type":"text","id":"letter_school_absence_1a","class":"letter_date letter_start_date","css":{"width":"200px"},"name":"letter_school_absence_1","caption":""}]},{"type":"br"},{"type":"div","class":"letter_buttonset","id":"letter_school_absence_2_div","html":[{"type":"span","html":"Return Date:"},{"type":"br"},{"type":"text","id":"letter_school_absence_2a","class":"letter_date letter_return_date","css":{"width":"200px"},"name":"letter_school_absence_2","caption":""}]},{"type":"hidden","class":"letter_hidden","value":"Please excuse _firstname from school starting on _start_date.  _firstname can return to school on _return_date.","id":"letter_school_absence_hidden"}]}',
			'group' => 'school_absence',
			'sex' => 'm'
		);
		$template_array[] = array(
			'category' => 'letter',
			'json' => '{"html":[{"type":"div","class":"letter_buttonset","id":"letter_school_absence_1_div","html":[{"type":"span","html":"Start Date:"},{"type":"br"},{"type":"text","id":"letter_school_absence_1a","class":"letter_date letter_start_date","css":{"width":"200px"},"name":"letter_school_absence_1","caption":""}]},{"type":"br"},{"type":"div","class":"letter_buttonset","id":"letter_school_absence_2_div","html":[{"type":"span","html":"Return Date:"},{"type":"br"},{"type":"text","id":"letter_school_absence_2a","class":"letter_date letter_return_date","css":{"width":"200px"},"name":"letter_school_absence_2","caption":""}]},{"type":"hidden","class":"letter_hidden","value":"Please excuse _firstname from school starting on _start_date.  _firstname can return to school on _return_date.","id":"letter_school_absence_hidden"}]}',
			'group' => 'school_absence',
			'sex' => 'f'
		);
		$template_array[] = array(
			'category' => 'letter',
			'json' => '{"html": [{"type":"div","class":"letter_buttonset","id":"letter_school_return_1_div","html": [{"type":"span","html":"Return Date:"},{"type":"br"},{"type":"text","id":"letter_school_return_1a","class":"letter_date letter_return_date","css": {"width":"200px"},"name":"letter_school_return_1","caption":""}]},{"type":"hidden","class":"letter_hidden","value":"_firstname can return to school on _return_date.","id":"letter_school_return_hidden"}]}',
			'group' => 'school_return',
			'sex' => 'm'
		);
		$template_array[] = array(
			'category' => 'letter',
			'json' => '{"html": [{"type":"div","class":"letter_buttonset","id":"letter_school_return_1_div","html": [{"type":"span","html":"Return Date:"},{"type":"br"},{"type":"text","id":"letter_school_return_1a","class":"letter_date letter_return_date","css": {"width":"200px"},"name":"letter_school_return_1","caption":""}]},{"type":"hidden","class":"letter_hidden","value":"_firstname can return to school on _return_date.","id":"letter_school_return_hidden"}]}',
			'group' => 'school_return',
			'sex' => 'f'
		);
		$template_array[] = array(
			'category' => 'letter',
			'json' => '{"html":[{"type":"div","class":"letter_buttonset","id":"letter_work_absence_1_div","html":[{"type":"span","html":"Start Date:"},{"type":"br"},{"type":"text","id":"letter_work_absence_1a","class":"letter_date letter_start_date","css":{"width":"200px"},"name":"letter_work_absence_1","caption":""}]},{"type":"br"},{"type":"div","class":"letter_buttonset","id":"letter_work_absence_2_div","html":[{"type":"span","html":"Return Date:"},{"type":"br"},{"type":"text","id":"letter_work_absence_2a","class":"letter_date letter_return_date","css":{"width":"200px"},"name":"letter_work_absence_2","caption":""}]},{"type":"hidden","class":"letter_hidden","value":"Please excuse _firstname from work starting on _start_date.  _firstname can return to work on _return_date.","id":"letter_work_absence_hidden"}]}',
			'group' => 'work_absence',
			'sex' => 'm'
		);
		$template_array[] = array(
			'category' => 'letter',
			'json' => '{"html":[{"type":"div","class":"letter_buttonset","id":"letter_work_absence_1_div","html":[{"type":"span","html":"Start Date:"},{"type":"br"},{"type":"text","id":"letter_work_absence_1a","class":"letter_date letter_start_date","css":{"width":"200px"},"name":"letter_work_absence_1","caption":""}]},{"type":"br"},{"type":"div","class":"letter_buttonset","id":"letter_work_absence_2_div","html":[{"type":"span","html":"Return Date:"},{"type":"br"},{"type":"text","id":"letter_work_absence_2a","class":"letter_date letter_return_date","css":{"width":"200px"},"name":"letter_work_absence_2","caption":""}]},{"type":"hidden","class":"letter_hidden","value":"Please excuse _firstname from work starting on _start_date.  _firstname can return to work on _return_date.","id":"letter_work_absence_hidden"}]}',
			'group' => 'work_absence',
			'sex' => 'f'
		);
		$template_array[] = array(
			'category' => 'letter',
			'json' => '{"html": [{"type":"div","class":"letter_buttonset","id":"letter_work_return_1_div","html": [{"type":"span","html":"Return Date:"},{"type":"br"},{"type":"text","id":"letter_work_return_1a","class":"letter_date letter_return_date","css": {"width":"200px"},"name":"letter_work_return_1","caption":""}]},{"type":"hidden","class":"letter_hidden","value":"_firstname can return to work on _return_date.","id":"letter_work_return_hidden"}]}',
			'group' => 'work_return',
			'sex' => 'm'
		);
		$template_array[] = array(
			'category' => 'letter',
			'json' => '{"html": [{"type":"div","class":"letter_buttonset","id":"letter_work_return_1_div","html": [{"type":"span","html":"Return Date:"},{"type":"br"},{"type":"text","id":"letter_work_return_1a","class":"letter_date letter_return_date","css": {"width":"200px"},"name":"letter_work_return_1","caption":""}]},{"type":"hidden","class":"letter_hidden","value":"_firstname can return to work on _return_date.","id":"letter_work_return_hidden"}]}',
			'group' => 'work_return',
			'sex' => 'f'
		);
		$template_array[] = array(
			'category' => 'letter',
			'json' => '{"html": [{"type":"div","class":"letter_buttonset","id":"letter_work_modified_1_div","html": [{"type":"span","html":"Start Date:"},{"type":"br"},{"type":"text","id":"letter_work_modified_1a","class":"letter_date letter_start_date","css": {"width":"200px"},"name":"letter_work_modified_1","caption":""}]},{"type":"br"},{"type":"div","class":"letter_buttonset","id":"letter_work_modified_2_div","html": [{"type":"span","html":"End Date:"},{"type":"br"},{"type":"text","id":"letter_work_modified_2a","class":"letter_date letter_end_date","css": {"width":"200px"},"name":"letter_work_modified_2","caption":""}]},{"type":"hidden","class":"letter_hidden","value":"_firstname should begin the following modified work restrictions starting on _start_date and ending on _end_date.","id":"letter_work_modified_hidden"},{"type":"div","class":"letter_buttonset","id":"letter_work_modified_3_div","html": [{"type":"span","html":"Select from list:"},{"type":"br"},{"type":"select","multiple":"multiple","id":"letter_work_modified_3a","class":"letter_select","css": {"width":"200px"},"name":"letter_work_modified_3","caption":"","options": {"shoulder": {"type":"optgroup","label":"Shoulder","options": {"Limited use of the right shoulder.  ":"Limited use of the right shoulder.","No use of the right shoulder.  ":"No use of the right shoulder.","Limited use of the left shoulder.  ":"Limited use of the left shoulder.","No use of the left shoulder.  ":"No use of the left shoulder.","Limited use of both shoulders.  ":"Limited use of both shoulders.","No use of both shoulders.  ":"No use of both shoulders."}},"arm": {"type":"optgroup","label":"Arm","options": {"Limited use of the right arm.  ":"Limited use of the right arm.","No use of the right arm.  ":"No use of the right arm.","Limited use of the left arm.  ":"Limited use of the left arm.","No use of the left arm.  ":"No use of the left arm.","Limited use of both arms.  ":"Limited use of both arms.","No use of both arms.  ":"No use of both arms."}},"hand": {"type":"optgroup","label":"Hand","options": {"Limited use of the right hand.  ":"Limited use of the right hand.","No use of the right hand.  ":"No use of the right hand.","Limited use of the left hand.  ":"Limited use of the left hand.","No use of the left hand.  ":"No use of the left hand.","Limited use of both hands.  ":"Limited use of both hands.","No use of both hands.  ":"No use of both hands."}},"leg": {"type":"optgroup","label":"Leg","options": {"Limited use of the right leg.  ":"Limited use of the right leg.","No use of the right leg.  ":"No use of the right leg.","Limited use of the left leg.  ":"Limited use of the left leg.","No use of the left leg.  ":"No use of the left leg.","Limited use of both legs.  ":"Limited use of both legs.","No use of both legs.  ":"No use of both legs."}},"device": {"type":"optgroup","label":"Devices","options": {"Need to use splint provided while at work.  ":"Need to use splint provided while at work.","Need to use crutches provided while at work.  ":"Need to use crutches provided while at work.","Need to use back brace provided while at work.":"Need to use back brace provided while at work."}},"actions": {"type":"optgroup","label":"Actions","options": {"Limited bending.  ":"Limited bending.","No bending.  ":"No bending.","Limited climbing.  ":"Limited climbing.","No climbing.  ":"No climbing.","Limited heavy lifting.  ":"Limited heavy lifting.","No heavy lifting.  ":"No heavy lifting.","Limited overhead reaching.  ":"Limited overhead reaching.","No overhead reaching.  ":"No overhead reaching.","Limited pulling.  ":"Limited pulling.","No pulling.  ":"No pulling.","Limited pushing.  ":"Limited pushing.","No pushing.  ":"No pushing.","Limited squatting.  ":"Limited squatting.","No squatting.  ":"No squatting.","Limited standing.  ":"Limited standing.","No standing.  ":"No standing","Limited stooping.  ":"Limited stooping.","No stooping.  ":"No stooping.","Limited twisting.  ":"Limited twisting.","No twisting.  ":"No twisting.","Limited weight bearing.  ":"Limited weight bearing.","No weight bearing.  ":"No weight bearing.","Limited work near moving machinery.  ":"Limited work near moving machinery.","No work near moving machinery.  ":"No work near moving machinery.","Limited work requiring depth perception.  ":"Limited work requiring depth perception.","No work requiring depth perception.  ":"No work requiring depth perception."}}}}]}]}',
			'group' => 'work_modified',
			'sex' => 'm'
		);
		$template_array[] = array(
			'category' => 'letter',
			'json' => '{"html": [{"type":"div","class":"letter_buttonset","id":"letter_work_modified_1_div","html": [{"type":"span","html":"Start Date:"},{"type":"br"},{"type":"text","id":"letter_work_modified_1a","class":"letter_date letter_start_date","css": {"width":"200px"},"name":"letter_work_modified_1","caption":""}]},{"type":"br"},{"type":"div","class":"letter_buttonset","id":"letter_work_modified_2_div","html": [{"type":"span","html":"End Date:"},{"type":"br"},{"type":"text","id":"letter_work_modified_2a","class":"letter_date letter_end_date","css": {"width":"200px"},"name":"letter_work_modified_2","caption":""}]},{"type":"hidden","class":"letter_hidden","value":"_firstname should begin the following modified work restrictions starting on _start_date and ending on _end_date.","id":"letter_work_modified_hidden"},{"type":"div","class":"letter_buttonset","id":"letter_work_modified_3_div","html": [{"type":"span","html":"Select from list:"},{"type":"br"},{"type":"select","multiple":"multiple","id":"letter_work_modified_3a","class":"letter_select","css": {"width":"200px"},"name":"letter_work_modified_3","caption":"","options": {"shoulder": {"type":"optgroup","label":"Shoulder","options": {"Limited use of the right shoulder.  ":"Limited use of the right shoulder.","No use of the right shoulder.  ":"No use of the right shoulder.","Limited use of the left shoulder.  ":"Limited use of the left shoulder.","No use of the left shoulder.  ":"No use of the left shoulder.","Limited use of both shoulders.  ":"Limited use of both shoulders.","No use of both shoulders.  ":"No use of both shoulders."}},"arm": {"type":"optgroup","label":"Arm","options": {"Limited use of the right arm.  ":"Limited use of the right arm.","No use of the right arm.  ":"No use of the right arm.","Limited use of the left arm.  ":"Limited use of the left arm.","No use of the left arm.  ":"No use of the left arm.","Limited use of both arms.  ":"Limited use of both arms.","No use of both arms.  ":"No use of both arms."}},"hand": {"type":"optgroup","label":"Hand","options": {"Limited use of the right hand.  ":"Limited use of the right hand.","No use of the right hand.  ":"No use of the right hand.","Limited use of the left hand.  ":"Limited use of the left hand.","No use of the left hand.  ":"No use of the left hand.","Limited use of both hands.  ":"Limited use of both hands.","No use of both hands.  ":"No use of both hands."}},"leg": {"type":"optgroup","label":"Leg","options": {"Limited use of the right leg.  ":"Limited use of the right leg.","No use of the right leg.  ":"No use of the right leg.","Limited use of the left leg.  ":"Limited use of the left leg.","No use of the left leg.  ":"No use of the left leg.","Limited use of both legs.  ":"Limited use of both legs.","No use of both legs.  ":"No use of both legs."}},"device": {"type":"optgroup","label":"Devices","options": {"Need to use splint provided while at work.  ":"Need to use splint provided while at work.","Need to use crutches provided while at work.  ":"Need to use crutches provided while at work.","Need to use back brace provided while at work.":"Need to use back brace provided while at work."}},"actions": {"type":"optgroup","label":"Actions","options": {"Limited bending.  ":"Limited bending.","No bending.  ":"No bending.","Limited climbing.  ":"Limited climbing.","No climbing.  ":"No climbing.","Limited heavy lifting.  ":"Limited heavy lifting.","No heavy lifting.  ":"No heavy lifting.","Limited overhead reaching.  ":"Limited overhead reaching.","No overhead reaching.  ":"No overhead reaching.","Limited pulling.  ":"Limited pulling.","No pulling.  ":"No pulling.","Limited pushing.  ":"Limited pushing.","No pushing.  ":"No pushing.","Limited squatting.  ":"Limited squatting.","No squatting.  ":"No squatting.","Limited standing.  ":"Limited standing.","No standing.  ":"No standing","Limited stooping.  ":"Limited stooping.","No stooping.  ":"No stooping.","Limited twisting.  ":"Limited twisting.","No twisting.  ":"No twisting.","Limited weight bearing.  ":"Limited weight bearing.","No weight bearing.  ":"No weight bearing.","Limited work near moving machinery.  ":"Limited work near moving machinery.","No work near moving machinery.  ":"No work near moving machinery.","Limited work requiring depth perception.  ":"Limited work requiring depth perception.","No work requiring depth perception.  ":"No work requiring depth perception."}}}}]}]}',
			'group' => 'work_modified',
			'sex' => 'f'
		);
		$template_array[] = array(
			'category' => 'referral',
			'json' => '{"html":[{"type":"hidden","class":"ref_hidden","value":"Referral - Please provide primary physician with summaries of subsequent visits.","id":"ref_referral_hidden"},{"type":"checkbox","id":"ref_referral_1","class":"ref_other ref_intro","value":"Assume management for this particular problem and return patient after conclusion of care.","name":"ref_referral_1","caption":"Return patient after managing particular problem"},{"type":"br"},{"type":"checkbox","id":"ref_referral_2","class":"ref_other ref_intro","value":"Assume future management of patient within your area of expertise.","name":"ref_referral_2","caption":"Future ongoing management"},{"type":"br"},{"type":"checkbox","id":"ref_referral_3","class":"ref_other ref_after","value":"Please call me when you have seen the patient.","name":"ref_referral_3","caption":"Call back"},{"type":"br"},{"type":"checkbox","id":"ref_referral_4","class":"ref_other ref_after","value":"I would like to receive periodic status reports on this patient.","name":"ref_referral_4","caption":"Receive periodic status reports"},{"type":"br"},{"type":"checkbox","id":"ref_referral_5","class":"ref_other ref_after","value":"Please send a thorough written report when the consultation is complete.","name":"ref_referral_5","caption":"Receive thorough written report"}]}',
			'group' => 'referral',
			'sex' => 'm'
		);
		$template_array[] = array(
			'category' => 'referral',
			'json' => '{"html":[{"type":"hidden","class":"ref_hidden","value":"Referral - Please provide primary physician with summaries of subsequent visits.","id":"ref_referral_hidden"},{"type":"checkbox","id":"ref_referral_1","class":"ref_other ref_intro","value":"Assume management for this particular problem and return patient after conclusion of care.","name":"ref_referral_1","caption":"Return patient after managing particular problem"},{"type":"br"},{"type":"checkbox","id":"ref_referral_2","class":"ref_other ref_intro","value":"Assume future management of patient within your area of expertise.","name":"ref_referral_2","caption":"Future ongoing management"},{"type":"br"},{"type":"checkbox","id":"ref_referral_3","class":"ref_other ref_after","value":"Please call me when you have seen the patient.","name":"ref_referral_3","caption":"Call back"},{"type":"br"},{"type":"checkbox","id":"ref_referral_4","class":"ref_other ref_after","value":"I would like to receive periodic status reports on this patient.","name":"ref_referral_4","caption":"Receive periodic status reports"},{"type":"br"},{"type":"checkbox","id":"ref_referral_5","class":"ref_other ref_after","value":"Please send a thorough written report when the consultation is complete.","name":"ref_referral_5","caption":"Receive thorough written report"}]}',
			'group' => 'referral',
			'sex' => 'f'
		);
		$template_array[] = array(
			'category' => 'referral',
			'json' => '{"html":[{"type":"hidden","class":"ref_hidden","value":"Consultation - Please send the patient back for follow-up and treatment.","id":"ref_consultation_hidden"},{"type":"checkbox","id":"ref_consultation_1","class":"ref_other ref_intro","value":"Confirm the diagnosis.","name":"ref_consultation_1","caption":"Confirm the diagnosis"},{"type":"br"},{"type":"checkbox","id":"ref_consultation_2","class":"ref_other ref_intro","value":"Advise as to the diagnosis.","name":"ref_consultation_2","caption":"Advise as to the diagnosis"},{"type":"br"},{"type":"checkbox","id":"ref_consultation_3","class":"ref_other ref_intro","value":"Suggest medication or treatment for the diagnosis.","name":"ref_consultation_3","caption":"Suggest medication or treatment"},{"type":"br"},{"type":"checkbox","id":"ref_consultation_4","class":"ref_other ref_after","value":"Please call me when you have seen the patient.","name":"ref_consultation_4","caption":"Call back"},{"type":"br"},{"type":"checkbox","id":"ref_consultation_5","class":"ref_other ref_after","value":"I would like to receive periodic status reports on this patient.","name":"ref_consultation_5","caption":"Receive periodic status reports"},{"type":"br"},{"type":"checkbox","id":"ref_consultation_6","class":"ref_other ref_after","value":"Please send a thorough written report when the consultation is complete.","name":"ref_consultation_6","caption":"Receive thorough written report"}]}',
			'group' => 'consultation',
			'sex' => 'm'
		);
		$template_array[] = array(
			'category' => 'referral',
			'json' => '{"html":[{"type":"hidden","class":"ref_hidden","value":"Consultation - Please send the patient back for follow-up and treatment.","id":"ref_consultation_hidden"},{"type":"checkbox","id":"ref_consultation_1","class":"ref_other ref_intro","value":"Confirm the diagnosis.","name":"ref_consultation_1","caption":"Confirm the diagnosis"},{"type":"br"},{"type":"checkbox","id":"ref_consultation_2","class":"ref_other ref_intro","value":"Advise as to the diagnosis.","name":"ref_consultation_2","caption":"Advise as to the diagnosis"},{"type":"br"},{"type":"checkbox","id":"ref_consultation_3","class":"ref_other ref_intro","value":"Suggest medication or treatment for the diagnosis.","name":"ref_consultation_3","caption":"Suggest medication or treatment"},{"type":"br"},{"type":"checkbox","id":"ref_consultation_4","class":"ref_other ref_after","value":"Please call me when you have seen the patient.","name":"ref_consultation_4","caption":"Call back"},{"type":"br"},{"type":"checkbox","id":"ref_consultation_5","class":"ref_other ref_after","value":"I would like to receive periodic status reports on this patient.","name":"ref_consultation_5","caption":"Receive periodic status reports"},{"type":"br"},{"type":"checkbox","id":"ref_consultation_6","class":"ref_other ref_after","value":"Please send a thorough written report when the consultation is complete.","name":"ref_consultation_6","caption":"Receive thorough written report"}]}',
			'group' => 'consultation',
			'sex' => 'f'
		);
		$template_array[] = array(
			'category' => 'referral',
			'json' => '{"html":[{"type":"hidden","class":"ref_hidden","value":"Physical therapy referral details:","id":"ref_pt_hidden"},{"type":"div","class":"ref_buttonset","id":"ref_pt_1_div","html":[{"type":"span","html":"Objectives:"},{"type":"br"},{"type":"checkbox","id":"ref_pt_1a","class":"ref_other ref_intro","value":"Decrease pain.","name":"ref_pt_1","caption":"Decrease pain"},{"type":"checkbox","id":"ref_pt_1b","class":"ref_other ref_intro","value":"Increase strength.","name":"ref_pt_1","caption":"Increase strength"},{"type":"checkbox","id":"ref_pt_1c","class":"ref_other ref_intro","value":"Increase mobility.","name":"ref_pt_1","caption":"Increase mobility"}]},{"type":"br"},{"type":"div","class":"ref_buttonset","id":"ref_pt_2_div","html":[{"type":"span","html":"Modalities:"},{"type":"br"},{"type":"select","multiple":"multiple","id":"ref_pt_2","class":"ref_select ref_intro","css":{"width":"200px"},"name":"ref_pt_2","caption":"","options":{"Hot or cold packs. ":"Hot or cold packs.","TENS unit. ":"TENS unit.","Back program. ":"Back program.","Joint mobilization. ":"Joint mobilization.","Home program. ":"Home program.","Pool therapy. ":"Pool therapy.","Feldenkrais method. ":"Feldenkrais method.","Therapeutic exercise. ":"Therapeutic exercise.","Myofascial release. ":"Myofascial release.","Patient education. ":"Patient education.","Work hardening. ":"Work hardening."}}]},{"type":"br"},{"type":"text","id":"ref_pt_3","css":{"width":"200px"},"class":"ref_other ref_detail_text ref_intro","name":"ref_pt_3","placeholder":"Precautions"},{"type":"br"},{"type":"text","id":"ref_pt_4","css":{"width":"200px"},"class":"ref_other ref_detail_text ref_intro","name":"ref_pt_4","placeholder":"Frequency"},{"type":"br"},{"type":"text","id":"ref_pt_5","css":{"width":"200px"},"class":"ref_other ref_detail_text ref_intro","name":"ref_pt_5","placeholder":"Duration"}]}',
			'group' => 'pt',
			'sex' => 'm'
		);
		$template_array[] = array(
			'category' => 'referral',
			'json' => '{"html":[{"type":"hidden","class":"ref_hidden","value":"Physical therapy referral details:","id":"ref_pt_hidden"},{"type":"div","class":"ref_buttonset","id":"ref_pt_1_div","html":[{"type":"span","html":"Objectives:"},{"type":"br"},{"type":"checkbox","id":"ref_pt_1a","class":"ref_other ref_intro","value":"Decrease pain.","name":"ref_pt_1","caption":"Decrease pain"},{"type":"checkbox","id":"ref_pt_1b","class":"ref_other ref_intro","value":"Increase strength.","name":"ref_pt_1","caption":"Increase strength"},{"type":"checkbox","id":"ref_pt_1c","class":"ref_other ref_intro","value":"Increase mobility.","name":"ref_pt_1","caption":"Increase mobility"}]},{"type":"br"},{"type":"div","class":"ref_buttonset","id":"ref_pt_2_div","html":[{"type":"span","html":"Modalities:"},{"type":"br"},{"type":"select","multiple":"multiple","id":"ref_pt_2","class":"ref_select ref_intro","css":{"width":"200px"},"name":"ref_pt_2","caption":"","options":{"Hot or cold packs. ":"Hot or cold packs.","TENS unit. ":"TENS unit.","Back program. ":"Back program.","Joint mobilization. ":"Joint mobilization.","Home program. ":"Home program.","Pool therapy. ":"Pool therapy.","Feldenkrais method. ":"Feldenkrais method.","Therapeutic exercise. ":"Therapeutic exercise.","Myofascial release. ":"Myofascial release.","Patient education. ":"Patient education.","Work hardening. ":"Work hardening."}}]},{"type":"br"},{"type":"text","id":"ref_pt_3","css":{"width":"200px"},"class":"ref_other ref_detail_text ref_intro","name":"ref_pt_3","placeholder":"Precautions"},{"type":"br"},{"type":"text","id":"ref_pt_4","css":{"width":"200px"},"class":"ref_other ref_detail_text ref_intro","name":"ref_pt_4","placeholder":"Frequency"},{"type":"br"},{"type":"text","id":"ref_pt_5","css":{"width":"200px"},"class":"ref_other ref_detail_text ref_intro","name":"ref_pt_5","placeholder":"Duration"}]}',
			'group' => 'pt',
			'sex' => 'f'
		);
		$template_array[] = array(
			'category' => 'referral',
			'json' => '{"html":[{"type":"hidden","class":"ref_hidden","value":"Massage therapy referral details:","id":"ref_massage_hidden"},{"type":"div","class":"ref_buttonset","id":"ref_massage_1_div","html":[{"type":"span","html":"Objectives:"},{"type":"br"},{"type":"checkbox","id":"ref_massage_1a","class":"ref_other ref_intro","value":"Decrease pain.","name":"ref_massage_1","caption":"Decrease pain"},{"type":"checkbox","id":"ref_massage_1b","class":"ref_other ref_intro","value":"Increase mobility.","name":"ref_massage_1","caption":"Increase mobility"}]},{"type":"br"},{"type":"text","id":"ref_massage_2","css":{"width":"200px"},"class":"ref_other ref_detail_text ref_intro","name":"ref_massage_2","placeholder":"Precautions"},{"type":"br"},{"type":"text","id":"ref_massage_3","css":{"width":"200px"},"class":"ref_other ref_detail_text ref_intro","name":"ref_massage_3","placeholder":"Frequency"},{"type":"br"},{"type":"text","id":"ref_massage_4","css":{"width":"200px"},"class":"ref_other ref_detail_text ref_intro","name":"ref_massage_4","placeholder":"Duration"}]}',
			'group' => 'massage',
			'sex' => 'm'
		);
		$template_array[] = array(
			'category' => 'referral',
			'json' => '{"html":[{"type":"hidden","class":"ref_hidden","value":"Massage therapy referral details:","id":"ref_massage_hidden"},{"type":"div","class":"ref_buttonset","id":"ref_massage_1_div","html":[{"type":"span","html":"Objectives:"},{"type":"br"},{"type":"checkbox","id":"ref_massage_1a","class":"ref_other ref_intro","value":"Decrease pain.","name":"ref_massage_1","caption":"Decrease pain"},{"type":"checkbox","id":"ref_massage_1b","class":"ref_other ref_intro","value":"Increase mobility.","name":"ref_massage_1","caption":"Increase mobility"}]},{"type":"br"},{"type":"text","id":"ref_massage_2","css":{"width":"200px"},"class":"ref_other ref_detail_text ref_intro","name":"ref_massage_2","placeholder":"Precautions"},{"type":"br"},{"type":"text","id":"ref_massage_3","css":{"width":"200px"},"class":"ref_other ref_detail_text ref_intro","name":"ref_massage_3","placeholder":"Frequency"},{"type":"br"},{"type":"text","id":"ref_massage_4","css":{"width":"200px"},"class":"ref_other ref_detail_text ref_intro","name":"ref_massage_4","placeholder":"Duration"}]}',
			'group' => 'massage',
			'sex' => 'f'
		);
		$template_array[] = array(
			'category' => 'referral',
			'json' => '{"html":[{"type":"hidden","class":"ref_hidden","value":"Sleep study referral details:","id":"ref_sleep_study_hidden"},{"type":"div","class":"ref_buttonset","id":"ref_sleep_study_1_div","html":[{"type":"span","html":"Type:"},{"type":"br"},{"type":"select","multiple":"multiple","id":"ref_sleep_study_1","class":"ref_select ref_other ref_intro","css":{"width":"200px"},"name":"ref_sleep_study_1","caption":"","options":{"Diagnostic Sleep Study Only.\n":"Diagnostic Sleep Study Only.","Diagnostic testing with Continuous Positive Airway Pressure.\n":"Diagnostic testing with Continuous Positive Airway Pressure.","Diagnostic testing with BiLevel Positive Airway Pressure.\n":"Diagnostic testing with BiLevel Positive Airway Pressure.","Diagnostic testing with BiLevel Positive Airway Pressure.\n":"Diagnostic testing with BiLevel Positive Airway Pressure.","Diagnostic testing with Oxygen.\n":"Diagnostic testing with Oxygen.","Diagnostic testing with Oral Device.\n":"Diagnostic testing with Oral Device.","MSLT (Multiple Sleep Latency Test).\n":"MSLT (Multiple Sleep Latency Test).","MWT (Maintenance of Wakefulness Test).\n":"MWT (Maintenance of Wakefulness Test).","Titrate BiPAP settings.\n":"Titrate BiPAP settings.","Patient education. ":"Patient education.","Work hardening. ":"Work hardening."}}]},{"type":"br"},{"type":"div","class":"ref_buttonset","id":"ref_sleep_study_2_div","html":[{"type":"span","html":"BiPAP pressures:"},{"type":"br"},{"type":"text","id":"ref_sleep_study_2a","css":{"width":"200px"},"class":"ref_other ref_detail_text ref_intro","name":"ref_sleep_study_2a","placeholder":"Inspiratory Pressure (IPAP), cm H20"},{"type":"br"},{"type":"text","id":"ref_sleep_study_2b","css":{"width":"200px"},"class":"ref_other ref_detail_text ref_intro","name":"ref_sleep_study_2b","placeholder":"Expiratory Pressure (EPAP), cm H20"}]},{"type":"br"},{"type":"div","class":"ref_buttonset","id":"ref_sleep_study_3_div","html":[{"type":"span","html":"BiPAP Mode:"},{"type":"br"},{"type":"checkbox","id":"ref_sleep_study_3a","class":"ref_other ref_intro","value":"Spontaneous mode.","name":"ref_sleep_study_3","caption":"Spontaneous"},{"type":"checkbox","id":"ref_sleep_study_3b","class":"ref_other ref_intro","value":"Spontaneous/Timed mode","name":"ref_sleep_study_3","caption":"Spontaneous/Timed"},{"type":"br"},{"type":"text","id":"ref_sleep_study_3c","css":{"width":"200px"},"class":"ref_other ref_detail_text ref_intro","name":"ref_sleep_study_3","placeholder":"Breaths per minute"}]}]}',
			'group' => 'sleep_study',
			'sex' => 'm'
		);
		$template_array[] = array(
			'category' => 'referral',
			'json' => '{"html":[{"type":"hidden","class":"ref_hidden","value":"Sleep study referral details:","id":"ref_sleep_study_hidden"},{"type":"div","class":"ref_buttonset","id":"ref_sleep_study_1_div","html":[{"type":"span","html":"Type:"},{"type":"br"},{"type":"select","multiple":"multiple","id":"ref_sleep_study_1","class":"ref_select ref_other ref_intro","css":{"width":"200px"},"name":"ref_sleep_study_1","caption":"","options":{"Diagnostic Sleep Study Only.\n":"Diagnostic Sleep Study Only.","Diagnostic testing with Continuous Positive Airway Pressure.\n":"Diagnostic testing with Continuous Positive Airway Pressure.","Diagnostic testing with BiLevel Positive Airway Pressure.\n":"Diagnostic testing with BiLevel Positive Airway Pressure.","Diagnostic testing with BiLevel Positive Airway Pressure.\n":"Diagnostic testing with BiLevel Positive Airway Pressure.","Diagnostic testing with Oxygen.\n":"Diagnostic testing with Oxygen.","Diagnostic testing with Oral Device.\n":"Diagnostic testing with Oral Device.","MSLT (Multiple Sleep Latency Test).\n":"MSLT (Multiple Sleep Latency Test).","MWT (Maintenance of Wakefulness Test).\n":"MWT (Maintenance of Wakefulness Test).","Titrate BiPAP settings.\n":"Titrate BiPAP settings.","Patient education. ":"Patient education.","Work hardening. ":"Work hardening."}}]},{"type":"br"},{"type":"div","class":"ref_buttonset","id":"ref_sleep_study_2_div","html":[{"type":"span","html":"BiPAP pressures:"},{"type":"br"},{"type":"text","id":"ref_sleep_study_2a","css":{"width":"200px"},"class":"ref_other ref_detail_text ref_intro","name":"ref_sleep_study_2a","placeholder":"Inspiratory Pressure (IPAP), cm H20"},{"type":"br"},{"type":"text","id":"ref_sleep_study_2b","css":{"width":"200px"},"class":"ref_other ref_detail_text ref_intro","name":"ref_sleep_study_2b","placeholder":"Expiratory Pressure (EPAP), cm H20"}]},{"type":"br"},{"type":"div","class":"ref_buttonset","id":"ref_sleep_study_3_div","html":[{"type":"span","html":"BiPAP Mode:"},{"type":"br"},{"type":"checkbox","id":"ref_sleep_study_3a","class":"ref_other ref_intro","value":"Spontaneous mode.","name":"ref_sleep_study_3","caption":"Spontaneous"},{"type":"checkbox","id":"ref_sleep_study_3b","class":"ref_other ref_intro","value":"Spontaneous/Timed mode","name":"ref_sleep_study_3","caption":"Spontaneous/Timed"},{"type":"br"},{"type":"text","id":"ref_sleep_study_3c","css":{"width":"200px"},"class":"ref_other ref_detail_text ref_intro","name":"ref_sleep_study_3","placeholder":"Breaths per minute"}]}]}',
			'group' => 'sleep_study',
			'sex' => 'f'
		);
		foreach ($template_array as $template_ind) {
			$template_array = serialize(json_decode($template_ind['json']));
			$template_data = array(
				'user_id' => '0',
				'template_name' => 'Global Default',
				'default' => 'default',
				'category' => $template_ind['category'],
				'sex' => $template_ind['sex'],
				'group' => $template_ind['group'],
				'array' => $template_array
			);
			DB::table('templates')->insert($template_data);
		}
		Auth::attempt(array('username' => $username, 'password' => $password, 'active' => '1', 'practice_id' => '1'));
		Session::put('user_id', $user_id);
		Session::put('group_id', '1');
		Session::put('practice_id', '1');
		Session::put('install_progress', 100);
		Session::put('install_note', 'Administrator user logged in and installation is complete.');
		echo "OK";
	}
	
	public function postInstallProgress()
	{
		$result = array();
		$result['install_progress'] = Session::get('install_progress');
		$result['install_note'] = Session::get('install_note');
		echo json_encode($result);
	}
	
	public function postDirectoryCheck()
	{
		$documents_dir = Input::get('documents_dir');
		if (!is_writable($documents_dir)) {
			echo "'" . $documents_dir . "' is not writable.";
		} else {
			echo "OK";
		}
	}
	
	public function postDatabaseFix()
	{
		$db_username = Input::get('db_username');
		$db_password = Input::get('db_password');
		$connect = mysqli_connect('localhost', $db_username, $db_password);
		$db = mysqli_select_db($connect,'nosh');
		if ($db) {
			$filename = "/var/www/laravel/.env.php";
			$config = include $filename;
			$config['mysql_username'] = $db_username;
			$config['mysql_password'] = $db_password;
			file_put_contents($filename, '<?php return ' . var_export($config, true) . ";\n");
			echo "OK";
		} else {
			echo "Incorrect username/password for your MySQL database.  Try again.";
		}
	}
	
	public function set_version()
	{
		$result = $this->github_all();
		File::put(__DIR__."/../../.version", $result[0]['sha']);
	}
	
	public function update()
	{
		if (!Schema::hasTable('migrations')) {
			Artisan::call('migrate:install');
		}
		Artisan::call('migrate');
		$practice = Practiceinfo::find(1);
		if ($practice->version < "1.8.0") {
			$this->update180();
		}
		return Redirect::to('/');
	}
	
	public function update180()
	{
		$orderslist1_array = array();
		$orderslist1_array[] = array(
			'orders_code' => '11550',
			'aoe_code' => 'CHM1^FASTING STATE:',
			'aoe_field' => 'aoe_fasting_code'
		);
		$orderslist1_array[] = array(
			'orders_code' => '12500',
			'aoe_code' => 'CHM1^FASTING STATE:',
			'aoe_field' => 'aoe_fasting_code'
		);
		$orderslist1_array[] = array(
			'orders_code' => '24080',
			'aoe_code' => 'MIC1^SOURCE:',
			'aoe_field' => 'aoe_source_code'
		);
		$orderslist1_array[] = array(
			'orders_code' => '30000',
			'aoe_code' => 'CHM1^FASTING STATE:',
			'aoe_field' => 'aoe_fasting_code'
		);
		$orderslist1_array[] = array(
			'orders_code' => '30740',
			'aoe_code' => 'CHM1^FASTING STATE:',
			'aoe_field' => 'aoe_fasting_code'
		);
		$orderslist1_array[] = array(
			'orders_code' => '30820',
			'aoe_code' => 'GLUFAST^HOURS FASTING:',
			'aoe_field' => 'aoe_fasting_hours_code'
		);
		$orderslist1_array[] = array(
			'orders_code' => '31300',
			'aoe_code' => 'CHM1^FASTING STATE:',
			'aoe_field' => 'aoe_fasting_code'
		);
		$orderslist1_array[] = array(
			'orders_code' => '33320',
			'aoe_code' => 'TDM1^LAST DOSE DATE:;TDM2^LAST DOSE TIME:',
			'aoe_field' => 'aoe_dose_date_code;aoe_dose_time_code'
		);
		$orderslist1_array[] = array(
			'orders_code' => '43540',
			'aoe_code' => 'CHM1^FASTING STATE:',
			'aoe_field' => 'aoe_fasting_code'
		);
		$orderslist1_array[] = array(
			'orders_code' => '43542',
			'aoe_code' => 'CHM1^FASTING STATE:',
			'aoe_field' => 'aoe_fasting_code'
		);
		$orderslist1_array[] = array(
			'orders_code' => '43546',
			'aoe_code' => 'CHM1^FASTING STATE:',
			'aoe_field' => 'aoe_fasting_code'
		);
		$orderslist1_array[] = array(
			'orders_code' => '60109',
			'aoe_code' => 'BFL1^SOURCE:',
			'aoe_field' => 'aoe_source1_code'
		);
		$orderslist1_array[] = array(
			'orders_code' => '61500',
			'aoe_code' => 'MIC1^SOURCE:;MIC2^ADD. INFORMATION:',
			'aoe_field' => 'aoe_source_code;aoe_additional_code'
		);
		$orderslist1_array[] = array(
			'orders_code' => '68329',
			'aoe_code' => 'MIC1^SOURCE:',
			'aoe_field' => 'aoe_source_code'
		);
		foreach ($orderslist1_array as $row1) {
			$order_query = DB::table('orderslist1')->where('orders_code', '=', $row1['orders_code'])->get();
			foreach ($orders_query as $row2) {
				$orders_data = array(
					'aoe_code' => $row1['aoe_code'],
					'aoe_field' => $row1['aoe_field']
				);
				DB::table('orderslist1')->where('orderslist1_id', '=', $row2->orderslist1_id)->update($orders_data);
			}
		}
		// Update referral templates
		$template_query = DB::table('templates')->where('category', '=', 'referral')->first();
		if (!$template_query) {
			$template_array = array();
			$template_array[] = array(
				'category' => 'referral',
				'json' => '{"html":[{"type":"hidden","class":"ref_hidden","value":"Referral - Please provide primary physician with summaries of subsequent visits.","id":"ref_referral_hidden"},{"type":"checkbox","id":"ref_referral_1","class":"ref_other ref_intro","value":"Assume management for this particular problem and return patient after conclusion of care.","name":"ref_referral_1","caption":"Return patient after managing particular problem"},{"type":"br"},{"type":"checkbox","id":"ref_referral_2","class":"ref_other ref_intro","value":"Assume future management of patient within your area of expertise.","name":"ref_referral_2","caption":"Future ongoing management"},{"type":"br"},{"type":"checkbox","id":"ref_referral_3","class":"ref_other ref_after","value":"Please call me when you have seen the patient.","name":"ref_referral_3","caption":"Call back"},{"type":"br"},{"type":"checkbox","id":"ref_referral_4","class":"ref_other ref_after","value":"I would like to receive periodic status reports on this patient.","name":"ref_referral_4","caption":"Receive periodic status reports"},{"type":"br"},{"type":"checkbox","id":"ref_referral_5","class":"ref_other ref_after","value":"Please send a thorough written report when the consultation is complete.","name":"ref_referral_5","caption":"Receive thorough written report"}]}',
				'group' => 'referral',
				'sex' => 'm'
			);
			$template_array[] = array(
				'category' => 'referral',
				'json' => '{"html":[{"type":"hidden","class":"ref_hidden","value":"Referral - Please provide primary physician with summaries of subsequent visits.","id":"ref_referral_hidden"},{"type":"checkbox","id":"ref_referral_1","class":"ref_other ref_intro","value":"Assume management for this particular problem and return patient after conclusion of care.","name":"ref_referral_1","caption":"Return patient after managing particular problem"},{"type":"br"},{"type":"checkbox","id":"ref_referral_2","class":"ref_other ref_intro","value":"Assume future management of patient within your area of expertise.","name":"ref_referral_2","caption":"Future ongoing management"},{"type":"br"},{"type":"checkbox","id":"ref_referral_3","class":"ref_other ref_after","value":"Please call me when you have seen the patient.","name":"ref_referral_3","caption":"Call back"},{"type":"br"},{"type":"checkbox","id":"ref_referral_4","class":"ref_other ref_after","value":"I would like to receive periodic status reports on this patient.","name":"ref_referral_4","caption":"Receive periodic status reports"},{"type":"br"},{"type":"checkbox","id":"ref_referral_5","class":"ref_other ref_after","value":"Please send a thorough written report when the consultation is complete.","name":"ref_referral_5","caption":"Receive thorough written report"}]}',
				'group' => 'referral',
				'sex' => 'f'
			);
			$template_array[] = array(
				'category' => 'referral',
				'json' => '{"html":[{"type":"hidden","class":"ref_hidden","value":"Consultation - Please send the patient back for follow-up and treatment.","id":"ref_consultation_hidden"},{"type":"checkbox","id":"ref_consultation_1","class":"ref_other ref_intro","value":"Confirm the diagnosis.","name":"ref_consultation_1","caption":"Confirm the diagnosis"},{"type":"br"},{"type":"checkbox","id":"ref_consultation_2","class":"ref_other ref_intro","value":"Advise as to the diagnosis.","name":"ref_consultation_2","caption":"Advise as to the diagnosis"},{"type":"br"},{"type":"checkbox","id":"ref_consultation_3","class":"ref_other ref_intro","value":"Suggest medication or treatment for the diagnosis.","name":"ref_consultation_3","caption":"Suggest medication or treatment"},{"type":"br"},{"type":"checkbox","id":"ref_consultation_4","class":"ref_other ref_after","value":"Please call me when you have seen the patient.","name":"ref_consultation_4","caption":"Call back"},{"type":"br"},{"type":"checkbox","id":"ref_consultation_5","class":"ref_other ref_after","value":"I would like to receive periodic status reports on this patient.","name":"ref_consultation_5","caption":"Receive periodic status reports"},{"type":"br"},{"type":"checkbox","id":"ref_consultation_6","class":"ref_other ref_after","value":"Please send a thorough written report when the consultation is complete.","name":"ref_consultation_6","caption":"Receive thorough written report"}]}',
				'group' => 'consultation',
				'sex' => 'm'
			);
			$template_array[] = array(
				'category' => 'referral',
				'json' => '{"html":[{"type":"hidden","class":"ref_hidden","value":"Consultation - Please send the patient back for follow-up and treatment.","id":"ref_consultation_hidden"},{"type":"checkbox","id":"ref_consultation_1","class":"ref_other ref_intro","value":"Confirm the diagnosis.","name":"ref_consultation_1","caption":"Confirm the diagnosis"},{"type":"br"},{"type":"checkbox","id":"ref_consultation_2","class":"ref_other ref_intro","value":"Advise as to the diagnosis.","name":"ref_consultation_2","caption":"Advise as to the diagnosis"},{"type":"br"},{"type":"checkbox","id":"ref_consultation_3","class":"ref_other ref_intro","value":"Suggest medication or treatment for the diagnosis.","name":"ref_consultation_3","caption":"Suggest medication or treatment"},{"type":"br"},{"type":"checkbox","id":"ref_consultation_4","class":"ref_other ref_after","value":"Please call me when you have seen the patient.","name":"ref_consultation_4","caption":"Call back"},{"type":"br"},{"type":"checkbox","id":"ref_consultation_5","class":"ref_other ref_after","value":"I would like to receive periodic status reports on this patient.","name":"ref_consultation_5","caption":"Receive periodic status reports"},{"type":"br"},{"type":"checkbox","id":"ref_consultation_6","class":"ref_other ref_after","value":"Please send a thorough written report when the consultation is complete.","name":"ref_consultation_6","caption":"Receive thorough written report"}]}',
				'group' => 'consultation',
				'sex' => 'f'
			);
			$template_array[] = array(
				'category' => 'referral',
				'json' => '{"html":[{"type":"hidden","class":"ref_hidden","value":"Physical therapy referral details:","id":"ref_pt_hidden"},{"type":"div","class":"ref_buttonset","id":"ref_pt_1_div","html":[{"type":"span","html":"Objectives:"},{"type":"br"},{"type":"checkbox","id":"ref_pt_1a","class":"ref_other ref_intro","value":"Decrease pain.","name":"ref_pt_1","caption":"Decrease pain"},{"type":"checkbox","id":"ref_pt_1b","class":"ref_other ref_intro","value":"Increase strength.","name":"ref_pt_1","caption":"Increase strength"},{"type":"checkbox","id":"ref_pt_1c","class":"ref_other ref_intro","value":"Increase mobility.","name":"ref_pt_1","caption":"Increase mobility"}]},{"type":"br"},{"type":"div","class":"ref_buttonset","id":"ref_pt_2_div","html":[{"type":"span","html":"Modalities:"},{"type":"br"},{"type":"select","multiple":"multiple","id":"ref_pt_2","class":"ref_select ref_intro","css":{"width":"200px"},"name":"ref_pt_2","caption":"","options":{"Hot or cold packs. ":"Hot or cold packs.","TENS unit. ":"TENS unit.","Back program. ":"Back program.","Joint mobilization. ":"Joint mobilization.","Home program. ":"Home program.","Pool therapy. ":"Pool therapy.","Feldenkrais method. ":"Feldenkrais method.","Therapeutic exercise. ":"Therapeutic exercise.","Myofascial release. ":"Myofascial release.","Patient education. ":"Patient education.","Work hardening. ":"Work hardening."}}]},{"type":"br"},{"type":"text","id":"ref_pt_3","css":{"width":"200px"},"class":"ref_other ref_detail_text ref_intro","name":"ref_pt_3","placeholder":"Precautions"},{"type":"br"},{"type":"text","id":"ref_pt_4","css":{"width":"200px"},"class":"ref_other ref_detail_text ref_intro","name":"ref_pt_4","placeholder":"Frequency"},{"type":"br"},{"type":"text","id":"ref_pt_5","css":{"width":"200px"},"class":"ref_other ref_detail_text ref_intro","name":"ref_pt_5","placeholder":"Duration"}]}',
				'group' => 'pt',
				'sex' => 'm'
			);
			$template_array[] = array(
				'category' => 'referral',
				'json' => '{"html":[{"type":"hidden","class":"ref_hidden","value":"Physical therapy referral details:","id":"ref_pt_hidden"},{"type":"div","class":"ref_buttonset","id":"ref_pt_1_div","html":[{"type":"span","html":"Objectives:"},{"type":"br"},{"type":"checkbox","id":"ref_pt_1a","class":"ref_other ref_intro","value":"Decrease pain.","name":"ref_pt_1","caption":"Decrease pain"},{"type":"checkbox","id":"ref_pt_1b","class":"ref_other ref_intro","value":"Increase strength.","name":"ref_pt_1","caption":"Increase strength"},{"type":"checkbox","id":"ref_pt_1c","class":"ref_other ref_intro","value":"Increase mobility.","name":"ref_pt_1","caption":"Increase mobility"}]},{"type":"br"},{"type":"div","class":"ref_buttonset","id":"ref_pt_2_div","html":[{"type":"span","html":"Modalities:"},{"type":"br"},{"type":"select","multiple":"multiple","id":"ref_pt_2","class":"ref_select ref_intro","css":{"width":"200px"},"name":"ref_pt_2","caption":"","options":{"Hot or cold packs. ":"Hot or cold packs.","TENS unit. ":"TENS unit.","Back program. ":"Back program.","Joint mobilization. ":"Joint mobilization.","Home program. ":"Home program.","Pool therapy. ":"Pool therapy.","Feldenkrais method. ":"Feldenkrais method.","Therapeutic exercise. ":"Therapeutic exercise.","Myofascial release. ":"Myofascial release.","Patient education. ":"Patient education.","Work hardening. ":"Work hardening."}}]},{"type":"br"},{"type":"text","id":"ref_pt_3","css":{"width":"200px"},"class":"ref_other ref_detail_text ref_intro","name":"ref_pt_3","placeholder":"Precautions"},{"type":"br"},{"type":"text","id":"ref_pt_4","css":{"width":"200px"},"class":"ref_other ref_detail_text ref_intro","name":"ref_pt_4","placeholder":"Frequency"},{"type":"br"},{"type":"text","id":"ref_pt_5","css":{"width":"200px"},"class":"ref_other ref_detail_text ref_intro","name":"ref_pt_5","placeholder":"Duration"}]}',
				'group' => 'pt',
				'sex' => 'f'
			);
			$template_array[] = array(
				'category' => 'referral',
				'json' => '{"html":[{"type":"hidden","class":"ref_hidden","value":"Massage therapy referral details:","id":"ref_massage_hidden"},{"type":"div","class":"ref_buttonset","id":"ref_massage_1_div","html":[{"type":"span","html":"Objectives:"},{"type":"br"},{"type":"checkbox","id":"ref_massage_1a","class":"ref_other ref_intro","value":"Decrease pain.","name":"ref_massage_1","caption":"Decrease pain"},{"type":"checkbox","id":"ref_massage_1b","class":"ref_other ref_intro","value":"Increase mobility.","name":"ref_massage_1","caption":"Increase mobility"}]},{"type":"br"},{"type":"text","id":"ref_massage_2","css":{"width":"200px"},"class":"ref_other ref_detail_text ref_intro","name":"ref_massage_2","placeholder":"Precautions"},{"type":"br"},{"type":"text","id":"ref_massage_3","css":{"width":"200px"},"class":"ref_other ref_detail_text ref_intro","name":"ref_massage_3","placeholder":"Frequency"},{"type":"br"},{"type":"text","id":"ref_massage_4","css":{"width":"200px"},"class":"ref_other ref_detail_text ref_intro","name":"ref_massage_4","placeholder":"Duration"}]}',
				'group' => 'massage',
				'sex' => 'm'
			);
			$template_array[] = array(
				'category' => 'referral',
				'json' => '{"html":[{"type":"hidden","class":"ref_hidden","value":"Massage therapy referral details:","id":"ref_massage_hidden"},{"type":"div","class":"ref_buttonset","id":"ref_massage_1_div","html":[{"type":"span","html":"Objectives:"},{"type":"br"},{"type":"checkbox","id":"ref_massage_1a","class":"ref_other ref_intro","value":"Decrease pain.","name":"ref_massage_1","caption":"Decrease pain"},{"type":"checkbox","id":"ref_massage_1b","class":"ref_other ref_intro","value":"Increase mobility.","name":"ref_massage_1","caption":"Increase mobility"}]},{"type":"br"},{"type":"text","id":"ref_massage_2","css":{"width":"200px"},"class":"ref_other ref_detail_text ref_intro","name":"ref_massage_2","placeholder":"Precautions"},{"type":"br"},{"type":"text","id":"ref_massage_3","css":{"width":"200px"},"class":"ref_other ref_detail_text ref_intro","name":"ref_massage_3","placeholder":"Frequency"},{"type":"br"},{"type":"text","id":"ref_massage_4","css":{"width":"200px"},"class":"ref_other ref_detail_text ref_intro","name":"ref_massage_4","placeholder":"Duration"}]}',
				'group' => 'massage',
				'sex' => 'f'
			);
			$template_array[] = array(
				'category' => 'referral',
				'json' => '{"html":[{"type":"hidden","class":"ref_hidden","value":"Sleep study referral details:","id":"ref_sleep_study_hidden"},{"type":"div","class":"ref_buttonset","id":"ref_sleep_study_1_div","html":[{"type":"span","html":"Type:"},{"type":"br"},{"type":"select","multiple":"multiple","id":"ref_sleep_study_1","class":"ref_select ref_other ref_intro","css":{"width":"200px"},"name":"ref_sleep_study_1","caption":"","options":{"Diagnostic Sleep Study Only.\n":"Diagnostic Sleep Study Only.","Diagnostic testing with Continuous Positive Airway Pressure.\n":"Diagnostic testing with Continuous Positive Airway Pressure.","Diagnostic testing with BiLevel Positive Airway Pressure.\n":"Diagnostic testing with BiLevel Positive Airway Pressure.","Diagnostic testing with BiLevel Positive Airway Pressure.\n":"Diagnostic testing with BiLevel Positive Airway Pressure.","Diagnostic testing with Oxygen.\n":"Diagnostic testing with Oxygen.","Diagnostic testing with Oral Device.\n":"Diagnostic testing with Oral Device.","MSLT (Multiple Sleep Latency Test).\n":"MSLT (Multiple Sleep Latency Test).","MWT (Maintenance of Wakefulness Test).\n":"MWT (Maintenance of Wakefulness Test).","Titrate BiPAP settings.\n":"Titrate BiPAP settings.","Patient education. ":"Patient education.","Work hardening. ":"Work hardening."}}]},{"type":"br"},{"type":"div","class":"ref_buttonset","id":"ref_sleep_study_2_div","html":[{"type":"span","html":"BiPAP pressures:"},{"type":"br"},{"type":"text","id":"ref_sleep_study_2a","css":{"width":"200px"},"class":"ref_other ref_detail_text ref_intro","name":"ref_sleep_study_2a","placeholder":"Inspiratory Pressure (IPAP), cm H20"},{"type":"br"},{"type":"text","id":"ref_sleep_study_2b","css":{"width":"200px"},"class":"ref_other ref_detail_text ref_intro","name":"ref_sleep_study_2b","placeholder":"Expiratory Pressure (EPAP), cm H20"}]},{"type":"br"},{"type":"div","class":"ref_buttonset","id":"ref_sleep_study_3_div","html":[{"type":"span","html":"BiPAP Mode:"},{"type":"br"},{"type":"checkbox","id":"ref_sleep_study_3a","class":"ref_other ref_intro","value":"Spontaneous mode.","name":"ref_sleep_study_3","caption":"Spontaneous"},{"type":"checkbox","id":"ref_sleep_study_3b","class":"ref_other ref_intro","value":"Spontaneous/Timed mode","name":"ref_sleep_study_3","caption":"Spontaneous/Timed"},{"type":"br"},{"type":"text","id":"ref_sleep_study_3c","css":{"width":"200px"},"class":"ref_other ref_detail_text ref_intro","name":"ref_sleep_study_3","placeholder":"Breaths per minute"}]}]}',
				'group' => 'sleep_study',
				'sex' => 'm'
			);
			$template_array[] = array(
				'category' => 'referral',
				'json' => '{"html":[{"type":"hidden","class":"ref_hidden","value":"Sleep study referral details:","id":"ref_sleep_study_hidden"},{"type":"div","class":"ref_buttonset","id":"ref_sleep_study_1_div","html":[{"type":"span","html":"Type:"},{"type":"br"},{"type":"select","multiple":"multiple","id":"ref_sleep_study_1","class":"ref_select ref_other ref_intro","css":{"width":"200px"},"name":"ref_sleep_study_1","caption":"","options":{"Diagnostic Sleep Study Only.\n":"Diagnostic Sleep Study Only.","Diagnostic testing with Continuous Positive Airway Pressure.\n":"Diagnostic testing with Continuous Positive Airway Pressure.","Diagnostic testing with BiLevel Positive Airway Pressure.\n":"Diagnostic testing with BiLevel Positive Airway Pressure.","Diagnostic testing with BiLevel Positive Airway Pressure.\n":"Diagnostic testing with BiLevel Positive Airway Pressure.","Diagnostic testing with Oxygen.\n":"Diagnostic testing with Oxygen.","Diagnostic testing with Oral Device.\n":"Diagnostic testing with Oral Device.","MSLT (Multiple Sleep Latency Test).\n":"MSLT (Multiple Sleep Latency Test).","MWT (Maintenance of Wakefulness Test).\n":"MWT (Maintenance of Wakefulness Test).","Titrate BiPAP settings.\n":"Titrate BiPAP settings.","Patient education. ":"Patient education.","Work hardening. ":"Work hardening."}}]},{"type":"br"},{"type":"div","class":"ref_buttonset","id":"ref_sleep_study_2_div","html":[{"type":"span","html":"BiPAP pressures:"},{"type":"br"},{"type":"text","id":"ref_sleep_study_2a","css":{"width":"200px"},"class":"ref_other ref_detail_text ref_intro","name":"ref_sleep_study_2a","placeholder":"Inspiratory Pressure (IPAP), cm H20"},{"type":"br"},{"type":"text","id":"ref_sleep_study_2b","css":{"width":"200px"},"class":"ref_other ref_detail_text ref_intro","name":"ref_sleep_study_2b","placeholder":"Expiratory Pressure (EPAP), cm H20"}]},{"type":"br"},{"type":"div","class":"ref_buttonset","id":"ref_sleep_study_3_div","html":[{"type":"span","html":"BiPAP Mode:"},{"type":"br"},{"type":"checkbox","id":"ref_sleep_study_3a","class":"ref_other ref_intro","value":"Spontaneous mode.","name":"ref_sleep_study_3","caption":"Spontaneous"},{"type":"checkbox","id":"ref_sleep_study_3b","class":"ref_other ref_intro","value":"Spontaneous/Timed mode","name":"ref_sleep_study_3","caption":"Spontaneous/Timed"},{"type":"br"},{"type":"text","id":"ref_sleep_study_3c","css":{"width":"200px"},"class":"ref_other ref_detail_text ref_intro","name":"ref_sleep_study_3","placeholder":"Breaths per minute"}]}]}',
				'group' => 'sleep_study',
				'sex' => 'f'
			);
			foreach ($template_array as $template_ind) {
				$template_array = serialize(json_decode($template_ind['json']));
				$template_data = array(
					'user_id' => '0',
					'template_name' => 'Global Default',
					'default' => 'default',
					'category' => $template_ind['category'],
					'sex' => $template_ind['sex'],
					'group' => $template_ind['group'],
					'array' => $template_array
				);
				DB::table('templates')->insert($template_data);
			}
		}
		// Update image links and create scans and received faxes directories if needed
		$practices = Practiceinfo::all();
		foreach ($practices as $practice) {
			$practice->practice_logo = str_replace("/var/www/nosh/","", $practice->practice_logo);
			$practice->save();
			$scans_dir = $practice->documents_dir . 'scans/' . $practice->practice_id;
			if (! file_exists($scans_dir)) {
				mkdir($scans_dir, 0777);
			}
			$received_dir = $practice->documents_dir . 'received/' . $practice->practice_id;
			if (! file_exists($received_dir)) {
				mkdir($received_dir, 0777);
			}
		}
		$providers = Providers::all();
		foreach ($providers as $provider) {
			$provider->signature = str_replace("/var/www/nosh/","", $provider->signature);
			$provider->save();
		}
		// Assign standard encounter templates
		DB::table('encounters')->update(array('encounter_template' => 'standardmedical'));
		// Move scans and received faxes
		$scans = DB::table('scans')->get();
		if ($scans) {
			foreach ($scans as $scan) {
				$practice1 = Practiceinfo::find($scan->practice_id);
				$new_scans_dir = $practice1->documents_dir . 'scans/' . $scan->practice_id;
				$scans_data['filePath'] = str_replace('/var/www/nosh/scans', $new_scans_dir, $scan->filePath);
				rename($scan->filePath, $scans_data['filePath']);
				DB::table('scans')->where('scans_id', '=', $scan->scans_id)->update($scans_data);
			}
		}
		$received = DB::table('received')->get();
		if ($received) {
			foreach ($received as $fax) {
				$practice2 = Practiceinfo::find($fax->practice_id);
				$new_received_dir = $practice2->documents_dir . 'received/' . $fax->practice_id;
				$received_data['filePath'] = str_replace('/var/www/nosh/received', $new_received_dir, $fax->filePath);
				rename($fax->filePath, $received_data['filePath']);
				DB::table('received')->where('received_id', '=', $fax->received_id)->update($received_data);
			}
		}
		// Migrate bill_complex field to encounters
		$encounters = DB::table('encounters')->get();
		if ($encounters) {
			foreach ($encounters as $encounter) {
				$billing = DB::table('billing')
					->where('eid', '=', $encounter->eid)
					->where(function($query_array1){
						$query_array1->where('bill_complex', '!=', "")
						->orWhereNotNull('bill_complex');
					})
					->first();
				$data['bill_complex'] = '';
				if ($billing) {
					$data['bill_complex'] = $billing->bill_complex;
				}
				DB::table('encounters')->where('eid', '=', $encounter->eid)->update($data);
			}
		}
		// Update version
		DB::table('practiceinfo')->update(array('version' => '1.8.0'));
	}
	
	public function codeigniter_migrate()
	{
		$codeigniter = __DIR__."/../../.codeigniter.php";
		if (file_exists($codeigniter)) {
			include($codeigniter);
			$db_name = 'nosh';
			$db_username = $db['default']['username'];
			$db_password = $db['default']['password'];
			$connect = mysqli_connect('localhost', $db_username, $db_password);
			if ($connect) {
				$database_filename = __DIR__."/../../.env.php";
				$database_config['mysql_database'] = $db_name;
				$database_config['mysql_username'] = $db_username;
				$database_config['mysql_password'] = $db_password;
				file_put_contents($database_filename, '<?php return ' . var_export($database_config, true) . ";\n");
				mysqli_close($connect);
			} else {
				echo "Incorrect username/password for your MySQL database.  Try again.";
				exit (0);
			}
		}
		return Redirect::to('/');
	}
}