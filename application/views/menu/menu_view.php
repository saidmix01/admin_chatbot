<!-- [ Layout content ] Start -->
<div class="layout-content">

	<!-- [ content ] Start -->
	<div class="container-fluid flex-grow-1 container-p-y">
		<h4 class="font-weight-bold py-3 mb-0">Menu page</h4>
		<div class="text-muted small mt-0 mb-4 d-block breadcrumb">
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="<?= base_url() ?>Home"><i class="feather icon-home"></i></a></li>
				<li class="breadcrumb-item active">Menu</li>
			</ol>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="card mb-4">
					<h6 class="card-header">Add new menu</h6>
					<div class="card-body">
						<form id="form_menu">
							<input type="hidden" name="men_id" id="men_id">
							<div class="form-row">
								<div class="form-group col-md-4">
									<label class="form-label">Status</label>
									<select class="custom-select" name="men_status" id="men_status">
										<option value="">Select Status</option>
										<option value="1">Active</option>
										<option value="0">Inactive</option>
									</select>
								</div>
								<div class="form-group col-md-4">
									<label class="form-label">Menu name</label>
									<input type="text" class="form-control" placeholder="Name" name="men_description" id="men_description">
									<div class="clearfix"></div>
								</div>
								<div class="form-group col-md-4">
									<label class="form-label">Icon</label>
									<input type="text" class="form-control" name="men_icon" id="men_icon">
									<div class="clearfix"></div>
								</div>
							</div>
							<button type="submit" onclick="save_menu()" id="btn_save_update" class="btn btn-success">Save</button>
						</form>
					</div>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-12">
				<div class="card mb-4">
					<div class="card-body">
						<table id="table_menus" class="table card-table" style="width:100%">
							<thead>
								<tr>
									<th>Id</th>
									<th>Status</th>
									<th>Name</th>
									<th>Icon</th>
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
