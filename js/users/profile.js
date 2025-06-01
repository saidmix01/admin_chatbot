window.addEventListener('load', async function () {
    await load_data_form('form_user_profile', us_id_saved);
});


const save_user = async () => {
    try {
        const {
            us_id,
            us_name,
            us_password,
            us_password_confirm,
            us_domain,
            us_country_code,
            us_tel,
            us_address,
            us_country,
            us_city,
            us_region
        } = await get_elements_form('form_user_profile');
        //Validate form fields
        if (us_domain == "") throw new Error("us_domain is required");
        if (us_name == "") throw new Error("Name is required");
        if (us_country_code == "") throw new Error("us_country_code is required");
        if (us_tel == "") throw new Error("us_tel is required");
        if (us_password == "") throw new Error("Password is required");
        if (us_password_confirm == "") throw new Error("Confirm Password is required");
        if (us_password != us_password_confirm) throw new Error("The passwords dont match");
        if (us_address == "") throw new Error("us_address is required");
        if (us_country == "") throw new Error("us_country is required");
        if (us_city == "") throw new Error("us_city is required");
        if (us_region == "") throw new Error("us_region is required");

        const us_id_input = document.querySelector(`#us_id`).value;
        if (us_id_input != "") {
            url_users = `${base_url}Users/update`
            data_send = { us_name, us_password, us_id, us_country_code, us_domain, us_tel, us_address, us_country, us_city, us_region }
        }
        document.querySelector('.loading').style.display = "flex";
        send_data(url_users, data_send)
            .then(response => {
                document.querySelector('.loading').style.display = "none";
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


const load_data_form = async (name_form = "", us_id = "") => {
    try {
        if (name_form == "") throw new Error("Form not found");
        if (us_id == "") throw new Error("men_id not found");
        document.querySelector('.loading').style.display = "flex";
        fetch(`${base_url}Users/get_users`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ us_id: us_id })
        })
            .then(response => response.json())
            .then(async data => {
                if (!data.status) throw new Error(data.message);
                load_form_data(data.data[0], name_form, ['us_password', 'us_password_confirm']);
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