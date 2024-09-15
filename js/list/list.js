window.addEventListener('load', async function () {
	await get_list({ lis_status: 1 });
});

/**
 * The `save_list` function in JavaScript handles saving or updating a list with form data and displays
 * success or error messages accordingly.
 * @param [us_id] - The `us_id` parameter in the `save_list` function represents the user ID. It is
 * used to identify the user for whom the list is being saved or updated. If no `us_id` is provided
 * (default is an empty string), an error will be thrown indicating that the parameter is
 */
const save_list = async (us_id="") => {
	try {
		if(us_id == "") throw new Error("Empty paramn");
		const { lis_name, lis_status, lis_id } = await get_elements_form('form_list');
		//Validate form fields
		if (lis_name == "") throw new Error("Name is required");
		if (lis_status == "") throw new Error("Status is required");
		let now = new Date();
		let lis_create_date = now.toISOString();
		const list_id_input = document.querySelector(`#lis_id`).value;
		let url_menu = `${base_url}List_manage/save`
		let data_send = { lis_name, lis_status, us_id, lis_create_date }
		if(list_id_input != ""){
			url_menu = `${base_url}List_manage/update`
			data_send = { lis_name, lis_status, us_id, lis_create_date, lis_id }
		}
		document.querySelector('.loading').style.display = "flex";
		send_data(url_menu, data_send)
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
 * The function `get_list` fetches data from a server, processes it, and displays it in a datatable on
 * a web page.
 * @param [data] - The `data` parameter in the `get_list` function is an object that contains the
 * information needed for the API request. This data will be sent in the body of the POST request to
 * the specified endpoint (`List_manage/get_lists`). The function then processes the
 * response data to populate
 */
const get_list = async (data = {}) => {
	document.querySelector('.loading').style.display = "flex";
	fetch(`${base_url}List_manage/get_lists`, {
		method: 'POST',
		headers: { 'Content-Type': 'application/json' },
		body: JSON.stringify(data)
	})
		.then(response => response.json())
		.then(async data => {
			if (!data.status) throw new Error(data.message);

			let table_data = data.data.map(item => {
				return [
					item.lis_id,
					format_status(item.lis_status),
					item.lis_name,
					`<div style="display: flex;"><button class="btn btn-danger btn-sm" onclick="delete_list(${item.lis_id})"><i class="feather icon-trash-2"></i></button>
					 <button class="btn btn-warning btn-sm" onclick="load_data_form('form_list',${item.lis_id})"><i class="feather icon-edit"></i></button>
					 <form action="${base_url}List_manage/options_list" method="POST">
						<input type="hidden" name="lis_id" value="${item.lis_id}">
						<button class="btn btn-info btn-sm"><i class="feather icon-file-plus"></i></button>
					</form></div>`
				];
			});

			let columns = [
				{ title: 'id' },
				{ title: 'Status' },
				{ title: 'Name' },
				{ title: "Actions" }
			];
			await paint_datatable('table_list', columns, table_data);
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

/**
 * The function `load_data_form` fetches data for a form based on the provided form name and list ID,
 * handling errors and displaying loading indicators.
 * @param [name_form] - The `name_form` parameter in the `load_data_form` function is used to specify
 * the name of the form that needs to be loaded. It is a string parameter that should contain the name
 * of the form you want to load data into. If the `name_form` parameter is not provided or
 * @param [lis_id] - The `lis_id` parameter in the `load_data_form` function is used to specify the ID
 * of the form data that needs to be loaded. It is sent as part of the request body when fetching data
 * from the server.
 */
const load_data_form = async (name_form = "", lis_id = "") => {
	try {
		
		if (name_form == "") throw new Error("Form not found");
		if (lis_id == "") throw new Error("lis_id not found");
		document.querySelector('.loading').style.display = "flex";
		fetch(`${base_url}List_manage/get_lists`, {
			method: 'POST',
			headers: { 'Content-Type': 'application/json' },
			body: JSON.stringify({ lis_id: lis_id })
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
 * The `delete_menu` function sends a POST request to delete a menu item and displays success or error
 * messages accordingly.
 * @param [lis_id] - The `lis_id` parameter in the `delete_menu` function represents the ID of the list
 * item that you want to delete. This function is an asynchronous function that sends a POST request to
 * the server to delete the specified list item based on the `lis_id` provided.
 */
const delete_list = async (lis_id = "") => {
	try {
		if (lis_id == "") throw new Error("Empty field");
		document.querySelector('.loading').style.display = "flex";
		fetch(`${base_url}List_manage/delete`, {
			method: 'POST',
			headers: { 'Content-Type': 'application/json' },
			body: JSON.stringify({ lis_id })
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
