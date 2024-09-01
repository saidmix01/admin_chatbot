window.addEventListener('load', async function () {
	await get_menus({ men_status: 1 });
});

/**
 * The `save_menu` function in JavaScript handles saving menu data with form validation and error
 * handling.
 */
const save_menu = async () => {
	try {
		document.querySelector('.loading').style.display = "flex";
		const { men_description, men_status, men_icon,men_id,men_url } = await get_elements_form('form_menu');
		//Validate form fields
		if (men_description == "") throw new Error("Name is required");
		if (men_status == "") throw new Error("Status is required");
		if (men_icon == "") throw new Error("Icon is required");
		if (men_url == "") throw new Error("Url is required");
		let now = new Date();
		let men_create_date = now.toISOString();
		const men_id_input = document.querySelector(`#men_id`).value;
		let url_menu = `${base_url}Menu/save`
		let data_send = { men_status, men_description, men_icon, men_create_date,men_url }
		if(men_id_input != ""){
			url_menu = `${base_url}Menu/update`
			data_send = { men_status, men_description, men_icon, men_create_date,men_id,men_url  }
		}
		send_data(url_menu, data_send)
			.then(response => {
				document.querySelector('.loading').style.display = "none";
				if (response.status) {
					Swal.fire({
						icon: "success",
						title: "Success",
						text: response.message
					}).then(async (result) => {
						document.querySelector('.loading').style.display = "none";
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
 * The `delete_menu` function sends a POST request to delete a menu item and displays a success or
 * error message using SweetAlert.
 * @param [men_id] - The `men_id` parameter in the `delete_menu` function represents the ID of the menu
 * item that you want to delete. This ID is used to identify the specific menu item that needs to be
 * deleted from the system.
 */
const delete_menu = async (men_id = "") => {
	try {
		if (men_id == "") throw new Error("Empty field");
		document.querySelector('.loading').style.display = "flex";
		fetch(`${base_url}Menu/delete`, {
			method: 'POST',
			headers: { 'Content-Type': 'application/json' },
			body: JSON.stringify({ men_id })
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
 * The `get_menus` function fetches menu data from a server, processes it, and displays it in a
 * datatable with options for editing and deleting.
 * @param [data] - The `data` parameter in the `get_menus` function is an object that contains any
 * additional data that needs to be sent along with the fetch request to the specified URL
 * `Menu/get_menus`. This data is stringified using `JSON.stringify(data)` and included in
 * the body of the
 */
const get_menus = async (data = {}) => {
	document.querySelector('.loading').style.display = "flex";
	fetch(`${base_url}Menu/get_menus`, {
		method: 'POST',
		headers: { 'Content-Type': 'application/json' },
		body: JSON.stringify(data)
	})
		.then(response => response.json())
		.then(async data => {
			if (!data.status) throw new Error(data.message);

			let table_data = data.data.map(item => {
				return [
					item.men_id,
					item.men_status,
					item.men_description,
					item.men_url,
					item.men_icon,
					`<button class="btn btn-danger btn-sm" onclick="delete_menu(${item.men_id})"><i class="feather icon-trash-2"></i></button>
					<button class="btn btn-warning btn-sm" onclick="load_data_form('form_menu',${item.men_id})"><i class="feather icon-edit"></i></button>`
				];
			});

			let columns = [
				{ title: 'id' },
				{ title: 'Status' },
				{ title: 'Name' },
				{ title: 'Url' },
				{ title: 'Icon' },
				{ title: "Actions" }
			];
			await paint_datatable('table_menus', columns, table_data);
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
 * The function `load_data_form` fetches menu data based on a given form name and menu ID, then updates
 * the form and button accordingly.
 * @param [name_form] - The `name_form` parameter in the `load_data_form` function is used to specify
 * the name of the form that needs to be loaded. It is a required parameter and if not provided, an
 * error "Form not found" will be thrown.
 * @param [men_id] - The `men_id` parameter in the `load_data_form` function seems to represent the ID
 * of a menu. This ID is used to fetch menu data from the server endpoint `Menu/get_menus`
 * in the POST request body.
 */
const load_data_form = async (name_form = "", men_id = "") => {
	try {
		
		if (name_form == "") throw new Error("Form not found");
		if (men_id == "") throw new Error("men_id not found");
		document.querySelector('.loading').style.display = "flex";
		fetch(`${base_url}Menu/get_menus`, {
			method: 'POST',
			headers: { 'Content-Type': 'application/json' },
			body: JSON.stringify({ men_id })
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
