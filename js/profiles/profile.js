window.addEventListener('load', async function () {
	await get_profiles({ pro_status: 1 });
});

/**
 * The `get_profiles` function fetches profile data, processes it, and displays it in a datatable with
 * options for editing and deleting profiles.
 * @param [data] - The `get_profiles` function is an asynchronous function that sends a POST request to
 * a specific endpoint (`Profiles/get_profiles`) with the provided data. Upon receiving a
 * response, it processes the data and dynamically generates a table with the retrieved information.
 */
const get_profiles = async (data = {}) => {
	document.querySelector('.loading').style.display = "flex";
	fetch(`${base_url}Profiles/get_profiles`, {
		method: 'POST',
		headers: { 'Content-Type': 'application/json' },
		body: JSON.stringify(data)
	})
		.then(response => response.json())
		.then(async data => {
			if (!data.status) throw new Error(data.message);

			let table_data = data.data.map(item => {
				return [
					item.pro_id,
					format_status(item.pro_status),
					item.pro_description,
					`<div style="display: flex;"><button class="btn btn-danger btn-sm" onclick="delete_profile(${item.pro_id})"><i class="feather icon-trash-2"></i></button>
					<button class="btn btn-warning btn-sm" onclick="load_data_form('form_profile',${item.pro_id})"><i class="feather icon-edit"></i></button>
					<form action="${base_url}Access_profile/" method="POST">
						<input type="hidden" name="pro_id" id="pro_id" value="${item.pro_id}">
						<button class="btn btn-info btn-sm"><i class="feather icon-lock"></i></button>
					</form></div>`
				];
			});

			let columns = [
				{ title: 'id' },
				{ title: 'Status' },
				{ title: 'Profile' },
				{ title: "Actions" }
			];
			await paint_datatable('table_profiles', columns, table_data);
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
 * The `save_menu` function handles saving or updating profile data with form validation and error
 * handling.
 */
const save_profile = async () => {
	try {
		document.querySelector('.loading').style.display = "flex";
		const { pro_id, pro_status, pro_description } = await get_elements_form('form_profile');
		//Validate form fields
		if (pro_description == "") throw new Error("Profile is required");
		if (pro_status == "") throw new Error("Status is required");
		let now = new Date();
		let pro_create_date = now.toISOString();
		const pro_id_input = document.querySelector(`#pro_id`).value;
		let url_profile = `${base_url}Profiles/save`
		let data_send = { pro_status, pro_description, pro_create_date: pro_create_date }
		if(pro_id_input != ""){
			url_profile = `${base_url}Profiles/update`
			data_send = { pro_status, pro_description, pro_create_date: pro_create_date, pro_id  }
		}
		send_data(url_profile, data_send)
			.then(response => {
				document.querySelector('.loading').style.display = "none";
				if (response.status) {
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
					Swal.fire({
						icon: "error",
						title: "Opss...",
						text: `${response.message}`
					})
				}
			})
			.catch(error => {
				console.error('Error:', error);
				throw new Error(error);
			});
	} catch (error) {
		document.querySelector('.loading').style.display = "none";
		console.log(error);
		Swal.fire({
			icon: "error",
			title: "Something went wrong!",
			text: error
		});
	}
}
/**
 * The function `delete_profile` is an asynchronous function that sends a POST request to delete a
 * profile based on the provided profile ID, displaying success or error messages using SweetAlert and
 * reloading the page upon successful deletion.
 * @param [pro_id] - The `delete_profile` function is an asynchronous function that sends a POST
 * request to delete a profile based on the `pro_id` provided. The `pro_id` parameter is the ID of the
 * profile that needs to be deleted.
 */
const delete_profile = async (pro_id = "") => {
	try {
		document.querySelector('.loading').style.display = "flex";
		if (pro_id == "") throw new Error("Empty field");

		fetch(`${base_url}Profiles/delete`, {
			method: 'POST',
			headers: { 'Content-Type': 'application/json' },
			body: JSON.stringify({ pro_id: pro_id })
		})
			.then(response => response.json())
			.then(async data => {
				if (!data.status) throw new Error(data.message);
				if (data.status) {
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
		document.querySelector('.loading').style.display = "none";
	}
}

/**
 * The function `load_data_form` fetches profile data based on a given form name and profile ID, then
 * updates the form with the retrieved data and toggles the save/update button's appearance.
 * @param [name_form] - The `name_form` parameter in the `load_data_form` function is used to specify
 * the name of the form that needs to be loaded. It is a required parameter and if not provided or an
 * empty string is passed, an error "Form not found" will be thrown.
 * @param [pro_id] - The `pro_id` parameter in the `load_data_form` function is used to specify the
 * profile ID for which the data needs to be loaded. This ID is then sent as part of the request to
 * fetch the profile data from the server.
 */
const load_data_form = async (name_form = "", pro_id = "") => {
	try {
		document.querySelector('.loading').style.display = "flex";
		if (name_form == "") throw new Error("Form not found");
		if (pro_id == "") throw new Error("pro_id not found");

		fetch(`${base_url}Profiles/get_profiles`, {
			method: 'POST',
			headers: { 'Content-Type': 'application/json' },
			body: JSON.stringify({ pro_id })
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
			})
			.catch(error => {
				console.log(error);
				Swal.fire({
					icon: "error",
					title: "Something went wrong!",
					text: error
				});
			});
			document.querySelector('.loading').style.display = "none";
	} catch (error) {
		document.querySelector('.loading').style.display = "none";
		Swal.fire({
			icon: "error",
			title: "Something went wrong!",
			text: error
		});
	}
}
