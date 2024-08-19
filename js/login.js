async function login(name_form="") {
    try {
        if (name_form == "") throw new Error('Form data is empty');
        const form_inf = await get_elements_form(name_form);
        const {us_email, us_password} = form_inf;
        if(us_email == "") throw new Error("Email is required");
		if(us_password == "") throw new Error("Password is required");
		send_data(`${base_url}login/login`,{us_email,us_password})
		.then(response => {
			if(response.status){
				Swal.fire({
					icon: "success",
					title: "Login success",
					text: ``
				  }).then((result) => {
					if (result.isConfirmed) {
					  location.href = `${base_url}Home/`;
					}
				  });
			}else{
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
        Swal.fire({
			icon: "error",
			title: "Something went wrong!",
			text: error
		  });
    }
}
