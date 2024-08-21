window.addEventListener('load', async function () {
	await load_profiles();
	await get_users({us_status:1});
});

/**
 * The function `load_profiles` fetches user profiles data from a server, processes the response, and
 * displays it in a select dropdown or shows an error message if there is an issue.
 */
const load_profiles = async () => {
	try {
		fetch(`${base_url}Users/load_profiles`, {
			method: 'POST',
			headers: { 'Content-Type': 'application/json' }
		})
			.then(response => response.json())
			.then(async data => {
				if (!data.status) throw new Error(data.message);
				let data_resp = [];
				data.data.forEach(element => {
					data_resp.push({id:element.pro_id,name:element.pro_description});
				});
				if (data.status) {
					paint_select(data_resp,'pro_id');
				} else {
					Swal.fire({
						icon: "error",
						title: "Opss...",
						text: `${data.message}`
					})
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
	} catch (error) {
		console.log({error});
		Swal.fire({
			icon: "error",
			title: "Something went wrong!",
			text: error
		});
	}
}

const get_users = async (data = {}) => {
	fetch(`${base_url}Users/get_users`, {
		method: 'POST',
		headers: { 'Content-Type': 'application/json' },
		body: JSON.stringify(data)
	})
		.then(response => response.json())
		.then(async data => {
			if (!data.status) throw new Error(data.message);

			let table_data = data.data.map(item => {
				return [
					item.us_id,
					item.us_status,
					item.us_name,
					item.us_email,
					item.Profile,
					`<button class="btn btn-danger btn-sm" onclick="delete_menu(${item.men_id})"><i class="feather icon-trash-2"></i></button>
					<button class="btn btn-warning btn-sm" onclick="load_data_form('form_menu',${item.men_id})"><i class="feather icon-edit"></i></button>`
				];
			});

			let columns = [
				{ title: 'id' },
				{ title: 'Status' },
				{ title: 'Name' },
				{ title: 'Email' },
				{ title: 'Profile' },
				{ title: "Actions" }
			];
			await paint_datatable('table_users', columns, table_data);
		})
		.catch(error => {
			console.log(error);
			Swal.fire({
				icon: "error",
				title: "Something went wrong!",
				text: error
			});
		});
}

/**
 * The function `save_user` is an asynchronous function that handles form submission, validates form
 * fields, and sends data to the server for saving or updating user information, displaying success or
 * error messages accordingly.
 */
const save_user = async () => {
	try {
		const { us_id, us_status, us_name,us_email,pro_id,us_password,us_password_confirm } = await get_elements_form('form_user');
		//Validate form fields
		if (us_status == "") throw new Error("Status is required");
		if (us_name == "") throw new Error("Name is required");
		if (us_email == "") throw new Error("Email is required");
		if (pro_id == "") throw new Error("Profile is required");
		if (us_password == "") throw new Error("Password is required");
		if (us_password_confirm == "") throw new Error("Confirm Password is required");
		if (us_password != us_password_confirm) throw new Error("The passwords dont match");

		let now = new Date();
		let us_create_date = now.toISOString();
		const us_id_input = document.querySelector(`#us_id`).value;
		let url_users = `${base_url}Users/save`
		let data_send = { us_status, us_name, us_email, pro_id, us_password,us_create_date }
		if(us_id_input != ""){
			url_users = `${base_url}Users/update`
			data_send = { us_status, us_name, us_email, pro_id, us_password, us_id  }
		}
		send_data(url_users, data_send)
			.then(response => {
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
		console.log(error);
		Swal.fire({
			icon: "error",
			title: "Something went wrong!",
			text: error
		});
	}
}

const load_data_form = async (name_form = "", us_id = "") => {
	try {
		if (name_form == "") throw new Error("Form not found");
		if (us_id == "") throw new Error("men_id not found");

		fetch(`${base_url}Users/get_users`, {
			method: 'POST',
			headers: { 'Content-Type': 'application/json' },
			body: JSON.stringify({ us_id: us_id })
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
	} catch (error) {
		Swal.fire({
			icon: "error",
			title: "Something went wrong!",
			text: error
		});
	}
}
