window.addEventListener('load', async function () {
	await get_all_chats('total_chat',"1,2,3,4");
});

/**
 * The function `show_modal_store_info` displays a modal with store information after loading data
 * based on the provided store and user IDs.
 * @param [sto_id] - The `sto_id` parameter in the `show_modal_store_info` function represents the ID
 * of a store. It is used to load data for a specific store in a modal window.
 * @param [us_id] - The `us_id` parameter in the `show_modal_store_info` function likely represents the
 * user ID, which is used to identify a specific user in the system. This parameter is used to load
 * data related to a specific store for a given user.
 */
const show_modal_store_info = async (sto_id = "", us_id = "") => {
	try {
		if(sto_id == "" || us_id == "") throw new Error("Unknown data ");
		await load_data_form('form_store_modal',sto_id);
	} catch (error) {
		Swal.fire({
			icon: "error",
			title: "Something went wrong!",
			text: error
		});
	}
}

/**
 * The function `chats_view` opens a URL with parameters `sto_id` and `us_id` if they are not empty.
 * @param [sto_id] - The `sto_id` parameter in the `chats_view` function represents the ID of a store.
 * @param [us_id] - The `us_id` parameter in the `chats_view` function likely represents the user ID,
 * which is used to identify a specific user in the context of viewing chats.
 */
function chats_view(sto_id = "", us_id = ""){
	try {
		if(sto_id == "" || us_id == ""){
			throw new Error("Empty params");
		}
		open_url(`${base_url}Stores/my_chats?id=${sto_id}`);
	} catch (error) {
		
	}
}



/**
 * The function `update` is an asynchronous function that handles form submission, validates form
 * fields, sends data to a server, and displays success or error messages accordingly.
 */
const update = async () => {
	try {
		const { sto_id, sto_name, sto_email, sto_direction, sto_phone, sto_wellcome_message } = await get_elements_form('form_store_modal');
		//Validate form fields
		if (sto_name == "") throw new Error("Name is required");
		if (sto_email == "") throw new Error("Email is required");
		if (sto_phone == "") throw new Error("Phone is required");
		if (sto_wellcome_message == "") throw new Error("Wellcome message is required");
		let url_store = `${base_url}Stores/update`
		let data_send = { sto_id, sto_name, sto_email, sto_direction, sto_phone, sto_wellcome_message }
		document.querySelector('.loading').style.display = "flex";
		send_data(url_store, data_send)
			.then(response => {
				if (response.status) {
					document.querySelector('.loading').style.display = "none";
					$('#update_my_store_info').modal('hide');
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
 * The function `load_data_form` fetches store data based on the provided store ID and loads it into a
 * specified form, displaying an error message if any issues occur.
 * @param [name_form] - The `name_form` parameter in the `load_data_form` function is used to specify
 * the name of the form that needs to be loaded. If the `name_form` parameter is not provided or is an
 * empty string, an error "Form not found" will be thrown.
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
				document.querySelector('.loading').style.display = "none";
				$('#update_my_store_info').modal('show');
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
 * The function `get_chats` fetches chat data based on store status and updates the specified HTML
 * element with the number of chats retrieved.
 * @param id_content - The `id_content` parameter in the `get_chats` function is used to specify the ID
 * of the HTML element where the number of chats will be displayed.
 * @param status - The `status` parameter in the `get_chats` function is used to filter the chats based
 * on their status. It is passed as a parameter to the `fetch` request to retrieve chats with a
 * specific status from the server.
 */
const get_all_chats = async(id_content,status) =>{
	document.querySelector('.loading').style.display = "flex";
	fetch(`${base_url}Stores/get_chats_by_store`, {
		method: 'POST',
		headers: { 'Content-Type': 'application/json' },
		body: JSON.stringify({us_id:us_id_view, st_status: status})
	})
		.then(response => response.json())
		.then(async data => {
			document.querySelector('.loading').style.display = "none";
			if (!data.status) throw new Error(data.message);
			
			if (data.status) {
				document.getElementById(id_content).innerHTML = Object.keys(data.data).length;
				console.log(Object.keys(data.data).length);
				
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
