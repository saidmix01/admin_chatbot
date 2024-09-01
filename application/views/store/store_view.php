<!-- [ Layout content ] Start -->
<div class="layout-content">

	<!-- [ content ] Start -->
	<div class="container-fluid flex-grow-1 container-p-y">
		<h4 class="font-weight-bold py-3 mb-0">Stores</h4>
		<div class="text-muted small mt-0 mb-4 d-block breadcrumb">
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="<?= base_url() ?>Home"><i class="feather icon-home"></i></a></li>
				<li class="breadcrumb-item active">Stores</li>
			</ol>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="card mb-4">
					<h6 class="card-header">Add new store</h6>
					<div class="card-body">
						<form id="form_stores" method="POST">
							<input type="hidden" name="sto_id" id="sto_id">
							<div class="form-row">
								<div class="form-group col-md-4">
									<label class="form-label">Status</label>
									<select class="custom-select" name="sto_status" id="sto_status">
										<option value="">Select Status</option>
										<option value="1">Active</option>
										<option value="0">Inactive</option>
									</select>
								</div>
								<div class="form-group col-md-4">
									<label class="form-label">Owner</label>
									<select class="custom-select" name="us_id" id="us_id">

									</select>
								</div>
								<div class="form-group col-md-4">
									<label class="form-label">Store name</label>
									<input type="text" class="form-control" placeholder="Store" name="sto_name" id="sto_name">
								</div>
							</div>
							<div class="form-row">
								<div class="form-group col-md-4">
									<label class="form-label">Store email</label>
									<input type="email" class="form-control" name="sto_email" id="sto_email" placeholder="example@email.com">
								</div>
								<div class="form-group col-md-4">
									<label class="form-label">Store Address</label>
									<input type="text" class="form-control" placeholder="Address" name="sto_direction" id="sto_direction">
								</div>
								<div class="form-group col-md-4">
									<label class="form-label">Store Phone Number</label>
									<input type="number" class="form-control" placeholder="Phone Number" name="sto_phone" id="sto_phone">
								</div>
							</div>
							<div class="form-row">
								<div class="form-group col-md-12">
									<label class="form-label">Wellcome Message</label>
									<textarea class="form-control" placeholder="Write something" name="sto_wellcome_message" id="sto_wellcome_message"></textarea>
									<small class="form-text text-muted">Max 200 characteres</small>
								</div>
							</div>
							<button type="submit" onclick="save()" id="btn_save_update" class="btn btn-success">Save</button>
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
							<table id="table_stores" class="table card-table" style="width:100%">
								<thead>
									<tr>
										<th>Id</th>
										<th>Status</th>
										<th>Store</th>
										<th>Owner</th>
										<th>Email</th>
										<th>QR Code</th>
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
