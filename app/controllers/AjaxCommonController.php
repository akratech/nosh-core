<?php

class AjaxCommonController extends BaseController {

	/**
	* NOSH ChartingSystem Common Chart Ajax public functions
	*/
	
	public function postEncounters()
	{
		$practice_id = Session::get('practice_id');
		$pid = Session::get('pid');
		$page = Input::get('page');
		$limit = Input::get('rows');
		$sidx = Input::get('sidx');
		$sord = Input::get('sord');
		$query = DB::table('encounters')->where('pid', '=', $pid)
			->where('addendum', '=', 'n')
			->where('practice_id', '=', $practice_id);
		if (Session::get('group_id') == '100') {
			$query->where('encounter_signed', '=', 'Yes');
		}
		$result = $query->get();
		if($result) { 
			$count = count($result);
			$total_pages = ceil($count/$limit); 
		} else { 
			$count = 0;
			$total_pages = 0;
		}
		if ($page > $total_pages) $page=$total_pages;
		$start = $limit*$page - $limit;
		if($start < 0) $start = 0;
		$query->orderBy($sidx, $sord)
			->skip($start)
			->take($limit);
		$query1 = $query->get();
		$response['page'] = $page;
		$response['total'] = $total_pages;
		$response['records'] = $count;
		if ($query1) {
			$response['rows'] = $query1;
		} else {
			$response['rows'] = '';
		}
		echo json_encode($response);
	}
	
	public function postIssues()
	{
		$pid = Session::get('pid');
		$page = Input::get('page');
		$limit = Input::get('rows');
		$sidx = Input::get('sidx');
		$sord = Input::get('sord');
		$query = DB::table('issues')
			->where('pid', '=', $pid)
			->where('issue_date_inactive', '=', '0000-00-00 00:00:00')
			->get();
		if($query) { 
			$count = count($query);
			$total_pages = ceil($count/$limit); 
		} else { 
			$count = 0;
			$total_pages = 0;
		}
		if ($page > $total_pages) $page=$total_pages;
		$start = $limit*$page - $limit; 
		if($start < 0) $start = 0;
		$query1 = DB::table('issues')
			->where('pid', '=', $pid)
			->where('issue_date_inactive', '=', '0000-00-00 00:00:00')
			->orderBy($sidx, $sord)
			->skip($start)
			->take($limit)
			->get();
		$response['page'] = $page;
		$response['total'] = $total_pages;
		$response['records'] = $count;
		if ($query1) {
			$response['rows'] = $query1;
		} else {
			$response['rows'] = '';
		}
		echo json_encode($response);
	}
	
	public function postIssuesInactive()
	{
		$pid = Session::get('pid');
		$page = Input::get('page');
		$limit = Input::get('rows');
		$sidx = Input::get('sidx');
		$sord = Input::get('sord');
		$query = DB::table('issues')
			->where('pid', '=', $pid)
			->where('issue_date_inactive', '!=', '0000-00-00 00:00:00')
			->get();
		if($query) { 
			$count = count($query);
			$total_pages = ceil($count/$limit); 
		} else { 
			$count = 0;
			$total_pages = 0;
		}
		if ($page > $total_pages) $page=$total_pages;
		$start = $limit*$page - $limit; 
		if($start < 0) $start = 0;
		$query1 = DB::table('issues')
			->where('pid', '=', $pid)
			->where('issue_date_inactive', '!=', '0000-00-00 00:00:00')
			->orderBy($sidx, $sord)
			->skip($start)
			->take($limit)
			->get();
		$response['page'] = $page;
		$response['total'] = $total_pages;
		$response['records'] = $count;
		if ($query1) {
			$response['rows'] = $query1;
		} else {
			$response['rows'] = '';
		}
		echo json_encode($response);
	}
	
