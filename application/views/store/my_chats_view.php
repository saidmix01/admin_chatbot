<!-- [ Layout content ] Start -->
<div class="layout-content">

	<!-- [ content ] Start -->
	<div class="container-fluid flex-grow-1 container-p-y">
		<h4 class="font-weight-bold py-3 mb-0">My Chats</h4>
		<div class="text-muted small mt-0 mb-4 d-block breadcrumb">
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="#"><i class="feather icon-home"></i></a></li>
				<li class="breadcrumb-item active">My Chats</li>
			</ol>
		</div>
		<div class="row">
			<div class="col-sm-6 col-md-3">
				<div class="card mb-4" onclick="load_chats_table(1)">
					<div class="card-body">
						<div class="d-flex align-items-center justify-content-between">
							<div class="">
								<h3 class="mb-2"> Started chats </h3>
								<p class="text-muted mb-0"><span class="badge badge-success" id="chat_started">0</span> Chats</p>
							</div>
							<div class="lnr lnr-bubble display-4 text-info"></div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-sm-6 col-md-3">
				<div class="card mb-4" onclick="load_chats_table(2)">
					<div class="card-body">
						<div class="d-flex align-items-center justify-content-between">
							<div class="">
								<h3 class="mb-2">Chats in Progress</h3>
								<p class="text-muted mb-0"><span class="badge badge-success" id="chat_progress">0</span> Chats</p>
							</div>
							<div class="lnr lnr-bubble display-4 text-warning"></div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-sm-6 col-md-3">
				<div class="card mb-4" onclick="load_chats_table(3)">
					<div class="card-body">
						<div class="d-flex align-items-center justify-content-between">
							<div class="">
								<h3 class="mb-2">Chats Success</h3>
								<p class="text-muted mb-0"><span class="badge badge-success" id="chat_success">0</span> Chats</p>
							</div>
							<div class="lnr lnr-bubble display-4 text-success"></div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-sm-6 col-md-3">
				<div class="card mb-4" onclick="load_chats_table(4)">
					<div class="card-body">
						<div class="d-flex align-items-center justify-content-between">
							<div class="">
								<h3 class="mb-2">Chats Errors</h3>
								<p class="text-muted mb-0"><span class="badge badge-success" id="chat_error">0</span> Chats</p>
							</div>
							<div class="lnr lnr-bubble display-4 text-danger"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
			<div class="card mb-12" onclick="">
				<div class="card-body">
					<div class="table-responsive">
						<table id="table_chats" class="table card-table" style="width:100%">
							<thead>
								<tr>
									<th>Phone</th>
									<th>Name</th>
									<th>Date</th>
									<th>Log</th>
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
		<div class="row">
			<div class="col-md-12">
			<div class="card mb-12" onclick="">
				<div class="card-body">
					<div id="chartContainer" style="height: 370px; width: 100%;"></div>
				</div>
			</div>
		</div>
	</div>
	<!-- [ content ] End -->
</div>
<script>
	const sto_id = '<?= $sto_id ?>';
	const us_id_view = '<?= $us_id ?>';
</script>
