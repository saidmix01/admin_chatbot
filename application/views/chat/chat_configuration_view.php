<!-- [ Layout content ] Start -->
<?php
$q = 0;
$show_answer = "display: none";
if (!empty($question)) {
	$q = $question;
	$show_answer = "";
} ?>
<div class="layout-content chat_configurarion">

	<!-- [ content ] Start -->
	<div class="container-fluid flex-grow-1 container-p-y">
		<h4 class="font-weight-bold py-3 mb-0">Chat configuration</h4>
		<div class="text-muted small mt-0 mb-4 d-block breadcrumb">
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="<?= base_url() ?>Home"><i class="feather icon-home"></i></a></li>
				<li class="breadcrumb-item active">Chat configuration</li>
			</ol>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="card">
					<div class="card-header with-elements pb-0 header_question">
						<?php if ($q != 0) { ?>
							<button class="btn icon-btn btn-xs btn-outline-info waves-effect" onclick="window.history.back();">
								<span class="feather icon-arrow-left"></span>
							</button>
						<?php } ?>
						<h6 id="question_selected">...</h6>
						<div class="card-header-elements ml-auto p-0">
							<ul class="nav nav-tabs">
								<li class="nav-item">
									<a class="nav-link show active" data-toggle="tab" href="#sale-stats">Questions</a>
								</li>
								<li class="nav-item" style="<?=$show_answer?>">
									<a class="nav-link show" data-toggle="tab" href="#latest-sales">Answer</a>
								</li>
							</ul>
						</div>
					</div>
					<div class="card-body">
						<div class="nav-tabs-top">
							<div class="tab-content">
								<div class="tab-pane fade active show" id="sale-stats">
									<div id="tab-table-1" class="ps ps--active-x ps--active-y">
										<form action="" id="form_question">
											<div class="form-row">
												<input type="hidden" name="chq_id" id="chq_id">
												<div class="form-group col-md-4">
													<label class="form-label">Status</label>
													<select name="chq_status" id="chq_status" class="form-control">
														<option value="">Select something</option>
														<option value="0">Deactive</option>
														<option value="1">Active</option>
													</select>
												</div>
												<div class="form-group col-md-4">
													<label class="form-label">Select list</label>
													<select name="lis_id" id="lis_id" class="form-control">

													</select>
												</div>
												<input type="hidden" name="chq_parent" id="chq_parent" value="<?= $q ?>">
												<div class="form-group col-md-4">
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
								</div>
								<div class="tab-pane fade" id="latest-sales">
									<div style="height: 330px" id="tab-table-2" class="ps">
										<div class="table-responsive">
											<table id="table_options" class="table card-table" style="width:100%">
												<thead>
													<tr>
														<th>Order</th>
														<th>Description</th>
														<th>Price</th>
														<th>QTY</th>
														<th>Information</th>
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