	public function postMedications()
	{
		$pid = Session::get('pid');
		$page = Input::get('page');
		$limit = Input::get('rows');
		$sidx = Input::get('sidx');
		$sord = Input::get('sord');
		$query = DB::table('rx_list')
			->where('pid', '=', $pid)
			->where('rxl_date_inactive', '=', '0000-00-00 00:00:00')
			->where('rxl_date_old', '=', '0000-00-00 00:00:00')
			->get();
		if($query) { 
			$count = count($query);
			$total_pages = ceil($count/$limit); 
		} else { 
			$count = 0;
			$total_pages = 0;
		}
		if ($page > $total_pages) $page=$total_pages;
		$start = $limit*$page - $limit;
		if($start < 0) $start = 0;
		$query1 = DB::table('rx_list')
			->where('pid', '=', $pid)
			->where('rxl_date_inactive', '=', '0000-00-00 00:00:00')
			->where('rxl_date_old', '=', '0000-00-00 00:00:00')
			->orderBy($sidx, $sord)
			->skip($start)
			->take($limit)
			->get();
		$response['page'] = $page;
		$response['total'] = $total_pages;
		$response['records'] = $count;
		if ($query1) {
			$response['rows'] = $query1;
		} else {
			$response['rows'] = '';
		}
		echo json_encode($response);
	}
	
	public function postMedicationsInactive()
	{
		$pid = Session::get('pid');
		$page = Input::get('page');
		$limit = Input::get('rows');
		$sidx = Input::get('sidx');
		$sord = Input::get('sord');
		$query = DB::table('rx_list')
			->where('pid', '=', $pid)
			->where('rxl_date_inactive', '!=', '0000-00-00 00:00:00')
			->get();
		if($query) { 
			$count = count($query);
			$total_pages = ceil($count/$limit); 
		} else { 
			$count = 0;
			$total_pages = 0;
		}
		if ($page > $total_pages) $page=$total_pages;
		$start = $limit*$page - $limit;
		if($start < 0) $start = 0;
		$query1 = DB::table('rx_list')
			->where('pid', '=', $pid)
			->where('rxl_date_inactive', '!=', '0000-00-00 00:00:00')
			->orderBy($sidx, $sord)
			->skip($start)
			->take($limit)
			->get();
		$response['page'] = $page;
		$response['total'] = $total_pages;
		$response['records'] = $count;
		if ($query1) {
			$response['rows'] = $query1;
		} else {
			$response['rows'] = '';
		}
		echo json_encode($response);
	}
	
	public function postSupplements()
	{
		$pid = Session::get('pid');
		$page = Input::get('page');
		$limit = Input::get('rows');
		$sidx = Input::get('sidx');
		$sord = Input::get('sord');
		$query = DB::table('sup_list')
			->where('pid', '=', $pid)
			->where('sup_date_inactive', '=', '0000-00-00 00:00:00')
			->get();
		if($query) { 
			$count = count($query);
			$total_pages = ceil($count/$limit); 
		} else { 
			$count = 0;
			$total_pages = 0;
		}
		if ($page > $total_pages) $page=$total_pages;
		$start = $limit*$page - $limit;
		if($start < 0) $start = 0;
		$query1 = DB::table('sup_list')
			->where('pid', '=', $pid)
			->where('sup_date_inactive', '=', '0000-00-00 00:00:00')
			->orderBy($sidx, $sord)
			->skip($start)
			->take($limit)
			->get();
		$response['page'] = $page;
		$response['total'] = $total_pages;
		$response['records'] = $count;
		if ($query1) {
			$response['rows'] = $query1;
		} else {
			$response['rows'] = '';
		}
		echo json_encode($response);
	}
	
	public function postSupplementsInactive()
	{
		$pid = Session::get('pid');
		$page = Input::get('page');
		$limit = Input::get('rows');
		$sidx = Input::get('sidx');
		$sord = Input::get('sord');
		$query = DB::table('sup_list')
			->where('pid', '=', $pid)
			->where('sup_date_inactive', '!=', '0000-00-00 00:00:00')
			->get();
		if($query) { 
			$count = count($query);
			$total_pages = ceil($count/$limit); 
		} else { 
			$count = 0;
			$total_pages = 0;
		}
		if ($page > $total_pages) $page=$total_pages;
		$start = $limit*$page - $limit;
		if($start < 0) $start = 0;
		$query1 = DB::table('sup_list')
			->where('pid', '=', $pid)
			->where('sup_date_inactive', '!=', '0000-00-00 00:00:00')
			->orderBy($sidx, $sord)
			->skip($start)
			->take($limit)
			->get();
		$response['page'] = $page;
		$response['total'] = $total_pages;
		$response['records'] = $count;
		if ($query1) {
			$response['rows'] = $query1;
		} else {
			$response['rows'] = '';
		}
		echo json_encode($response);
	}
	
