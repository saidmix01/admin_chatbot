<!-- [ Layout content ] Start -->
<div class="layout-content">

	<!-- [ content ] Start -->
	<div class="container-fluid flex-grow-1 container-p-y">
		<h4 class="font-weight-bold py-3 mb-0">Users page</h4>
		<div class="text-muted small mt-0 mb-4 d-block breadcrumb">
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="<?= base_url() ?>Home"><i class="feather icon-home"></i></a></li>
				<li class="breadcrumb-item active">Users</li>
			</ol>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="card mb-4">
					<h6 class="card-header">Add new user</h6>
					<div class="card-body">
						<form id="form_user">
							<input type="hidden" name="us_id" id="us_id">
							<div class="form-row">
								<div class="form-group col-md-4">
									<label class="form-label">Status</label>
									<select class="custom-select" name="us_status" id="us_status">
										<option value="">Select Status</option>
										<option value="1">Active</option>
										<option value="0">Inactive</option>
									</select>
								</div>
								<div class="form-group col-md-4">
									<label class="form-label">User name</label>
									<input type="text" class="form-control" placeholder="Name" name="us_name" id="us_name">
									<div class="clearfix"></div>
								</div>
								<div class="form-group col-md-4">
									<label class="form-label">User Email</label>
									<input type="email" class="form-control" name="us_email" id="us_email" placeholder="joe@mail.com">
									<div class="clearfix"></div>
								</div>
							</div>
							<div class="form-row">
								<div class="form-group col-md-4">
									<label class="form-label">Profile</label>
									<select class="custom-select" name="pro_id" id="pro_id">
										<option value="">Select something</option>
									</select>
								</div>
								<div class="form-group col-md-4">
									<label class="form-label">Password</label>
									<input type="password" class="form-control" name="us_password" id="us_password" placeholder="Your password">
								</div>
								<div class="form-group col-md-4">
									<label class="form-label">Confirm Password</label>
									<input type="password" class="form-control" name="us_password_confirm" id="us_password_confirm" placeholder="Repeat Your password">
								</div>
							</div>
							<button type="submit" onclick="save_user()" id="btn_save_update" class="btn btn-success">Save</button>
						</form>
					</div>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-12">
				<div class="card mb-4">
					<div class="card-body">
						<table id="table_users" class="table card-table" style="width:100%">
							<thead>
								<tr>
									<th>Id</th>
									<th>Status</th>
									<th>Name</th>
									<th>Email</th>
									<th>Profile</th>
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
	<!-- [ content ] End -->
</div>
