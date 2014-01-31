<?php
	# agileMantis - makes Mantis ready for Scrum

	# agileMantis is free software: you can redistribute it and/or modify
	# it under the terms of the GNU General Public License as published by
	# the Free Software Foundation, either version 2 of the License, or
	# (at your option) any later version.
	#
	# agileMantis is distributed in the hope that it will be useful,
	# but WITHOUT ANY WARRANTY; without even the implied warranty of
	# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	# GNU General Public License for more details.
	#
	# You should have received a copy of the GNU General Public License
	# along with agileMantis. If not, see <http://www.gnu.org/licenses/>.
	
	html_page_top(plugin_lang_get( 'manage_capacity_title' ));
?>
<br>
<?php
	
	include(PLUGIN_URI.'/pages/footer_menu.php');

	# add back button action
	if($_POST['back_button']){
		header($sprint->forwardReturnToPage('capacity.php'));
	} else {
		# save / update developer / team capacities
		if(!empty($_POST['capacity']) && !$_POST['addavailability']){
			foreach($_POST['capacity'] AS $user => $date){
				$count_over_capacity[$user] = 0;
				foreach($date AS $num => $row){
					if($row != ""){
						$row = str_replace(',','.',$row);
						if(!is_numeric($row)){
							$system =  plugin_lang_get( 'manage_capacity_error_985405' );
							$_POST['staycal'] = 1;
						} else {
							if($row < 0.00){
								$_POST['staycal'] = 1;
								$system = plugin_lang_get( 'manage_capacity_error_984401' );
							}
							if($row > 24.00){
								$_POST['staycal'] = 1;
								$system = plugin_lang_get( 'manage_capacity_error_984400' );
							}
							if($row >= 0.00 && $row <= 24.00){
								$team->insertTeamUserCapacity($_POST['team'], $user, $num, $row);
							}
							if($av->getAvailabilityToSavedCapacity($user,$num) < $av->getCapacityToSavedAvailability($user,$num) && $count_over_capacity[$user] != 1){
								$hinweis = plugin_lang_get( 'manage_capacity_error_108400' );
								$count_over_capacity[$user] = 1;
								$_POST['staycal'] = 1;
							}
						}
					}
				}
				if($count_over_capacity[$user] > 0){
					$av->setUserAsMarkType($user,1);
				} else {
					$av->setUserAsMarkType($user,0);
				}
			}
		}

		if($_POST['action'] == 'save' && $system == "" && !$_POST['addavailability'] && !$_POST['sprint'] && !$_POST['submit_button']){
			header($sprint->forwardReturnToPage('capacity.php'));
		}
		
		$showform = true;
		if($_POST['submit_button']){
			# check if start date is set
			if($_POST['start'] == ""){
				$system = plugin_lang_get( 'manage_capacity_error_923402' );
			}
			
			if(stristr($_POST['start'],',') && $system == ""){
				$_POST['start'] = str_replace(',','.',$_POST['start']);
			}
			
			if(stristr($_POST['start'],'.') && $system == ""){
				$_POST['start'] = str_replace('.','',$_POST['start']);
			}
			
			# check if reformatted date is numeric
			if(!is_numeric($_POST['start']) && $system == ""){
				$system = plugin_lang_get( 'manage_capacity_error_985403' );
			}
			
			# check if start date is between 6 and 10 digits long
			if((strlen($_POST['start']) < 6 || strlen($_POST['start']) > 10) && $system == ""){
				$system = plugin_lang_get( 'manage_capacity_error_985402' );
			}
			
			# check if start date is a valid date
			if($system == ""){

				$day 	= substr($_POST['start'],0,2);
				$month 	= substr($_POST['start'],2,2);
				$year_start 	= substr($_POST['start'], 4, strlen($_POST['start']));
				
				if($day > 31){
					$day = "";
				}

				if($month > 12){
					$month = "";
				}

				if(strlen($year_start) < 4){
					$year_start = '20'.$year_start;
				}

				if(!empty($day) && !empty($month) && !empty($year_start)){
					$_POST['start'] = $day.'.'.$month.'.'.$year_start;
				} else {
					$_POST['start'] = "";
					$system = plugin_lang_get( 'manage_capacity_error_985404' );
				}
			} else {
				$_POST['start'] = "";
			}
			
			if($year_start >= 2038 && $system == ""){
				$_POST['start'] =  "";
				$system = plugin_lang_get( 'manage_capacity_error_180401' );
			}
			# check if end date is set
			if($_POST['end'] == "" && $system == ""){
				$system = plugin_lang_get( 'manage_capacity_error_923400' );
			}
			
			if(stristr($_POST['end'],',') && $system == ""){
				$_POST['end'] = str_replace(',','.',$_POST['end']);
			}
			
			if(stristr($_POST['end'],'.') && $system == ""){
				$_POST['end'] = str_replace('.','',$_POST['end']);
			}
			
			if(!is_numeric($_POST['end']) && $system == ""){
				$system = plugin_lang_get( 'manage_capacity_error_985401' );
			}
			
			# check if end date is between 6 and 10 digits long
			if((strlen($_POST['end']) < 6 || strlen($_POST['end']) > 10) && $system == ""){
				$system = plugin_lang_get( 'manage_capacity_error_985400' );
			}
			
			if($system == ""){

				$day 	= substr($_POST['end'],0,2);
				$month 	= substr($_POST['end'],2,2);
				$year_end 	= substr($_POST['end'], 4, strlen($_POST['end']));

				if($day > 31){
					$day = "";
				}

				if($month > 12){
					$month = "";
				}

				if(strlen($year_end) < 4){
					$year_end = '20'.$year_end;
				}
				
				if(!empty($day) && !empty($month) && !empty($year_end)){
					$_POST['end'] = $day.'.'.$month.'.'.$year_end;
				} else {
					$_POST['end'] = "";
					$system = plugin_lang_get( 'manage_capacity_error_985406' );
				}
			} else {
				$_POST['end'] = "";
			}
			
			if($year_end >= 2038 && $system == ""){
				$_POST['end'] =  "";
				$system = plugin_lang_get( 'manage_capacity_error_180400' );
			}
			
			# check if team is set
			if($_POST['team'] == 0 && $system == ""){
				$system = plugin_lang_get( 'manage_capacity_error_923401' );
			}

			if(strtotime($_POST['end']) < strtotime($_POST['start']) && $system == ""){
				$system = plugin_lang_get( 'manage_capacity_error_180402' );
			}
			
			if(date('Y',strtotime($_POST['end'])) < date('Y',time())-2 && date('Y',strtotime($_POST['start'])) < date('Y',time())-2 && $system == ""){
				$system = plugin_lang_get( 'manage_capacity_error_980400' );
			}
			
			if($system == ""){
				$showform = false;
			}
		}
	}