	public function postAllergies()
	{
		$pid = Session::get('pid');
		$page = Input::get('page');
		$limit = Input::get('rows');
		$sidx = Input::get('sidx');
		$sord = Input::get('sord');
		$query = DB::table('allergies')
			->where('pid', '=', $pid)
			->where('allergies_date_inactive', '=', '0000-00-00 00:00:00')
			->get();
		if($query) { 
			$count = count($query);
			$total_pages = ceil($count/$limit); 
		} else { 
			$count = 0;
			$total_pages = 0;
		}
		if ($page > $total_pages) $page=$total_pages;
		$start = $limit*$page - $limit;
		if($start < 0) $start = 0;
		$query1 = DB::table('allergies')
			->where('pid', '=', $pid)
			->where('allergies_date_inactive', '=', '0000-00-00 00:00:00')
			->orderBy($sidx, $sord)
			->skip($start)
			->take($limit)
			->get();
		$response['page'] = $page;
		$response['total'] = $total_pages;
		$response['records'] = $count;
		if ($query1) {
			$response['rows'] = $query1;
		} else {
			$response['rows'] = '';
		}
		echo json_encode($response);
	}
	
	public function postAllergiesInactive()
	{
		$pid = Session::get('pid');
		$page = Input::get('page');
		$limit = Input::get('rows');
		$sidx = Input::get('sidx');
		$sord = Input::get('sord');
		$query = DB::table('allergies')
			->where('pid', '=', $pid)
			->where('allergies_date_inactive', '!=', '0000-00-00 00:00:00')
			->get();
		if($query) { 
			$count = count($query);
			$total_pages = ceil($count/$limit); 
		} else { 
			$count = 0;
			$total_pages = 0;
		}
		if ($page > $total_pages) $page=$total_pages;
		$start = $limit*$page - $limit;
		if($start < 0) $start = 0;
		$query1 = DB::table('allergies')
			->where('pid', '=', $pid)
			->where('allergies_date_inactive', '!=', '0000-00-00 00:00:00')
			->orderBy($sidx, $sord)
			->skip($start)
			->take($limit)
			->get();
		$response['page'] = $page;
		$response['total'] = $total_pages;
		$response['records'] = $count;
		if ($query1) {
			$response['rows'] = $query1;
		} else {
			$response['rows'] = '';
		}
		echo json_encode($response);
	}
	
	public function postDocuments($type)
	{
		$type = str_replace('_', ' ', $type);
		$pid = Session::get('pid');
		$page = Input::get('page');
		$limit = Input::get('rows');
		$sidx = Input::get('sidx');
		$sord = Input::get('sord'); 
		$query = DB::table('documents')
			->where('pid', '=', $pid)
			->where('documents_type', '=', $type)
			->get();
		if($query) { 
			$count = count($query);
			$total_pages = ceil($count/$limit); 
		} else { 
			$count = 0;
			$total_pages = 0;
		}
		if ($page > $total_pages) $page=$total_pages;
		$start = $limit*$page - $limit;
		if($start < 0) $start = 0;
		$query1 = DB::table('documents')
			->where('pid', '=', $pid)
			->where('documents_type', '=', $type)
			->orderBy($sidx, $sord)
			->skip($start)
			->take($limit)
			->get();
		$response['page'] = $page;
		$response['total'] = $total_pages;
		$response['records'] = $count;
		if ($query1) {
			$response['rows'] = $query1;
		} else {
			$response['rows'] = '';
		}
		echo json_encode($response);
	}
	
