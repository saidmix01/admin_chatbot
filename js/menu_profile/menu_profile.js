window.addEventListener('load', async function () {
	await get_menus({ men_status: 1 })
});

/**
 * The function `get_menus` fetches menu data from a server, dynamically creates checkboxes based on
 * the data, and displays them in a container on the webpage.
 * @param [data] - The `data` parameter in the `get_menus` function is an object that contains the
 * information needed for the POST request to the `Access_profile/get_menus` endpoint. This
 * data is stringified using `JSON.stringify(data)` and sent in the body of the request.
 */
const get_menus = async (data = {}) => {
	document.querySelector('.loading').style.display = "flex";
	fetch(`${base_url}Access_profile/get_menus`, {
		method: 'POST',
		headers: { 'Content-Type': 'application/json' },
		body: JSON.stringify(data)
	})
		.then(response => response.json())
		.then(async data => {
			if (!data.status) throw new Error(data.message);
			const container = document.getElementById('checkbox_menus');
			container.innerHTML = '';
			const pro_id = document.getElementById('pro_id').value;
			data.data.forEach(async item => {
				const validation = await validate_menu_profile(item.men_id);
				console.log({validation});
				
				let val_chk = false;
				let val = '1';
				if (validation >= 1) {
					val_chk = true;
					val = '2';
				}
				const label = document.createElement('label');
				label.className = 'custom-control custom-checkbox';

				const checkbox = document.createElement('input');
				checkbox.type = 'checkbox';
				checkbox.className = 'custom-control-input';
				checkbox.id = `checkbox_${item.men_id}`;
				checkbox.setAttribute('men_id', `${item.men_id}`);
				checkbox.setAttribute('pro_id', `${pro_id}`);
				checkbox.name = `checkbox_${item.men_id}`
				checkbox.checked = val_chk;
				checkbox.value = val;

				const span = document.createElement('span');
				span.className = 'custom-control-label';
				span.textContent = item.men_description;

				label.appendChild(checkbox);
				label.appendChild(span);
				container.appendChild(label);
			});
			document.getElementById('btn_save_mp').style.display = "block";
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
 * The function `validate_menu_profile` asynchronously validates a menu profile by sending a POST
 * request with menu and profile IDs and handles any errors that occur.
 * @param [men_id] - The `men_id` parameter in the `validate_menu_profile` function represents the menu
 * ID that is being validated. It is used to specify which menu profile is being validated in the
 * function.
 * @returns The function `validate_menu_profile` is returning the `data.data` object if the response
 * status is true. If there is an error during the process, it will log the error to the console,
 * display an error message using SweetAlert (Swal.fire), and then re-throw the error for further
 * handling by the calling function.
 */
const validate_menu_profile = async (men_id = "") => {
	try {
		if (!document.getElementById('pro_id')) throw new Error("pro_id is invalid");
		const pro_id = document.getElementById('pro_id').value;
		const response = await fetch(`${base_url}Access_profile/validate_menu_profile`, {
			method: 'POST',
			headers: { 'Content-Type': 'application/json' },
			body: JSON.stringify({ men_id, pro_id })
		});
		const data = await response.json();
		if (!data.status) throw new Error(data.message);
		return data.data;
	} catch (error) {
		console.log(error);
		Swal.fire({
			icon: "error",
			title: "Something went wrong!",
			text: error.message
		});
		throw error; // Re-lanzar el error para que la función que llama pueda manejarlo si es necesario
	}
};

const save_men_prof = async () => {
	try {
		document.querySelector('.loading').style.display = "flex";
		const data_form = await get_elements_form('form_menu_profiles');
		//Validate form fields
		const array_send = [];
		Object.keys(data_form).forEach(key => {
			let now = new Date();
			let men_pro_create_date = now.toISOString();
			const chk = document.getElementById(key);
			let action = "save";
			if(data_form[key] == ''){
				action = 'delete';
			}
			array_send.push({
				men_id:chk.getAttribute('men_id'),
				pro_id:chk.getAttribute('pro_id'),
				action,
				men_pro_create_date
			})
		});
		fetch(`${base_url}Access_profile/save_delete`, {
			method: 'POST',
			headers: { 'Content-Type': 'application/json' },
			body: JSON.stringify(array_send)
		})
			.then(response => response.json())
			.then(async data => {
				if (!data.status) throw new Error(data.message);
				Swal.fire({
					icon: "success",
					title: "Success",
					text: data.message
				}).then(async (result) => {
					if (result.isConfirmed) {
						location.reload();
					}
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
