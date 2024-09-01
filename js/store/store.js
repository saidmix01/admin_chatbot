window.addEventListener('load', async function () {
	await get_users({ us_status: 1 });
	await get_stores({ sto_status: 1 });
});

/**
 * The `save_question` function in JavaScript handles saving or updating store information with form
 * validation and error handling.
 */
const save = async () => {
	try {
		const { sto_status, us_id, sto_name, sto_email, sto_direction, sto_phone, sto_wellcome_message } = await get_elements_form('form_stores');
		//Validate form fields
		if (sto_status == "") throw new Error("Status is required");
		if (sto_name == "") throw new Error("Name is required");
		if (sto_email == "") throw new Error("Email is required");
		if (sto_phone == "") throw new Error("Phone is required");
		if (sto_wellcome_message == "") throw new Error("Wellcome message is required");
		const sto_id = document.querySelector(`#sto_id`).value;
		let now = new Date();
		let sto_create_date = now.toISOString();
		let url_store = `${base_url}Stores/save`
		let data_send = { sto_status, us_id, sto_name, sto_email, sto_direction, sto_phone, sto_wellcome_message, sto_create_date }
		if (sto_id != "") {
			url_store = `${base_url}Stores/update`
			data_send = { sto_id, sto_status, us_id, sto_name, sto_email, sto_direction, sto_phone, sto_wellcome_message, sto_create_date }
		}
		document.querySelector('.loading').style.display = "flex";
		send_data(url_store, data_send)
			.then(response => {
				if (response.status) {
					document.querySelector('.loading').style.display = "none";
					Swal.fire({
						icon: "success",
						title: "Success",
						text: response.message
					}).then(async (result) => {
						if (result.isConfirmed) {
							location.reload();
						}
					});
				} else {
					document.querySelector('.loading').style.display = "none";
					Swal.fire({
						icon: "error",
						title: "Opss...",
						text: `${response.message}`
					})
				}
			})
			.catch(error => {
				document.querySelector('.loading').style.display = "none";
				console.error('Error:', error);
				throw new Error(error);
			});
	} catch (error) {
		console.log(error);
		document.querySelector('.loading').style.display = "none";
		Swal.fire({
			icon: "error",
			title: "Something went wrong!",
			text: error
		});
	}
}

/**
 * The function `get_users` makes an asynchronous POST request to fetch user data, processes the
 * response, and updates the UI accordingly.
 * @param [data] - The `get_users` function is an asynchronous function that fetches user data from a
 * specified URL endpoint (`Users/get_users`) using a POST request with the provided data in
 * JSON format.
 */
