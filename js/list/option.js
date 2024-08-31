window.addEventListener('load', async function () {
	await get_options({ lis_id: document.querySelector(`#lis_id`).value });
});


const save_option = async (us_id="") => {
	try {
		if(us_id == "") throw new Error("Empty paramn");
		const { opt_id,opt_status,opt_description,opt_price,opt_qty,opt_more_information, opt_order } = await get_elements_form('form_option');
		//Validate form fields
		if (opt_status == "") throw new Error("Status is required");
		if (opt_description == "") throw new Error("Description is required");
		let now = new Date();
		let opt_create_date = now.toISOString();
		const opt_id_input = document.querySelector(`#opt_id`).value;
		const lis_id = document.querySelector(`#lis_id`).value;
		let url_menu = `${base_url}List_manage/save_option`
		let data_send = { opt_status,opt_description,opt_price,opt_qty,opt_more_information,opt_create_date,lis_id,us_id,opt_order }
		if(opt_id_input != ""){
			url_menu = `${base_url}List_manage/update_option`
			data_send = { opt_id,opt_status,opt_description,opt_price,opt_qty,opt_more_information,opt_create_date,lis_id,us_id,opt_order }
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


const get_options = async (data = {}) => {
	document.querySelector('.loading').style.display = "flex";
	fetch(`${base_url}List_manage/get_options`, {
		method: 'POST',
		headers: { 'Content-Type': 'application/json' },
		body: JSON.stringify(data)
	})
		.then(response => response.json())
		.then(async data => {
			if (!data.status) throw new Error(data.message);

			let table_data = data.data.map(item => {
				return [
					item.opt_id,
					item.opt_order,
					item.opt_status,
					item.opt_description,
					item.opt_price,
					item.opt_qty,
					item.opt_more_information,
					`<button class="btn btn-danger btn-sm" onclick="delete_option(${item.opt_id})"><i class="feather icon-trash-2"></i></button>
					 <button class="btn btn-warning btn-sm" onclick="load_data_form('form_option',${item.opt_id})"><i class="feather icon-edit"></i></button>
					 `
				];
			});

			let columns = [
				{ title: 'id' },
				{ title: 'Order' },
				{ title: 'Status' },
				{ title: 'Description' },
				{ title: 'Price' },
				{ title: 'Quantity' },
				{ title: 'More Information' },
				{ title: "Actions" }
			];
			await paint_datatable('table_option', columns, table_data);
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

const load_data_form = async (name_form = "", opt_id = "") => {
	try {
		
		if (name_form == "") throw new Error("Form not found");
		if (opt_id == "") throw new Error("lis_id not found");
		document.querySelector('.loading').style.display = "flex";
		fetch(`${base_url}List_manage/get_options`, {
			method: 'POST',
			headers: { 'Content-Type': 'application/json' },
			body: JSON.stringify({ opt_id })
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
 * The function `delete_list` is an asynchronous function that sends a POST request to delete a list
 * item and displays success or error messages using SweetAlert.
 * @param [opt_id] - The `lis_id` parameter in the `delete_list` function is used to specify the ID of
 * the list that you want to delete. This ID is then sent as part of the request body to the server
 * when making a POST request to the `List_manage/delete` endpoint. The function also includes
 */
const delete_option = async (opt_id = "") => {
	try {
		if (opt_id == "") throw new Error("Empty field");
		document.querySelector('.loading').style.display = "flex";
		fetch(`${base_url}List_manage/delete_option`, {
			method: 'POST',
			headers: { 'Content-Type': 'application/json' },
			body: JSON.stringify({ opt_id })
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
