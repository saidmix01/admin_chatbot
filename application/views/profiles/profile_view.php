<!-- [ Layout content ] Start -->
<div class="layout-content">

	<!-- [ content ] Start -->
	<div class="container-fluid flex-grow-1 container-p-y">
		<h4 class="font-weight-bold py-3 mb-0">Profile page</h4>
		<div class="text-muted small mt-0 mb-4 d-block breadcrumb">
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="<?= base_url() ?>Home"><i class="feather icon-home"></i></a></li>
				<li class="breadcrumb-item active">Profile</li>
			</ol>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="card mb-4">
					<h6 class="card-header">Add new profile</h6>
					<div class="card-body">
						<form id="form_profile">
							<input type="hidden" name="pro_id" id="pro_id">
							<div class="form-row">
								<div class="form-group col-md-6">
									<label class="form-label">Status</label>
									<select class="custom-select" name="pro_status" id="pro_status">
										<option value="">Select Status</option>
										<option value="1">Active</option>
										<option value="0">Inactive</option>
									</select>
								</div>
								<div class="form-group col-md-6">
									<label class="form-label">Profile name</label>
									<input type="text" class="form-control" placeholder="Profile" name="pro_description" id="pro_description">
									<div class="clearfix"></div>
								</div>
							</div>
							<button type="submit" onclick="save_profile()" id="btn_save_update" class="btn btn-success">Save</button>
						</form>
					</div>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-12">
				<div class="card mb-4">
					<div class="card-body">
						<div class="table-responsive">
							<table id="table_profiles" class="table card-table" style="width:100%">
								<thead>
									<tr>
										<th>Id</th>
										<th>Status</th>
										<th>Name</th>
										<th>Actions</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table>	
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- [ content ] End -->
</div>
