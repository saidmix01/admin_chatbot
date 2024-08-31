<!-- [ Layout content ] Start -->
<div class="layout-content">
<input type="hidden" id="lis_id_2" name="lis_id_2" value="<?=$lis_id?>">
	<!-- [ content ] Start -->
	<div class="container-fluid flex-grow-1 container-p-y">
		<h4 class="font-weight-bold py-3 mb-0">Options List</h4>
		<div class="text-muted small mt-0 mb-4 d-block breadcrumb">
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="<?= base_url() ?>Home"><i class="feather icon-home"></i></a></li>
				<li class="breadcrumb-item"><a href="<?= base_url() ?>List_manage"><i class="feather icon-file-text"></i></a></li>
				<li class="breadcrumb-item active">Options List</li>
			</ol>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="card mb-4">
					<div class="card-header"><strong>Add new option</strong></div>
					<div class="card-body">
						<form id="form_option">
							<input type="hidden" name="opt_id" id="opt_id">
							<input type="hidden" id="lis_id" name="lis_id" value="<?=$lis_id?>">
							<div class="form-row">
								<div class="form-group col-md-2">
								<label class="form-label">Status</label>
									<select class="custom-select form-control" name="opt_status" id="opt_status">
										<option value="">Select Status</option>
										<option value="1">Active</option>
										<option value="0">Inactive</option>
									</select>
								</div>
								<div class="form-group col-md-3">
									<label class="form-label">Option description</label>
									<input type="text" class="form-control" placeholder="Description" name="opt_description" id="opt_description">
								</div>
								<div class="form-group col-md-2">
									<label class="form-label">Price</label>
									<input type="number" class="form-control" placeholder="Price" name="opt_price" id="opt_price">
								</div>
								<div class="form-group col-md-2">
									<label class="form-label">Quantity</label>
									<input type="number" class="form-control" placeholder="Quantity" name="opt_qty" id="opt_qty">
								</div>
								<div class="form-group col-md-3">
									<label class="form-label">Order</label>
									<input type="number" class="form-control" placeholder="Order on the list" name="opt_order" id="opt_order">
								</div>
							</div>
							<div class="form-row">
								<div class="form-group col-md-12">
									<label class="form-label">More Information</label>
									<textarea class="form-control" placeholder="Textarea" name="opt_more_information" id="opt_more_information"></textarea>
									<small class="form-text text-muted">Max 100 characteres</small>
								</div>
							</div>
							<div class="form-row">
								<button type="submit" 
										class="btn btn-success" 
										id="btn_save_update" 
										onclick="save_option(`<?php echo $this->session->userdata('us_id'); ?>`)">
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
							<table id="table_option" class="table card-table" style="width:100%">
								<thead>
									<tr>
										<th>Id</th>
										<th>Order</th>
										<th>Status</th>
										<th>Description</th>
										<th>Price</th>
										<th>Quantity</th>
										<th>More Information</th>
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
