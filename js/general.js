/**
 * The `send_data` function is a global function in JavaScript that sends data to a PHP controller
 * using a POST request and returns a Promise to handle the response.
 * @param url - The `url` parameter in the `send_data` function is the endpoint URL of the PHP
 * controller where you want to send the data. This URL should be the address of the server-side script
 * that will handle the data sent from the client-side JavaScript code.
 * @param data - The `data` parameter in the `send_data` function is the data that you want to send to
 * the PHP controller. This data should be in the form of a JavaScript object that you want to send as
 * JSON to the server. For example, if you want to send user information like name,
 * @returns A Promise is being returned from the `send_data` function.
 */
function send_data(url, data) {
	return new Promise((resolve, reject) => {
		// Create a new XMLHttpRequest instance
		const xhr = new XMLHttpRequest();
		// Configure the POST request
		xhr.open('POST', url, true);
		// No es necesario establecer el Content-Type para FormData, el navegador lo hace automáticamente
		// Handle the request response
		xhr.onreadystatechange = function () {
			if (xhr.readyState === 4) { // La petición ha finalizado
				if (xhr.status >= 200 && xhr.status < 300) {
					// The request was successful
					resolve(JSON.parse(xhr.responseText));
				} else {
					// The request failed
					reject({
						status: xhr.status,
						statusText: xhr.statusText
					});
				}
			}
		};
		// Handle request errors
		xhr.onerror = function () {
			reject({
				status: xhr.status,
				statusText: xhr.statusText
			});
		};
		// Create FormData from the data object
		const formData = new FormData();
		for (const key in data) {
			if (data.hasOwnProperty(key)) {
				formData.append(key, data[key]);
			}
		}
		// Send the data as FormData
		xhr.send(formData);
	});
}

/**
 * The function `get_elements_form` retrieves form data from a specified form element on submission.
 * @param [form_name] - The `form_name` parameter in the `get_elements_form` function is used to
 * specify the name of the form element in the HTML document that you want to extract data from.
 * @returns An empty object `{}` is being returned.
 */
function get_elements_form(form_name = "") {
	return new Promise((resolve, reject) => {
		const formObject = {};
		try {
			if (form_name == "") throw new Error('Form_name is empty');
			document.getElementById(form_name).addEventListener('submit', function (event) {
				event.preventDefault();
				const formData = new FormData(event.target);
				formData.forEach((value, key) => {
					formObject[key] = value;
				});
				resolve(formObject); // Resuelve la promesa con los datos del formulario
			});
		} catch (error) {
			console.log('error');

			reject(error);
		}
	});
}

const paint_datatable = async (table_name, columns, data) => {
	$(`#${table_name}`).DataTable({
		data: data,
		columns: columns,
		paging: true,
		dom: '<"top"f>rt<"bottom"lp><"clear">',
		language: {
			search: "Buscar:",
			lengthMenu: "Mostrar _MENU_ registros por página",
			zeroRecords: "No se encontraron resultados",
			info: "Mostrando página _PAGE_ de _PAGES_",
			infoEmpty: "No hay registros disponibles",
			infoFiltered: "(filtrado de _MAX_ registros totales)",
			paginate: {
				first: "Primero",
				last: "Último",
				next: "Siguiente",
				previous: "Anterior"
			}
		}
	});
}

function load_form_data(data, name_form) {
	const form = document.querySelector(`#${name_form}`);
	const elements = form.querySelectorAll('input, select, textarea');

	elements.forEach(e => {
		const name = e.name;

		if (data.hasOwnProperty(name)) {
			switch (e.type) {
				case 'checkbox':
				case 'radio':
					e.checked = data[name];
					break;
				case 'select-one':
					e.value = data[name];
					break;
				default:
					e.value = data[name];
			}
		}
	});
}

/**
 * The function "pintarOpciones" populates a select element with options based on the data provided.
 * @param data - An array of objects containing information about the options to be displayed in the
 * select element. Each object should have properties like `id` and `name` to populate the `value` and
 * `textContent` of the option elements respectively.
 * @param select_id - The `select_id` parameter is the id of the HTML select element where you want to
 * dynamically populate options based on the data provided.
 */
function paint_select(data, select_id) {
    const element = document.getElementById(select_id);
    element.innerHTML = '';
	const opt_default = document.createElement('option');
	opt_default.value = '';
	opt_default.textContent = 'Select something';
	element.appendChild(opt_default);
    data.forEach(e => {
        // Crear un nuevo elemento option
        const optionElement = document.createElement('option');
        optionElement.value = e.id; 
        optionElement.textContent = e.name;
        element.appendChild(optionElement);
    });
}
