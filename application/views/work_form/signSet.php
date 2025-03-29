<input type="hidden" id="id" value="<?php echo $data->id; ?>">
<div class="pageTitle">工單報表管理-工單簽核設定</div>

<div class="pageContent work_form">
	<div class="row">
		<div class="col-md-6 col-xl-4">
			<label>表單編號：</label>
			<b><span id="formNo"><?php echo $data->formNo; ?></span></b>
		</div>
		<div class="col-md-6 col-xl-4">
			<label>設備[編號]：</label>
			<span><?php echo $data->robotName . "[" . $data->robotNo . "]"; ?></span>
		</div>
		<div class="col-md-6 col-xl-4">
			<label>作業時間：</label>
			<span>
				<?php
				$startTime = date("Y-m-d H:i", strtotime($data->startTime));
				if ($data->finishTime == '0000-00-00 00:00:00') {
					$workTime = '作業中(' . $startTime . '開始作業)';
				} else {
					$workTime = $startTime . '~' . date("Y-m-d H:i", strtotime($data->finishTime));
				}

				echo $workTime;
				?>
			</span>
		</div>

		<div class="col-md-12">
			<hr>
		</div>

		<div class="col-md-6 col-xl-4">
			<label>專案編號：</label>
			<span><?php echo $data->projectNo; ?></span>
		</div>
		<div class="col-md-6 col-xl-4">
			<label>工程專案名稱：</label>
			<span><?php echo $data->projectName; ?></span>
		</div>
		<div class="col-md-6 col-xl-4">
			<label>分項工程名稱：</label>
			<span><?php echo $data->subProjectName; ?></span>
		</div>
		<div class="col-md-6 col-xl-4">
			<label>協力廠商：</label>
			<span><?php echo $data->contractor; ?></span>
		</div>
		<div class="col-md-6 col-xl-4">
			<label>工作位置：</label>
			<span><?php echo $data->workPlaceName; ?></span>
		</div>
		<div class="col-md-6 col-xl-4">
			<label>日期：</label>
			<span>
				<?php
				if ($data->checkDate != '0000-00-00') {
					echo $data->checkDate;
				}
				?>
			</span>
		</div>
		<div class="col-md-6 col-xl-4">
			<label>業主：</label>
			<span><?php echo $data->company; ?></span>
		</div>
		<div class="col-md-6 col-xl-4">
			<label>管線編號：</label>
			<span><?php echo $data->pipingLineNo; ?></span>
		</div>
		<div class="col-md-12">
			<label>備註：</label>
			<span><?php echo $data->remark; ?></span>
		</div>

		<div class="col-md-12">
			<hr>
		</div>

		<div class="col-md-12"><span class="formSubTitle">簽核資料設定</span></div>
		<div class="col-md-12 alertMsg">
			<div class="padding_5px">*為必填資料</div>
		</div>
		<div class="col-md-12">
			<label class="formLabelTitle">施工單位</label>
			<?php
			echo '<div class="inlineBlock"><label><span class="alertMsg">*</span>簽核人員</label>';
			$subcontrator = $formDepStaff['subcontrator'];

			//施工單位若已簽核，則不能再修改設定
			$disabled = '';
			$msg = '';
			if ($formDepStaff['isSign']) {
				$disabled = 'disabled';
				$msg = '已進入簽核流程，無法修改設定';
			}
			echo '<input type="hidden" id="old_subcontratorId" value="'.$subcontrator['settingPersonId'].'">';
			echo '<input type="hidden" id="old_proxy_subcontratorId" value="'.$subcontrator['proxyPersonId'].'">';
			echo '<select id="subcontrator" onChange="signFormSelChange(this);" ' . $disabled . '>';
			echo '<option value="0">請選擇簽核人員</option>';
			foreach ($subcontrator['staff'] as $row) {
				$select = '';
				if ($row->id == $subcontrator['settingPersonId']) {
					$select = 'selected';
				}
				$disableSelect = '';
				if ($row->id == $subcontrator['proxyPersonId']) {
					$disableSelect = 'class="red" disabled';
				}
				echo '<option id="subcontrator_' . $row->id . '" value="' . $row->id . '" ' . $select . $disableSelect . '>' . $row->name . '[' . $row->staffNo . ']</option>';
			}
			echo '</select></div>';
			echo '<div class="inlineBlock"><label>簽核代理人員</label>';
			echo '<select id="proxy_subcontrator" onChange="proxySignFormSelChange(this);" ' . $disabled . '>';
			echo '<option value="0">請選擇簽核代理人員</option>';
			foreach ($subcontrator['staff'] as $row) {
				$select = '';
				if ($row->id == $subcontrator['proxyPersonId']) {
					$select = 'selected';
				}
				$disableSelect = '';
				if ($row->id == $subcontrator['settingPersonId']) {
					$disableSelect = 'class="red" disabled';
				}
				echo '<option id="proxy_subcontrator_' . $row->id . '" value="' . $row->id . '" ' . $select . $disableSelect . '>' . $row->name . '[' . $row->staffNo . ']</option>';
			}
			echo '</select></div>';
			?>
		</div>
		<div class="col-md-12">
			<label class="formLabelTitle">建造工程師</label>
			<?php
			echo '<div class="inlineBlock"><label><span class="alertMsg">*</span>簽核人員</label>';
			$construction = $formDepStaff['construction'];
			echo '<select id="construction" onChange="signFormSelChange(this);" ' . $disabled . '>';
			echo '<option value="0">請選擇簽核人員</option>';
			foreach ($construction['staff'] as $row) {
				$select = '';
				if ($row->id == $construction['settingPersonId']) {
					$select = 'selected';
				}
				$disableSelect = '';
				if ($row->id == $construction['proxyPersonId']) {
					$disableSelect = 'class="red" disabled';
				}
				echo '<option id="construction_' . $row->id . '" value="' . $row->id . '" ' . $select . $disableSelect . '>' . $row->name . '[' . $row->staffNo . ']</option>';
			}
			echo '</select></div>';
			echo '<div class="inlineBlock"><label>簽核代理人員</label>';
			echo '<select id="proxy_construction" onChange="proxySignFormSelChange(this);" ' . $disabled . '>';
			echo '<option value="0">請選擇簽核代理人員</option>';
			foreach ($construction['staff'] as $row) {
				$select = '';
				if ($row->id == $construction['proxyPersonId']) {
					$select = 'selected';
				}
				$disableSelect = '';
				if ($row->id == $construction['settingPersonId']) {
					$disableSelect = 'class="red" disabled';
				}
				echo '<option id="proxy_construction_' . $row->id . '" value="' . $row->id . '" ' . $select . $disableSelect . '>' . $row->name . '[' . $row->staffNo . ']</option>';
			}
			echo '</select></div>';
			?>
		</div>
		<div class="col-md-12">
			<label class="formLabelTitle">品管工程師</label>
			<?php
			echo '<div class="inlineBlock"><label><span class="alertMsg">*</span>簽核人員</label>';
			$qualityEngineer = $formDepStaff['qualityEngineer'];
			echo '<select id="qualityEngineer" onChange="signFormSelChange(this);" ' . $disabled . '>';
			echo '<option value="0">請選擇簽核人員</option>';
			foreach ($qualityEngineer['staff'] as $row) {
				$select = '';
				if ($row->id == $qualityEngineer['settingPersonId']) {
					$select = 'selected';
				}
				$disableSelect = '';
				if ($row->id == $qualityEngineer['proxyPersonId']) {
					$disableSelect = 'class="red" disabled';
				}
				echo '<option id="qualityEngineer_' . $row->id . '" value="' . $row->id . '" ' . $select . $disableSelect . '>' . $row->name . '[' . $row->staffNo . ']</option>';
			}
			echo '</select></div>';
			echo '<div class="inlineBlock"><label>簽核代理人員</label>';
			echo '<select id="proxy_qualityEngineer" onChange="proxySignFormSelChange(this);" ' . $disabled . '>';
			echo '<option value="0">請選擇簽核代理人員</option>';
			foreach ($qualityEngineer['staff'] as $row) {
				$select = '';
				if ($row->id == $qualityEngineer['proxyPersonId']) {
					$select = 'selected';
				}
				$disableSelect = '';
				if ($row->id == $qualityEngineer['settingPersonId']) {
					$disableSelect = 'class="red" disabled';
				}
				echo '<option id="proxy_qualityEngineer_' . $row->id . '" value="' . $row->id . '" ' . $select . $disableSelect . '>' . $row->name . '[' . $row->staffNo . ']</option>';
			}
			echo '</select></div>';
			?>
		</div>

		<div class="btnBox">

			<div class="alertMsg" id="errorMsg"><?php echo $msg; ?></div>
			<?php
			if ($formDepStaff['isSign']) {
				echo "<button class='button gray' onclick='gotoHome(" . $searchData . ");'>回上一頁</button>";
			} else {
				echo "<button class='button' onclick='saveFormSignSet(" . $searchData . ");'>儲存</button>";
				echo "<button class='button removeRed' onclick='gotoHome(" . $searchData . ");'>取消</button>";
			}
			?>
		</div>
	</div>
</div>
<script type="text/javascript" src='<?php echo base_url(); ?>appoint/javascript/common_function.js'></script>
<script type="text/javascript" src='<?php echo base_url(); ?>appoint/javascript/work_form.js'></script>