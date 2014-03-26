<div id="menucontainer">
	<div id="menu_accordion">
		<h3><?php echo HTML::image('images/chart2.png', 'Chart', array('border' => '0', 'height' => '20', 'width' => '20', 'style' => 'vertical-align:middle;')); ?> Chart for <span id="menu_ptname"></span> (ID: <?php echo $id;?>)</h3>
		<div id="menu_accordion_chart">
			<strong>Nickname:</strong> <span id="menu_nickname"></span><br>
			<strong>Date of Birth:</strong> <span id="menu_dob"></span><br>
			<strong>Age:</strong> <span id="menu_age"></span><br>
			<strong>Gender:</strong> <span id="menu_gender1"></span><br>
			<strong>Last Encounter:</strong> <?php echo $lastvisit;?><br>
			<strong>Next Appointment:</strong> <?php echo $nextvisit;?><br><br>
			<div class="pure-g" style="font-size:1.1em">
				<div class="pure-u-1-2"><?php echo HTML::image('images/chart2.png', 'Encounters', array('border' => '0', 'height' => '20', 'width' => '20', 'style' => 'vertical-align:middle;')); ?> <a href="#" id="encounter_list" title="Past encounters" class="nosh_tooltip">Encounters</a></div>
				<div class="pure-u-1-2"><?php echo HTML::image('images/newmessage.png', 'Messages', array('border' => '0', 'height' => '20', 'width' => '20', 'style' => 'vertical-align:middle;')); ?> <a href="#" id="messages_list" title="Telephone messages and e-mail encounters" class="nosh_tooltip">Messages</a></div>
				<div class="pure-u-1-2"><?php echo HTML::image('images/search.png', 'Documents', array('border' => '0', 'height' => '20', 'width' => '20', 'style' => 'vertical-align:middle;')); ?> <a href="#" id="documents_list" title="Lab, imaging, cardiopulmonary, referral, and other associated documents for this patient." class="nosh_tooltip">Documents</a></div>
				<div class="pure-u-1-2"><?php echo HTML::image('images/billing.png', 'Billing', array('border' => '0', 'height' => '20', 'width' => '20', 'style' => 'vertical-align:middle;')); ?> <a href="#" id="billing_list" title="Past claims, payments, and balances for this patient" class="nosh_tooltip">Billing</a></div>
				<div class="pure-u-1-2"><?php echo HTML::image('images/printmgr.png', 'Send Records', array('border' => '0', 'height' => '20', 'width' => '20', 'style' => 'vertical-align:middle;')); ?> <a href="#" id="print_list" title="Print, fax, and create C-CDA documents of this patient's records." class="nosh_tooltip">Send Records</a></div>
				<div class="pure-u-1-2"><?php echo HTML::image('images/prevent.png', 'Prevention', array('border' => '0', 'height' => '20', 'width' => '20', 'style' => 'vertical-align:middle;')); ?> <a href="#" id="prevention_list" title="View prevention recommendations from U.S. Preventatitve Services Task Force and the CDC" class="nosh_tooltip">Prevention</a></div>
				<?php if($mtm == 'y') {?>
					<div class="pure-u-1-2"><?php echo HTML::image('images/graph.png', 'MTM', array('border' => '0', 'height' => '20', 'width' => '20', 'style' => 'vertical-align:middle;')); ?> <a href="#" id="mtm_list" title="Medication Therapy Management" class="nosh_tooltip">MTM</a></div>
				<?php }?>
				<?php if (Session::get('agealldays') <6574.5) {?>
					<div class="pure-u-1-2"><?php echo HTML::image('images/plot.png', 'Growth Charts', array('border' => '0', 'height' => '20', 'width' => '20', 'style' => 'vertical-align:middle;')); ?> <a href="#" id="gc_list" title="Growth Charts" class="nosh_tooltip">Growth Charts</a></div>
				<?php }?>
			</div>
		</div>
		<h3><button class="demographics_list nosh_button_edit">Edit</button><a href="#"><?php echo HTML::image('images/personal.png', 'Demographics', array('border' => '0', 'height' => '20', 'width' => '20', 'style' => 'vertical-align:middle;')); ?> Demographics</a></h3>
		<div id="menu_accordion_demographics-list">
			<a href="#" class="demographics_list">Edit and View Details</a><br>
			<div id="menu_accordion_demographics-list_load"><?php echo HTML::image('images/indicator.gif', 'Loading', array('border' => '0', 'style' => 'vertical-align:middle;')); ?> Loading...</div>
			<div id="menu_accordion_demographics-list_content" class="menu_accordion_content"></div>
		</div>
		<h3><button class="issues_list nosh_button_edit">Edit</button><a href="#"><?php echo HTML::image('images/chart.png', 'Issues', array('border' => '0', 'height' => '20', 'width' => '20', 'style' => 'vertical-align:middle;')); ?> Issues</a></h3>
		<div id="menu_accordion_issues-list">
			<a href="#" class="issues_list">Edit and View Details</a><br>
			<div id="menu_accordion_issues-list_load"><?php echo HTML::image('images/indicator.gif', 'Loading', array('border' => '0', 'style' => 'vertical-align:middle;')); ?> Loading...</div>
			<div id="menu_accordion_issues-list_content" class="menu_accordion_content"></div>
		</div>
		<h3><button class="medications_list nosh_button_edit">Edit</button><a href="#"><?php echo HTML::image('images/rx.png', 'Medications', array('border' => '0', 'height' => '20', 'width' => '20', 'style' => 'vertical-align:middle;')); ?> Medications</a></h3>
		<div id="menu_accordion_medications-list">
			<a href="#" class="medications_list">Edit and View Details</a><br>
			<div id="menu_accordion_medications-list_load"><?php echo HTML::image('images/indicator.gif', 'Loading', array('border' => '0', 'style' => 'vertical-align:middle;')); ?> Loading...</div>
			<div id="menu_accordion_medications-list_content" class="menu_accordion_content"></div>
		</div>
		<h3><button class="supplements_list nosh_button_edit">Edit</button><a href="#"><?php echo HTML::image('images/supplements.png', 'Supplements', array('border' => '0', 'height' => '20', 'width' => '20', 'style' => 'vertical-align:middle;')); ?> Supplements</a></h3>
		<div id="menu_accordion_supplements-list">
			<a href="#" class="supplements_list">Edit and View Details</a><br>
			<div id="menu_accordion_supplements-list_load"><?php echo HTML::image('images/indicator.gif', 'Loading', array('border' => '0', 'style' => 'vertical-align:middle;')); ?> Loading...</div>
			<div id="menu_accordion_supplements-list_content" class="menu_accordion_content"></div>
		</div>
		<h3><button class="immunizations_list nosh_button_edit">Edit</button><a href="#"><?php echo HTML::image('images/immunization.png', 'Immunizations', array('border' => '0', 'height' => '20', 'width' => '20', 'style' => 'vertical-align:middle;')); ?> Immunizations</a></h3>
		<div id="menu_accordion_immunizations-list">
			<a href="#" class="immunizations_list">Edit and View Details</a><br>
			<div id="menu_accordion_immunizations-list_load"><?php echo HTML::image('images/indicator.gif', 'Loading', array('border' => '0', 'style' => 'vertical-align:middle;')); ?> Loading...</div>
			<div id="menu_accordion_immunizations-list_content" class="menu_accordion_content"></div>
		</div>
		<h3><button class="allergies_list nosh_button_edit">Edit</button><a href="#"><?php echo HTML::image('images/important.png', 'Allergies', array('border' => '0', 'height' => '20', 'width' => '20', 'style' => 'vertical-align:middle;')); ?> Allergies</a></h3>
		<div id="menu_accordion_allergies-list">
			<a href="#" class="allergies_list">Edit and View Details</a><br>
			<div id="menu_accordion_allergies-list_load"><?php echo HTML::image('images/indicator.gif', 'Loading', array('border' => '0', 'style' => 'vertical-align:middle;')); ?> Loading...</div>
			<div id="menu_accordion_allergies-list_content" class="menu_accordion_content"></div>
		</div>
		<h3><button class="alerts_list nosh_button_edit">Edit</button><a href="#"><?php echo HTML::image('images/alert.png', 'Alerts', array('border' => '0', 'height' => '20', 'width' => '20', 'style' => 'vertical-align:middle;')); ?> Alerts</a></h3>
		<div id="menu_accordion_alerts-list">
			<a href="#" class="alerts_list">Edit and View Details</a><br>
			<div id="menu_accordion_alerts-list_load"><?php echo HTML::image('images/indicator.gif', 'Loading', array('border' => '0', 'style' => 'vertical-align:middle;')); ?> Loading...</div>
			<div id="menu_accordion_alerts-list_content" class="menu_accordion_content"></div>
		</div>
	</div>
</div>