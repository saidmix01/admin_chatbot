window.addEventListener('load', async function () {
	await get_services_user();
});

const get_services_user = async (data = {}) => {
	document.querySelector('.loading').style.display = "flex";
	fetch(`${base_url}Services/get_services_by_user`, {
		method: 'POST',
		headers: { 'Content-Type': 'application/json' },
		body: JSON.stringify(data)
	})
		.then(response => response.json())
		.then(async data => {
			if (!data.status) throw new Error(data.message);
			data.data.forEach(item => {
				console.log({item});
				
				let card_store = `
				<div class="col-md-6" onclick="view_service('${item.ser_us_id}')">
					<div class="card mb-4">
						<div class="card-body">
							<div class="d-flex align-items-center justify-content-between">
								<div class="">
									<h2 class="mb-2">${item.ser_name}</h2>
									<p class="text-muted mb-0"></p>
								</div>
								<div class="lnr lnr-chart-bars display-4 text-success"></div>
							</div>
						</div>
					</div>
				</div>`;
				document.getElementById('content_stores').innerHTML += card_store;
			});
			document.querySelector('.loading').style.display = "none";
		})
		.catch(error => {
			document.querySelector('.loading').style.display = "none";
			console.log(error);
			Swal.fire({
				icon: "error",
				title: "Something went wrong!",
				text: error
			});
		});
}





const get_stores = async (data = {}) => {
	document.querySelector('.loading').style.display = "flex";
	fetch(`${base_url}Stores/get_stores`, {
		method: 'POST',
		headers: { 'Content-Type': 'application/json' },
		body: JSON.stringify(data)
	})
		.then(response => response.json())
		.then(async data => {
			if (!data.status) throw new Error(data.message);
			data.data.forEach(item => {
				let card_store = `
				<div class="col-md-6" onclick="view_store('${item.sto_id}')">
					<div class="card mb-4">
						<div class="card-body">
							<div class="d-flex align-items-center justify-content-between">
								<div class="">
									<h2 class="mb-2">${item.sto_name}</h2>
									<p class="text-muted mb-0"><span class="badge badge-success">20</span> Chats</p>
								</div>
								<div class="lnr lnr-chart-bars display-4 text-success"></div>
							</div>
						</div>
					</div>
				</div>`;
				document.getElementById('content_stores').innerHTML += card_store;
			});
			document.querySelector('.loading').style.display = "none";
		})
		.catch(error => {
			document.querySelector('.loading').style.display = "none";
			console.log(error);
			Swal.fire({
				icon: "error",
				title: "Something went wrong!",
				text: error
			});
		});
}


const view_store = (sto_id = "") => {
	location.href = `${base_url}Stores/my_store?id=${sto_id}`
}