	public function postViewDocuments1($id)
	{
		$pid = Session::get('pid');
		$result = Documents::find($id);
		$file_path = $result->documents_url;
		$data1 = array(
			'documents_viewed' => Session::get('displayname')
		);
		DB::table('documents')->where('documents_id', '=', $id)->update($data1);
		$this->audit('Update');
		$name = time() . '_' . $pid . '.pdf';
		$data['filepath'] = __DIR__.'/../../public/temp/' . $name;
		copy($file_path, $data['filepath']);
		while(!file_exists($data['filepath'])) {
			sleep(2);
		}
		$data['html'] = '<iframe src="' . asset('temp/' . $name) . '" width="770" height="425" style="border: none;"></iframe>';
		$data['id'] = $id;
		echo json_encode($data);
	}
	
	public function postCloseDocument()
	{
		unlink(Input::get('document_filepath'));
		echo 'OK';
	}
	
	public function postPatientInstructions($eid)
	{	
		$html = $this->page_plan($eid)->render();
		$name = "plan_" . time() . "_" . Session::get('user_id') . ".pdf";
		$data['filepath'] = __DIR__."/../../public/temp/" . $name;
		$this->generate_pdf($html, $data['filepath']);
		while(!file_exists($data['filepath'])) {
			sleep(2);
		}
		$data['html'] = '<iframe src="' . asset('temp/' . $name) . '" width="770" height="425" style="border: none;"></iframe>';
		echo json_encode($data);
	}
	
	public function postFormsGrid()
	{
		$pid = Session::get('pid');
		$age = Session::get('agealldays');
		$demo_row = DB::table('demographics')->where('pid', '=', $pid)->first();
		$agediff = (time() - $this->human_to_unix($demo_row->DOB))/86400;
		$pos4 = strpos($agediff, '.');
		$age1 = substr($agediff, 0, $pos4);
		$page = Input::get('page');
		$limit = Input::get('rows');
		$sidx = Input::get('sidx');
		$sord = Input::get('sord');
		$query = DB::table('templates')
			->where('category', '=', 'forms')
			->where('sex', '=', $demo_row->sex);
		if ($age1 > 6574.5) {
			$query->where('age', '!=', 'child');
		} else {
			$query->where('age', '!=', 'adult');
		}
		$result = $query->get();
		if($result) { 
			$count = count($result);
			$total_pages = ceil($count/$limit); 
		} else { 
			$count = 0;
			$total_pages = 0;
		}
		if ($page > $total_pages) $page=$total_pages;
		$start = $limit*$page - $limit;
		if($start < 0) $start = 0;
		$query->orderBy($sidx, $sord)
			->skip($start)
			->take($limit);
		$query1 = $query->get();
		$response['page'] = $page;
		$response['total'] = $total_pages;
		$response['records'] = $count;
		if ($query1) {
			$records1 = array();
			$i = 0;
			foreach ($query1 as $records_row) {
				$records1[$i]['template_id'] = $records_row->template_id;
				$records1[$i]['template_name'] = $records_row->template_name;
				$row2 = DB::table('forms')
					->where('template_id', '=', $records_row->template_id)
					->where('pid', '=', $pid)
					->orderBy('forms_date', 'desc')
					->take(1)
					->first();
				if ($row2) {
					if ($records_row->array == $row2->array) {
						$records1[$i]['forms_id'] = $row2->forms_id;
						$records1[$i]['forms_date'] = $row2->forms_date;
					} else {
						$records1[$i]['forms_id'] = '';
						$records1[$i]['forms_date'] = '';
					}
				} else {
					$records1[$i]['forms_id'] = '';
					$records1[$i]['forms_date'] = '';
				}
				$i++;
			}
			$response['rows'] = $records1;
		} else {
			$response['rows'] = '';
		}
		echo json_encode($response);
	}
	
	public function postGetForm($id)
	{
		$row = DB::table('templates')->where('template_id', '=', $id)->first();
		$data = unserialize($row->array);
		echo $data;
	}
	
	public function postGetFormData($id)
	{
		$row = DB::table('forms')->where('forms_id', '=', $id)->first();
		$data = unserialize($row->forms_content);
		echo $data;
	}
	
