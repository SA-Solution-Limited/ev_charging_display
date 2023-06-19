<div class="card py-3 mb-4">
    <div class="card-header">
        <h4 class="mb-4 text-center">EV Charging Simulator</h4>
    </div>
    <div class="card-body">
		<form name="simulator" action="" method="post">
			<div class="form-group row mt-3">
				<label class="col-form-label col-md-3">Status</label>
				<div class="col-md-9">
					<select name="status" class="form-control">
						<option value="vacant" <?php echo($simulator->status == 'vacant' ? 'selected' : ''); ?>>Vacant</option>
						<option value="entrance" <?php echo($simulator->status == 'entrance' ? 'selected' : ''); ?>>Vehicle Entering Charging Zone</option>
						<option value="charging" <?php echo($simulator->status == 'charging' ? 'selected' : ''); ?>>Charging</option>
						<option value="charging_completed" <?php echo($simulator->status == 'charging_completed' ? 'selected' : ''); ?>>Charging Completed</option>
						<option value="exit" <?php echo($simulator->status == 'exit' ? 'selected' : ''); ?>>Vehicle Exiting Charging Zone</option>
						<option value="warning" <?php echo($simulator->status == 'failure' ? 'selected' : ''); ?>>Warning</option>
					</select>
				</div>
			</div>
			<div class="form-group row mt-3">
				<label class="col-form-label col-md-3">Initial Battery Level</label>
				<div class="col-md-9">
					<div class="input-group">
						<input type="number" name="initialBatteryLevel" value="<?php echo($simulator->initialBatteryLevel); ?>" class="form-control" />
						<div class="input-group-append"><span class="input-group-text">%</span></div>
					</div>
				</div>
			</div>
			<div class="form-group row mt-3">
				<label class="col-form-label col-md-3">Current Battery Level</label>
				<div class="col-md-9">
					<input class="form-range" style="border-color:#696cff" type="range" name="currentBatteryLevel" min="0" max="100" step="1" value="<?php echo($simulator->currentBatteryLevel); ?>" />
				</div>
			</div>
			<hr />
        </form>
	</div>
</div>