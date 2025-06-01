function open_modal(id="",action="show"){
	if(id != ""){
		$(`#${id}`).modal(`${action}`);
	}
}

function go_checkout(service = ""){
	try {
		if (service == "") throw new Error("service is empty");
		location.href = `${base_url}checkout?service=${service}`;
	} catch (error) {
		console.log(error);
	}
}

function get_elements_form(form_name = "") {
	return new Promise((resolve, reject) => {
		const formObject = {};
		try {
			console.log(form_name);
			
			if (form_name == "") throw new Error('Form_name is empty');
				
				document.getElementById(form_name).addEventListener('submit', function (event) {

				event.preventDefault();
				
				const formData = new FormData(event.target);
				
				formData.forEach((value, key) => {
					if (value == undefined) throw new Error(key+' is empty');
					formObject[key] = value;
				});
				const checkboxes = event.target.querySelectorAll('input[type="checkbox"]');
				checkboxes.forEach(checkbox => {
					if (!formObject.hasOwnProperty(checkbox.name)) {
						formObject[checkbox.name] = checkbox.checked ? checkbox.value : '';
					}
				});

				resolve(formObject);
			});
		} catch (error) {
			console.log(error);
			reject(error);
		}
	});
}

function get_elements_form_sync(form_name = "") {
	const formObject = {};

	if (form_name === "") {
		console.error('Form name is empty');
		return formObject;
	}

	const form = document.getElementById(form_name);
	if (!form) {
		console.error('Form not found');
		return formObject;
	}

	const formData = new FormData(form);

	formData.forEach((value, key) => {
		if (value === undefined) {
			console.error(`${key} is undefined`);
		}
		formObject[key] = value;
	});

	// Asegurar que se tomen los checkboxes no seleccionados
	const checkboxes = form.querySelectorAll('input[type="checkbox"]');
	checkboxes.forEach(checkbox => {
		if (!formObject.hasOwnProperty(checkbox.name)) {
			formObject[checkbox.name] = checkbox.checked ? checkbox.value : '';
		}
	});

	return formObject;
}