const get_users = async (data = {}) => {
	document.querySelector('.loading').style.display = "flex";
	fetch(`${base_url}Users/get_users`, {
		method: 'POST',
		headers: { 'Content-Type': 'application/json' },
		body: JSON.stringify(data)
	})
		.then(response => response.json())
		.then(async data => {
			document.querySelector('.loading').style.display = "none";
			if (!data.status) throw new Error(data.message);
			let data_resp = [];
			data.data.forEach(element => {
				data_resp.push({ id: element.us_id, name: element.us_name });
			});
			if (data.status) {
				paint_select(data_resp, 'us_id');
			} else {
				Swal.fire({
					icon: "error",
					title: "Opss...",
					text: `${data.message}`
				})
			}

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


/**
 * The function `load_data_form` fetches store data based on store ID and updates the form with the
 * retrieved data.
 * @param [name_form] - The `name_form` parameter in the `load_data_form` function is used to specify
 * the name of the form that needs to be loaded. It is a required parameter and if not provided or an
 * empty string is passed, an error "Form not found" will be thrown.
 * @param [sto_id] - The `sto_id` parameter in the `load_data_form` function is used to specify the ID
 * of the store for which data needs to be loaded. This ID is then sent as part of the request to fetch
 * store data from the server.
 */
const load_data_form = async (name_form = "", sto_id = "") => {
	try {

		if (name_form == "") throw new Error("Form not found");
		if (sto_id == "") throw new Error("chq_id not found");
		document.querySelector('.loading').style.display = "flex";
		fetch(`${base_url}Stores/get_stores`, {
			method: 'POST',
			headers: { 'Content-Type': 'application/json' },
			body: JSON.stringify({ sto_id })
		})
			.then(response => response.json())
			.then(async data => {
				if (!data.status) throw new Error(data.message);
				load_form_data(data.data[0], name_form);
				const btn = document.querySelector(`#btn_save_update`);
				if (btn.classList.contains('btn-success')) {
					btn.classList.remove('btn-success');
					btn.classList.add('btn-warning');
					btn.textContent = 'Update';
				} else if (btn.classList.contains('btn-warning')) {
					btn.classList.remove('btn-warning');
					btn.classList.add('btn-success');
					btn.textContent = 'Save';
				}
				document.querySelector('.loading').style.display = "none";
			})
			.catch(error => {
				console.log(error);
				document.querySelector('.loading').style.display = "none";
				Swal.fire({
					icon: "error",
					title: "Something went wrong!",
					text: error
				});
			});
	} catch (error) {
		document.querySelector('.loading').style.display = "none";
		Swal.fire({
			icon: "error",
			title: "Something went wrong!",
			text: error
		});
	}
}


/**
 * The function `delete_question` is an asynchronous function that sends a POST request to delete a
 * question based on the provided ID, displaying success or error messages using SweetAlert and
 * handling loading indicators.
 * @param [sto_id] - The `sto_id` parameter in the `delete_question` function is used to specify the ID
 * of the store that you want to delete. This function is an asynchronous function that sends a POST
 * request to the server to delete the store with the specified ID. If the `sto_id` parameter is empty
 */
const delete_store = async (sto_id = "") => {
	try {
		if (sto_id == "") throw new Error("Empty field");
		document.querySelector('.loading').style.display = "flex";
		fetch(`${base_url}Stores/delete`, {
			method: 'POST',
			headers: { 'Content-Type': 'application/json' },
			body: JSON.stringify({ sto_id })
		})
			.then(response => response.json())
			.then(async data => {
				if (!data.status) throw new Error(data.message);
				if (data.status) {
					document.querySelector('.loading').style.display = "none";
					Swal.fire({
						icon: "success",
						title: "Success",
						text: data.message
					}).then(async (result) => {
						if (result.isConfirmed) {
							location.reload();
						}
					});
				} else {
					document.querySelector('.loading').style.display = "none";
					Swal.fire({
						icon: "error",
						title: "Opss...",
						text: `${data.message}`
					})
				}
			})
			.catch(error => {
				console.log(error);
				document.querySelector('.loading').style.display = "none";
				Swal.fire({
					icon: "error",
					title: "Something went wrong!",
					text: error
				});
			});
	} catch (error) {
		console.log(error);
	}
}


/**
 * The `get_stores` function fetches store data from a server, populates a table with the retrieved
 * data, and handles errors gracefully.
 * @param [data] - The `get_stores` function is an asynchronous function that fetches store data from a
 * specified URL endpoint. It takes an optional `data` parameter which is an object containing any
 * additional data that needs to be sent with the request.
 */
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

			let table_data = data.data.map(item => {
				return [
					item.sto_id,
					item.sto_status,
					item.sto_name,
					item.us_name,
					item.sto_email,
					item.sto_qr,
					`<button class="btn btn-danger btn-sm" onclick="delete_store(${item.sto_id})"><i class="feather icon-trash-2"></i></button>
					<button class="btn btn-warning btn-sm" onclick="load_data_form('form_stores',${item.sto_id})"><i class="feather icon-edit"></i></button>`
				];
			});

			let columns = [
				{ title: 'id' },
				{ title: 'Status' },
				{ title: 'Store' },
				{ title: 'Owner' },
				{ title: 'Email' },
				{ title: 'QR Code' },
				{ title: "Actions" }
			];
			await paint_datatable('table_stores', columns, table_data);
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
