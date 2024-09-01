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
