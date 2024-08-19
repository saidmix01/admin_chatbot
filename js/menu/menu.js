window.addEventListener('load', async function() {
    await get_menus({men_status:1});
});

/**
 * The `save_menu` function in JavaScript handles saving menu data with form validation and error
 * handling.
 */
const save_menu = async () => {
	try {
		const { men_description, men_status, men_icon } = await get_elements_form('form_menu');
		//Validate form fields
		if (men_description == "") throw new Error("Name is required");
		if (men_status == "") throw new Error("Status is required");
		if (men_icon == "") throw new Error("Icon is required");
		let now = new Date();
		let men_create_date = now.toISOString();
		send_data(`${base_url}Menu/save`, { men_status, men_description, men_icon, men_create_date })
			.then(response => {
				if (response.status) {
					Swal.fire({
						icon: "success",
						title: "Success",
						text: response.message
					}).then(async (result) => {
						if (result.isConfirmed) {
							await get_menus({men_status:1})
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

const delete_menu = async (men_id = "") => {
	try {
		if(men_id == "") throw new Error("Empty field");
		
		fetch(`${base_url}Menu/delete`,{
			method:'POST',
			headers:{ 'Content-Type': 'application/json' },
			body:JSON.stringify({men_id})
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
							await get_menus({men_status:1})
						}
					});
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
		
	}
}


const get_menus = async (data = {}) => {
	fetch(`${base_url}Menu/get_menus`,{
		method:'POST',
		headers:{ 'Content-Type': 'application/json' },
		body:JSON.stringify(data)
	}) 
		.then(response => response.json())
		.then(async data => {
			if (!data.status) throw new Error(data.message);
			
			let table_data = data.data.map(item => {
				return [
					item.men_id,
					item.men_status,
					item.men_description,
					item.men_icon,
					`<button class="btn btn-danger btn-sm" onclick="delete_menu(${item.men_id})"><i class="feather icon-trash-2"></i></button>
					<button class="btn btn-warning btn-sm" onclick="load_info_edit(${item.men_id})"><i class="feather icon-edit"></i></button>`
				];
			});
			
			let columns = [
				{ title: 'id' },
				{ title: 'Status' },
				{ title: 'Name' },
				{ title: 'Icon' },
				{ title: "Actions" }
			];
			await paint_datatable('table_menus', columns, table_data);
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
