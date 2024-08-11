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
        xhr.onreadystatechange = function() {
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
        xhr.onerror = function() {
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
function get_elements_form(form_name="") {
    return new Promise((resolve, reject) => {
        const formObject = {};
        try {
            if (form_name == "") throw new Error('Form_name is empty');
            document.getElementById(form_name).addEventListener('submit', function(event) {
                event.preventDefault();
                const formData = new FormData(event.target);
                formData.forEach((value, key) => {
                    formObject[key] = value;
                });
                resolve(formObject); // Resuelve la promesa con los datos del formulario
            });
        } catch (error) {
            reject(error);
        }
    });
}