?>
<br>
<?php if($hinweis){?>
		<center><span style="color:red; font-size:16px; font-weight:bold;"><?php echo $hinweis?></span></center>
	<br>
<?php }?>
<?php if($system){?>
		<center><span style="color:red; font-size:16px; font-weight:bold;"><?php echo $system?></span></center>
	<br>
<?php }?>
<?php if($showform && !$_POST['addavailability'] && !$_POST['staycal'] == 1){?>
<?php
	$show_post_values = 1;
	
	# change sprint start and end date
	if($_POST['sprint'] != ""){
		$sprint->sprint_id 	= 	htmlspecialchars($_POST['sprint']);
		$selectedSprint 	= 	$sprint->getSprintById();
		$sprint_start_date 	= 	explode('-',$selectedSprint['start']);
		$sprint_end_date 	= 	explode('-',$selectedSprint['end']);
		$show_post_values 	= 	0;
	}

	# set new start datet if no sprint name is set
	if(isset($_POST['sprint']) && $_POST['sprint']=="" && $_POST['post_values'] == 0){
		$_POST['end'] = "";
		$_POST['start'] = date("d.m.Y",time());
		$_POST['team'] = 0;
	}
?>
<form action="" method="post" id="capacity_form">
<?php if($show_post_values == 1){?>
<input type="hidden" name="post_values" value="<?php echo $show_post_values?>">
<?php }?>
<input type="hidden" name="action" value="save">
<input type="hidden" name="sprintName" value="<?php echo $_POST['sprintName']?>">
<input type="hidden" name="productBacklogName" value="<?php echo $_POST['productBacklogName']?>">
<input type="hidden" name="fromSprintBacklog" value="<?php echo $_POST['fromSprintBacklog']?>">
<input type="hidden" name="fromTaskboard" value="<?php echo $_POST['fromTaskboard']?>">
<input type="hidden" name="fromDailyScrum" value="<?php echo $_POST['fromDailyScrum']?>">
<input type="hidden" name="fromProductBacklog" value="<?php echo $_POST['fromProductBacklog']?>">
<table align="center" class="width75" cellspacing="1">
	<tr>
		<td colspan="2"><b><?php echo plugin_lang_get( 'manage_capacity_title' )?></b></td>
	</tr>
	<tr <?php echo helper_alternate_class() ?>>
		<td class="category"><b>Sprint</b></td>
		<td>
			<select name="sprint" onChange="this.form.submit();">
				<option value=""><?php echo plugin_lang_get( 'manage_capacity_title' )?></option>
				<?php
				# get all sprints
				$sprints = $sprint->getSprints();
				if(!empty($sprints)){
					while($row = mysql_fetch_assoc($sprints)){
						$start_date = explode('-',$row['start']);
						$end_date = explode('-',$row['end']);

					?>
						<option value="<?php echo $row['sname']?>" <?php if($row['sname'] == htmlspecialchars($_POST['sprint'])){?>selected<?php }?>><?php echo $row['sname']?><?php echo plugin_lang_get( 'manage_capacity_realised_by' )?><?php echo $sprint->getTeamById($row['team_id'])?><?php echo plugin_lang_get( 'manage_capacity_from' )?><?php echo $start_date[2].'.'.$start_date[1].'.'.$start_date[0]?><?php echo plugin_lang_get( 'manage_capacity_till' )?><?php echo $end_date[2].'.'.$end_date[1].'.'.$end_date[0]?></option>
					<?php
					}
				}
				?>
			</select>
		</td>
	</tr>
	<tr <?php echo helper_alternate_class() ?>>
		<td class="category"><b>*Start</b></td>
		<td><input type="text" name="start" value="<?php if($_POST['sprint'] != ""){echo $sprint_start_date[2].'.'.$sprint_start_date[1].'.'.$sprint_start_date[0];} else {if($_POST['start']){?><?php echo $_POST['start']?><?php } else {?><?php echo date("d.m.Y",time())?><?php }}?>"></td>
	</tr>
	<tr <?php echo helper_alternate_class() ?>>
		<td class="category"><b>*<?php echo plugin_lang_get( 'manage_capacity_end' )?></b></td>
		<td><input type="text" name="end" value="<?php if($_POST['sprint'] != ""){echo $sprint_end_date[2].'.'.$sprint_end_date[1].'.'.$sprint_end_date[0];} else { echo $_POST['end'];}?>"></td>
	</tr>
	<tr <?php echo helper_alternate_class() ?>>
		<td class="category"><b>*Team</b></td>
		<td>
			<?php if($_POST['fromProductBacklog']){?>
				<input type="hidden" name="team" value="<?php echo $_POST['team']?>">
			<?php }?>
			<select name="team" <?php if($_POST['fromProductBacklog']){?>disabled<?php }?>>
				<option value="0"><?php echo plugin_lang_get( 'common_chose' )?></option>
				<?
					$teamData = $team->getTeams();
					foreach($teamData AS $num => $row){
				?>
					<option value="<?php echo $row['id']?>" <?php if($_POST['sprint'] != ""){if($selectedSprint['team_id']==$row['id']){ ?>selected <?php }} else {if($_POST['team'] == $row['id']){?>selected<?php }?><?php }?>><?php echo $row['name']?></option>
				<?php }?>
			</select>
		</td>
	</tr>
	<tr>
		<td><span class="required"> * <?php echo lang_get( 'required' ) ?></span></td>
		<td>
			<input type="submit" name="submit_button" value="<?php echo plugin_lang_get( 'button_open_calendar' )?>">
			<input type="submit" style="margin-top:10px;" name="back_button" value="<?php echo plugin_lang_get( 'button_back' )?>">
		</td>
	</tr>
</table>
</form>
<?php }?>
<?php
if(($_POST['team'] != 0 && $_POST['start'] != "" && $_POST['end'] != "" && strtotime($_POST['end']) >= strtotime($_POST['start']) && $showform == false) || $_POST['addavailability'] || $_POST['staycal'] == 1){
$system = "";
?>
<form action="" method="post">
<input type="hidden" name="action" value="save">
<input type="hidden" name="start" value="<?php echo $_POST['start']?>">
<input type="hidden" name="end" value="<?php echo $_POST['end']?>">
<input type="hidden" name="team" value="<?php echo $_POST['team']?>">
<input type="hidden" name="fromSprintBacklog" value="<?php echo $_POST['fromSprintBacklog']?>">
<input type="hidden" name="fromTaskboard" value="<?php echo $_POST['fromTaskboard']?>">
<input type="hidden" name="productBacklogName" value="<?php echo $_POST['productBacklogName']?>">
<input type="hidden" name="fromProductBacklog" value="<?php echo $_POST['fromProductBacklog']?>">
<input type="hidden" name="fromDailyScrum" value="<?php echo $_POST['fromDailyScrum']?>">
<input type="hidden" name="sprintName" value="<?php echo $_POST['sprintName']?>">
<table align="center" class="width100" cellspacing="1">
<tr>
	<td class="left"><b><?php echo plugin_lang_get( 'manage_capacity_planning_for' )?></b> <span style="font-weight:bold;color:grey;"><?$team->id = $_POST['team'];$currentTeam = $team->getSelectedTeam();echo $currentTeam[0]['name'];?></span></td>
	<td class="right">
		<input type="submit" name="addavailability" value="<?php echo plugin_lang_get( 'manage_capacity_add_availability' )?>">
		<input type="submit" name="submit" value="<?php echo plugin_lang_get( 'button_save' )?>">
		<input type="submit" name="back_button" value="<?php echo plugin_lang_get( 'button_back' )?>">
	</td>
</tr>
</table>
	<div style="clear:both;"></div>
	<?php
		
		# configure days of a week
		$days_of_week[0] 	= 	plugin_lang_get( 'mo' );
		$days_of_week[1] 	= 	plugin_lang_get( 'tu' );
		$days_of_week[2] 	= 	plugin_lang_get( 'we' );
		$days_of_week[3] 	= 	plugin_lang_get( 'th' );
		$days_of_week[4] 	= 	plugin_lang_get( 'fr' );
		$days_of_week[5] 	= 	plugin_lang_get( 'sa' );
		$days_of_week[6] 	= 	plugin_lang_get( 'su' );

		# get date information
		$current_year		=	date('Y');
		$start 				= 	strtotime($_POST['start']);
		$end 				= 	strtotime($_POST['end']);

		$year_current		= 	date('Y');
		$year_start			=	date('Y',$start);
		$year_end			=	date('Y',$end);

		$month_start		=	date('n',$start);
		$month_end			=	date('n',$end);
		
		$datetime1 = new DateTime($_POST['start']);
		$datetime2 = new DateTime($_POST['end']);
		$interval = $datetime1->diff($datetime2);
		
		$amount_of_weeks = ceil($interval->format('%a') / 7);
		
		$amount_of_month 	=	 abs( ( $year_start * 12 + $month_start ) - ( $year_end * 12 + $month_end ) );
		
		# include calendary style
		include_once(PLUGIN_URI.'pages/user_capacity_style.php');

		$team->id 		=	$_POST['team'];
		$teamDeveloper 	= 	$team->getTeamDeveloper($_POST['team']);

		# open calendary foreach developer in chosen team
		if(!empty($teamDeveloper)){
			foreach($teamDeveloper AS $num => $row){
				if($row['username'] != null){
					include(PLUGIN_URI.'pages/calendary.php');
				}
			}
		}
	?>
	<div style="clear:both;"></div>
	<center><input type="submit" style="margin-top:10px;" name="submit" value="<?php echo plugin_lang_get( 'button_save' )?>"></center><br>
</form>
<?php }?>
<?php html_page_bottom() ?>