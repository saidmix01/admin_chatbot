const send_mail_contact = async (form_name = "") => {
	try {
        if(form_name == "") throw new Error("form name is empty");
		const { 
            name,
            email,
            message
         } = await get_elements_form(form_name);
		//Validate form fields
		if (name == "") throw new Error("name is required");
		if (email == "") throw new Error("email is required");
		if (message == "") throw new Error("message is required");

		let url_users = `${base_url}Page/send_message_contact`
		let data_send = { name, email, message }
		
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