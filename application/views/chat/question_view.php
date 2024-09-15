<!-- [ Layout content ] Start -->
<div class="layout-content">
	<!-- [ content ] Start -->
	<div class="container-fluid flex-grow-1 container-p-y">
		<h4 class="font-weight-bold py-3 mb-0">Question</h4>
		<div class="text-muted small mt-0 mb-4 d-block breadcrumb">
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="<?= base_url() ?>Home"><i class="feather icon-home"></i></a></li>
				<li class="breadcrumb-item"><a href="<?= base_url() ?>Chat_configuration">Chat configuration</a></li>
				<li class="breadcrumb-item active">Question</li>
			</ol>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="card">
					<h6 class="card-header" id="main_question"></h6>
					<div class="card-body">
						<div id="yes_no" style="display: none;">
							<form id="form_yes_no">
								<div class="form-row">
									<input type="hidden" name="chq_id" id="chq_id">
									<div class="form-group col-md-3">
										<label class="form-label">Status</label>
										<select name="chq_status" id="chq_status" class="form-control">
											<option value="">Select something</option>
											<option value="0">Deactive</option>
											<option value="1">Active</option>
										</select>
									</div>
									<div class="form-group col-md-3">
										<label class="form-label">Response</label>
										<select name="chq_response" id="chq_response" class="form-control">
											<option value="">Select something</option>
											<option value="0">No</option>
											<option value="1">Yes</option>
										</select>
									</div>
									<div class="form-group col-md-3">
										<label class="form-label">Select Type</label>
										<select name="chq_type" id="chq_type" class="form-control">
											<option value="">Select something</option>
											<option value="1">List</option>
											<option value="2">Yes/No</option>
											<option value="3">Text</option>
										</select>
									</div>
									<div class="form-group col-md-3">
										<label class="form-label">Order</label>
										<input type="number" name="chq_order" id="chq_order" class="form-control" placeholder="Order question">
									</div>
								</div>
								<div class="form-row">
									<div class="form-group col-md-12">
										<label class="form-label">Question</label>
										<input type="text" name="chq_text" id="chq_text" class="form-control" placeholder="Enter your question">
									</div>
								</div> 
								<div class="form-row">
									<button class="btn btn-success" type="submit" id="btn_save_update" onclick="save_question(`<?php echo $this->session->userdata('us_id'); ?>`)">
										Add Question
									</button>
								</div>
							</form>
						</div>
						<div id="list">
							<form id="form_list">
								<div class="form-row">
									<input type="hidden" name="chq_id" id="chq_id">
									<div class="form-group col-md-3">
										<label class="form-label">Status</label>
										<select name="chq_status" id="chq_status" class="form-control">
											<option value="">Select something</option>
											<option value="0">Deactive</option>
											<option value="1">Active</option>
										</select>
									</div>
									<div class="form-group col-md-3">
										<label class="form-label">Lists</label>
										<select name="chq_lists" id="chq_lists" class="form-control">
											<option value="">Select something</option>
											<option value="0">No</option>
											<option value="1">Yes</option>
										</select>
									</div>
									<div class="form-group col-md-3">
										<label class="form-label">Select Type</label>
										<select name="chq_type" id="chq_type" class="form-control">
											<option value="">Select something</option>
											<option value="1">List</option>
											<option value="2">Yes/No</option>
											<option value="3">Text</option>
										</select>
									</div>
									<div class="form-group col-md-3">
										<label class="form-label">Order</label>
										<input type="number" name="chq_order" id="chq_order" class="form-control" placeholder="Order question">
									</div>
								</div>
								<div class="form-row">
									<div class="form-group col-md-12">
										<label class="form-label">Question</label>
										<input type="text" name="chq_text" id="chq_text" class="form-control" placeholder="Enter your question">
									</div>
								</div> 
								<div class="form-row">
									<button class="btn btn-success" type="submit" id="btn_save_update" onclick="save_question(`<?php echo $this->session->userdata('us_id'); ?>`)">
										Add Question
									</button>
								</div>
							</form>
						</div>
						<div id="text">

						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="card mb-4">
					<div class="card-body">
						<div class="table-responsive">
							<table id="table_questions" class="table card-table" style="width:100%">
								<thead>
									<tr>
										<th>Id</th>
										<th>Order</th>
										<th>Status</th>
										<th>List</th>
										<th>Parent</th>
										<th>Question</th>
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
<script>
	const us_id = `<?php echo $this->session->userdata('us_id'); ?>`;
</script>
