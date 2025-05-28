window.addEventListener('load', async function () {
	this.document.getElementById('cupon').style.display = 'none';
});

function enable_cupon_label() {
	const cupon_code = document.getElementById('cupon_code');
	const cupon = document.getElementById('cupon');

	if (cupon_code.checked) {
		cupon.style.display = 'inline';
	} else {
		cupon.style.display = 'none';
	}
}

const send_checkout = async (form_checkout) => {
	try {
		const { 
			us_name, 
			us_email,
			us_tel,
			us_domain,
			us_comments,
			ser_id,
			us_doc_type,
			us_doc_number,
			us_address,
			us_country,
			us_city,
			us_region,
			us_country_code
		} = await get_elements_form(form_checkout);
		//Validate form fields
		if (us_name == "") throw new Error("Name is required");
		if (us_email == "") throw new Error("Email is required");
		if (us_tel == "") throw new Error("Phone number is required");
		if (us_doc_type == "") throw new Error("doc type is required");
		if (us_doc_number == "") throw new Error("doc_number is required");
		if (us_address == "") throw new Error("address is required");
		if (us_country == "") throw new Error("country is required");
		if (us_city == "") throw new Error("city is required");
		if (us_region == "") throw new Error("region is required");
		if (us_country_code == "") throw new Error("us_country_code is required");
		let now = new Date();
		let us_create_date = now.toISOString();

		let url_users = `${base_url}Checkout/shop_service`
		let data_send = { 
			ser_id, 
			us_name, 
			us_email, 
			us_create_date, 
			us_domain, 
			us_comments, 
			us_tel, 
			us_doc_type,
			us_doc_number,
			us_address,
			us_country,
			us_city,
			us_region,
			us_country_code
		}

		send_data(url_users, data_send)
			.then(response => {
				if (response.status) {
					if (response.redirect) {
						// Redirigir a Wompi si se requiere
						window.location.href = response.redirect;
						return;
					}else{
						Swal.fire({
							icon: "success",
							title: "Success",
							text: response.message
						}).then(async (result) => {
							if (result.isConfirmed) {
								//location.reload();
							}
						});
					}
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