window.addEventListener('load', async function () {
	await load_list({ lis_status: 1 });
	await get_questions({ chq_status: 1 });
	await get_questions_select( { chq_status: 1 } );
});


/**
 * The `save_question` function in JavaScript handles saving or updating a question with form
 * validation and error handling.
 * @param [us_id] - The `us_id` parameter in the `save_question` function is used to specify the user
 * ID. It is an optional parameter with a default value of an empty string. This parameter is passed to
 * the function to associate the question with a specific user when saving or updating the question
 * data. If no
 */
const save_question = async (us_id = "") => {
	try {
		if(us_id == "") throw new Error("Empty paramn");
		const { chq_parent,chq_text,chq_status,chq_order,chq_type } = await get_elements_form('form_question');
		//Validate form fields
		if (chq_parent == "") throw new Error("Parent is required");
		if (chq_text == "") throw new Error("Question is required");
		if (chq_status == "") throw new Error("Status is required");
		if (chq_order == "") throw new Error("Order is required");
		if (chq_type == "") throw new Error("Type is required");
		const chq_id = document.querySelector(`#chq_id`).value;
		let url_question = `${base_url}Chat_configuration/save`
		let data_send = { chq_parent,chq_text,chq_status,chq_order,chq_type,us_id }
		if(chq_id != ""){
			url_question = `${base_url}Chat_configuration/update`
			data_send = { chq_parent,chq_text,chq_status,chq_order,chq_type,us_id,chq_id }
		}
		document.querySelector('.loading').style.display = "flex";
		send_data(url_question, data_send)
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
 * The function `get_questions` fetches question data from a server, populates a table with the data,
 * and handles errors with a user-friendly message.
 * @param [data] - The `get_questions` function is an asynchronous function that fetches questions data
 * from a specific URL endpoint and then dynamically populates a table with the retrieved data. Here's
 * a breakdown of the function and its parameters:
 */
const get_questions = async (data = {}) => {
	document.querySelector('.loading').style.display = "flex";
	fetch(`${base_url}Chat_configuration/get_questions`, {
		method: 'POST',
		headers: { 'Content-Type': 'application/json' },
		body: JSON.stringify(data)
	})
		.then(response => response.json())
		.then(async data => {
			if (!data.status) throw new Error(data.message);

			let table_data = data.data.map(item => {
				return [
					item.chq_id,
					item.chq_order,
					item.chq_status,
					item.lis_name,
					item.chq_parent,
					item.chq_text,
					`<button class="btn btn-danger btn-sm" onclick="delete_question(${item.chq_id})"><i class="feather icon-trash-2"></i></button>
					<button class="btn btn-warning btn-sm" onclick="load_data_form('form_question',${item.chq_id})"><i class="feather icon-edit"></i></button>`
				];
			});

			let columns = [
				{ title: 'id' },
				{ title: 'Order' },
				{ title: 'Status' },
				{ title: 'List' },
				{ title: 'Parent' },
				{ title: 'Question' },
				{ title: "Actions" }
			];
			await paint_datatable('table_questions', columns, table_data);
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

const get_questions_select = async (data = {}) => {
	document.querySelector('.loading').style.display = "flex";
	fetch(`${base_url}Chat_configuration/get_questions`, {
		method: 'POST',
		headers: { 'Content-Type': 'application/json' },
		body: JSON.stringify(data)
	})
		.then(response => response.json())
		.then(async data => {
			if (!data.status) throw new Error(data.message);
				document.querySelector('.loading').style.display = "none";
				if (!data.status) throw new Error(data.message);
				let data_resp = [];
				data_resp.push({id:0,name:'Main question'});
				data.data.forEach(element => {
					data_resp.push({id:element.chq_id,name:element.chq_text});
				});
				if (data.status) {
					paint_select(data_resp,'chq_parent');
				} else {
					Swal.fire({
						icon: "error",
						title: "Opss...",
						text: `${data.message}`
					})
				}
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
 * The `load_list` function fetches data from a server, handles loading indicators, and displays error
 * messages if needed.
 * @param data - The `load_list` function is an asynchronous function that fetches a list of items from
 * a server endpoint and populates a select dropdown with the retrieved data. Here's a breakdown of the
 * function:
 */
const load_list = async (data) => {
	try {
		if(us_id == "") throw new Error("us_id is empty");
		document.querySelector('.loading').style.display = "flex";
		fetch(`${base_url}List_manage/get_lists`, {
			method: 'POST',
			headers: { 'Content-Type': 'application/json' },
			body: JSON.stringify(data)
		})
			.then(response => response.json())
			.then(async data => {
				document.querySelector('.loading').style.display = "none";
				if (!data.status) throw new Error(data.message);
				let data_resp = [];
				data_resp.push({id:0,name:'No List'});
				data.data.forEach(element => {
					data_resp.push({id:element.lis_id,name:element.lis_name});
				});
				if (data.status) {
					paint_select(data_resp,'chq_type');
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
	} catch (error) {
		document.querySelector('.loading').style.display = "none";
		console.log({error});
		Swal.fire({
			icon: "error",
			title: "Something went wrong!",
			text: error
		});
	}
}

/**
 * The function `load_data_form` fetches data for a form based on the form name and question ID,
 * handling errors and displaying loading indicators.
 * @param [name_form] - The `name_form` parameter in the `load_data_form` function is used to specify
 * the name of the form that you want to load data into. It is a string parameter that should contain
 * the name of the form you are working with. If the `name_form` parameter is not provided or
 * @param [chq_id] - The `chq_id` parameter in the `load_data_form` function is used to specify the ID
 * of the form data that you want to load. It is sent as part of the request body when fetching the
 * questions related to the form from the server. This ID helps in identifying the specific form
 */
const load_data_form = async (name_form = "", chq_id = "") => {
	try {
		
		if (name_form == "") throw new Error("Form not found");
		if (chq_id == "") throw new Error("chq_id not found");
		document.querySelector('.loading').style.display = "flex";
		fetch(`${base_url}Chat_configuration/get_questions`, {
			method: 'POST',
			headers: { 'Content-Type': 'application/json' },
			body: JSON.stringify({ chq_id })
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
 * reloading the page upon successful deletion.
 * @param [chq_id] - The `delete_question` function is an asynchronous function that deletes a question
 * based on the `chq_id` provided. The function first checks if the `chq_id` is empty, and if it is, it
 * throws an error with the message "Empty field".
 */
const delete_question = async (chq_id = "") => {
	try {
		if (chq_id == "") throw new Error("Empty field");
		document.querySelector('.loading').style.display = "flex";
		fetch(`${base_url}Chat_configuration/delete`, {
			method: 'POST',
			headers: { 'Content-Type': 'application/json' },
			body: JSON.stringify({ chq_id })
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