	public function postSaveFormData()
	{
		$data = array(
			'pid' => Session::get('pid'),
			'template_id' => Input::get('template_id'),
			'forms_title' => Input::get('forms_title'),
			'forms_content' => serialize(Input::get('forms_content')),
			'forms_destination' => Input::get('forms_destination'),
			'forms_content_text' => Input::get('forms_content_text'),
			'array' => serialize(Input::get('array'))
		);
		if (Input::get('forms_id') != '') {
			DB::table('forms')->where('forms_id', '=', Input::get('forms_id'))->update($data);
			$this->audit('Update');
		} else {
			DB::table('forms')->insert($data);
			$this->audit('Add');
		}
		echo "Form saved and submitted to your provider!";
	}
	
	public function postGrowthChart($style)
	{
		$pid = Session::get('pid');
		$displayname = Session::get('displayname');
		$demographics = Demographics::find($pid);
		$gender = Session::get('gender');
		$time = time();
		$dob = $this->human_to_unix($demographics->DOB);
		$pedsage = ($time - $dob);
		$datenow = date(DATE_RFC822, $time);
		$date = date("Y-m-d", $time);
		$data = array();
		$data['patientname'] = $demographics->firstname . ' ' . $demographics->lastname;
		if ($style == 'bmi-age') {
			$data['patient'] = $this->getBMIChart($pid);
			$data['yaxis'] = 'kg/m2';
			if ($gender == 'male') {
				$sex = 'm';
			} else {
				$sex = 'f';
			}
			$array = $this->getSpline($style, $sex);
			usort($array, array("AjaxCommonController", "cmp"));
			foreach ($array as $row) {
				$data['categories'][] = (float) $row['Age'];
				$data['P5'][] = (float) $row['P5'];
				$data['P10'][] = (float) $row['P10'];
				$data['P25'][] = (float) $row['P25'];
				$data['P50'][] = (float) $row['P50'];
				$data['P75'][] = (float) $row['P75'];
				$data['P90'][] = (float) $row['P90'];
				$data['P95'][] = (float) $row['P95'];
			}
			$data['xaxis'] = 'Age (days)';
			$data['title'] = 'BMI-for-age percentiles for ' . $demographics->firstname . ' ' . $demographics->lastname . ' as of ' . $datenow;
			$val = end($data['patient']);
			$age = round($val[0]);
			$x = $val[1];
			$lms = $this->getLMS($style, $sex, $age);
			$l = $lms['L'];
			$m = $lms['M'];
			$s = $lms['S'];
			$val1 = $x / $m;
			if ($lms['L'] != '0') {
				$val2 = pow($val1, $l);
				$val2 = $val2 - 1;
				$val3 = $l * $s;
				$zscore = $val2 / $val3;
			} else {
				$val4 = log($val1);
				$zscore = $val4 / $s;
			}
			$percentile = $this->cdf($zscore) * 100;
			$percentile = round($percentile);
			$data['percentile'] = strval($percentile);
			echo json_encode($data);
			exit (0);
		}
		if ($style == 'weight-age') {
			$data['patient'] = $this->getWeightChart($pid);
			$data['yaxis'] = 'kg';
			if ($gender == 'male') {
				$sex = 'm';
			} else {
				$sex = 'f';
			}
			$array = $this->getSpline($style, $sex);
			usort($array, array("AjaxCommonController", "cmp"));
			foreach ($array as $row) {
				$data['categories'][] = (float) $row['Age'];
				$data['P5'][] = (float) $row['P5'];
				$data['P10'][] = (float) $row['P10'];
				$data['P25'][] = (float) $row['P25'];
				$data['P50'][] = (float) $row['P50'];
				$data['P75'][] = (float) $row['P75'];
				$data['P90'][] = (float) $row['P90'];
				$data['P95'][] = (float) $row['P95'];
			}
			$data['xaxis'] = 'Age (days)';
			$data['title'] = 'Weight-for-age percentiles for ' . $demographics->firstname . ' ' . $demographics->lastname . ' as of ' . $datenow;
			$val = end($data['patient']);
			$age = round($val[0]);
			$x = $val[1];
			$lms = $this->getLMS($style, $sex, $age);
			$l = $lms['L'];
			$m = $lms['M'];
			$s = $lms['S'];
			$val1 = $x / $m;
			$data['val1'] = $val1;
			if ($lms['L'] != '0') {
				$val2 = pow($val1, $l);
				$val2 = $val2 - 1;
				$val3 = $l * $s;
				$zscore = $val2 / $val3;
			} else {
				$val4 = log($val1);
				$zscore = $val4 / $s;
			}
			$percentile = $this->cdf($zscore) * 100;
			$percentile = round($percentile);
			$data['percentile'] = strval($percentile);
			echo json_encode($data);
			exit (0);
		}
		if ($style == 'height-age') {
			$data['patient'] = $this->getHeightChart($pid);
			$data['yaxis'] = 'cm';
			if ($gender == 'male') {
				$sex = 'm';
			} else {
				$sex = 'f';
			}
			$array = $this->getSpline($style, $sex);
			usort($array, array("AjaxCommonController", "cmp"));
			foreach ($array as $row) {
				$data['categories'][] = (float) $row['Age'];
				$data['P5'][] = (float) $row['P5'];
				$data['P10'][] = (float) $row['P10'];
				$data['P25'][] = (float) $row['P25'];
				$data['P50'][] = (float) $row['P50'];
				$data['P75'][] = (float) $row['P75'];
				$data['P90'][] = (float) $row['P90'];
				$data['P95'][] = (float) $row['P95'];
			}
			$data['title'] = 'Height-for-age percentiles for ' . $demographics->firstname . ' ' . $demographics->lastname . ' as of ' . $datenow;
			$val = end($data['patient']);
			$age = round($val[0]);
			$x = $val[1];
			$lms = $this->getLMS($style, $sex, $age);
			$l = $lms['L'];
			$m = $lms['M'];
			$s = $lms['S'];
			$val1 = $x / $m;
			if ($lms['L'] != '0') {
				$val2 = pow($val1, $l);
				$val2 = $val2 - 1;
				$val3 = $l * $s;
				$zscore = $val2 / $val3;
			} else {
				$val4 = log($val1);
				$zscore = $val4 / $s;
			}
			$percentile = $this->cdf($zscore) * 100;
			$percentile = round($percentile);
			$data['percentile'] = strval($percentile);
			echo json_encode($data);
			exit (0);
		}
		if ($style == 'head-age') {
			$data['patient'] = $this->getHCChart($pid);
			$data['yaxis'] = 'cm';
			if ($gender == 'male') {
				$sex = 'm';
			} else {
				$sex = 'f';
			}
			$array = $this->getSpline($style, $sex);
			usort($array, array("AjaxCommonController", "cmp"));
			foreach ($array as $row) {
				$data['categories'][] = (float) $row['Age'];
				$data['P5'][] = (float) $row['P5'];
				$data['P10'][] = (float) $row['P10'];
				$data['P25'][] = (float) $row['P25'];
				$data['P50'][] = (float) $row['P50'];
				$data['P75'][] = (float) $row['P75'];
				$data['P90'][] = (float) $row['P90'];
				$data['P95'][] = (float) $row['P95'];
			}
			$data['xaxis'] = 'Age (days)';
			$data['title'] = 'Head circumference-for-age percentiles for ' . $demographics->firstname . ' ' . $demographics->lastname . ' as of ' . $datenow;
			$val = end($data['patient']);
			$age = round($val[0]);
			$x = $val[1];
			$lms = $this->getLMS($style, $sex, $age);
			$l = $lms['L'];
			$m = $lms['M'];
			$s = $lms['S'];
			$val1 = $x / $m;
			if ($lms['L'] != '0') {
				$val2 = pow($val1, $l);
				$val2 = $val2 - 1;
				$val3 = $l * $s;
				$zscore = $val2 / $val3;
			} else {
				$val4 = log($val1);
				$zscore = $val4 / $s;
			}
			$percentile = $this->cdf($zscore) * 100;
			$percentile = round($percentile);
			$data['percentile'] = strval($percentile);
			echo json_encode($data);
			exit (0);
		}
		if ($style == 'weight-height') {
			$data['patient'] = $this->getWeightHeightChart($pid);
			$data['yaxis'] = 'kg';
			$data['xaxis'] = 'cm';
			if ($gender == 'male') {
				$sex = 'm';
			} else {
				$sex = 'f';
			}
			if ($pedsage <= 63113852) {
				$array1 = $this->getSpline('weight-length', $sex);
				usort($array1, array("AjaxCommonController", "cmp1"));
				$i = 0;
				foreach ($array1 as $row1) {
					$data['P5'][$i][] = (float) $row1['Height'];
					$data['P5'][$i][] = (float) $row1['P5'];
					$data['P10'][$i][] = (float) $row1['Height'];
					$data['P10'][$i][] = (float) $row1['P10'];
					$data['P25'][$i][] = (float) $row1['Height'];
					$data['P25'][$i][] = (float) $row1['P25'];
					$data['P50'][$i][] = (float) $row1['Height'];
					$data['P50'][$i][] = (float) $row1['P50'];
					$data['P75'][$i][] = (float) $row1['Height'];
					$data['P75'][$i][] = (float) $row1['P75'];
					$data['P90'][$i][] = (float) $row1['Height'];
					$data['P90'][$i][] = (float) $row1['P90'];
					$data['P95'][$i][] = (float) $row1['Height'];
					$data['P95'][$i][] = (float) $row1['P95'];
					$i++;
				}
			} else {
				$array2 = $this->getSpline($style, $sex);
				usort($array2, array("AjaxCommonController", "cmp2"));
				$j = 0;
				foreach ($array2 as $row1) {
					$data['P5'][$j][] = (float) $row1['Height'];
					$data['P5'][$j][] = (float) $row1['P5'];
					$data['P10'][$j][] = (float) $row1['Height'];
					$data['P10'][$j][] = (float) $row1['P10'];
					$data['P25'][$j][] = (float) $row1['Height'];
					$data['P25'][$j][] = (float) $row1['P25'];
					$data['P50'][$j][] = (float) $row1['Height'];
					$data['P50'][$j][] = (float) $row1['P50'];
					$data['P75'][$j][] = (float) $row1['Height'];
					$data['P75'][$j][] = (float) $row1['P75'];
					$data['P90'][$j][] = (float) $row1['Height'];
					$data['P90'][$j][] = (float) $row1['P90'];
					$data['P95'][$j][] = (float) $row1['Height'];
					$data['P95'][$j][] = (float) $row1['P95'];
					$j++;
				}
			}
			$data['title'] = 'Weight-height for ' . $demographics->firstname . ' ' . $demographics->lastname . ' as of ' . $datenow;
			$val = end($data['patient']);
			$length = round($val[0]);
			$data['length'] = $length;
			$x = $val[1];
			if ($pedsage <= 63113852) {
				$lms = $this->getLMS1('weight-length', $sex, $length);
			} else {
				$lms = $this->getLMS2($style, $sex, $length);
			}
			$l = $lms['L'];
			$m = $lms['M'];
			$s = $lms['S'];
			$val1 = $x / $m;
			if ($lms['L'] != '0') {
				$val2 = pow($val1, $l);
				$val2 = $val2 - 1;
				$val3 = $l * $s;
				$zscore = $val2 / $val3;
			} else {
				$val4 = log($val1);
				$zscore = $val4 / $s;
			}
			$percentile = $this->cdf($zscore) * 100;
			$percentile = round($percentile);
			$data['percentile'] = strval($percentile);
			echo json_encode($data);
			exit (0);
		}
	}
	
	public function cmp($a, $b) 
	{
		return $a["Age"] - $b["Age"];
	}
	
	public function cmp1($a, $b) 
	{
		if ($a["Length"] == $b["Length"]) {
			return 0;
		}
		return ($a["Length"] < $b["Length"]) ? -1 : 1;
	}
	
	public function cmp2($a, $b) 
	{
		if ($a["Height"] == $b["Height"]) {
			return 0;
		}
		return ($a["Height"] < $b["Height"]) ? -1 : 1;
	}
	
	public function bluebutton($id)
	{
		$result = Documents::find($id);
		$file_path = $result->documents_url;
		$data['ccda'] = File::get($file_path);
		$data['js'] = File::get(__DIR__.'/../../public/js/bluebutton1.js');
		return View::make('bluebutton', $data);
	}
}