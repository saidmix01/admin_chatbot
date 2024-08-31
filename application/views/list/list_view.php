<!-- [ Layout content ] Start -->
<div class="layout-content">

	<!-- [ content ] Start -->
	<div class="container-fluid flex-grow-1 container-p-y">
		<h4 class="font-weight-bold py-3 mb-0">List manage</h4>
		<div class="text-muted small mt-0 mb-4 d-block breadcrumb">
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="<?= base_url() ?>Home"><i class="feather icon-home"></i></a></li>
				<li class="breadcrumb-item active">List manage</li>
			</ol>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="card mb-4">
					<div class="card-header"><strong>New list</strong></div>
					<div class="card-body">
						<form id="form_list">
							<input type="hidden" name="lis_id" id="lis_id">
							<div class="form-row">
								<div class="form-group col-md-6">
								<label class="form-label">Status</label>
									<select class="custom-select form-control" name="lis_status" id="lis_status">
										<option value="">Select Status</option>
										<option value="1">Active</option>
										<option value="0">Inactive</option>
									</select>
								</div>
								<div class="form-group col-md-6">
									<label class="form-label">List name</label>
									<input type="text" class="form-control" placeholder="Name List" name="lis_name" id="lis_name">
								</div>
							</div>
							<div class="form-row">
								<button type="submit" 
										class="btn btn-success" 
										id="btn_save_update" 
										onclick="save_list(`<?php echo $this->session->userdata('us_id'); ?>`)">
									Save
								</button>
							</div>
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
							<table id="table_list" class="table card-table" style="width:100%">
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
