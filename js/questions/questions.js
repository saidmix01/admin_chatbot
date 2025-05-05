window.addEventListener('load', async function () {
	await load_questions();
});

/* The `document.addEventListener("DOMContentLoaded", function () { ... })` code snippet is setting up
an event listener that triggers when the DOM content has been fully loaded. Within this event
listener, a new instance of `Sortable` is created on the element with the ID
`user_question_content`. */
document.addEventListener("DOMContentLoaded", function () {
	new Sortable(document.getElementById('user_question_content'), {
		animation: 150,
		handle: '.question-card', // o '.drag-handle' si usas un ícono
		onEnd: async function (/**Event*/evt) {
			console.log('Nuevo orden:');
			document.querySelectorAll('.question-card').forEach((el, index) => {
				console.log(`ID: ${el.dataset.id}, Orden: ${index + 1}`);
			});
			await update_order_question();
		}
	});
});

const update_order_question = async () => {
	let url_update_order = `${base_url}Questions/update_order_question`;
	let data_send = [];
	document.querySelectorAll('.question-card').forEach((el, index) => {
		data_send.push({
			que_id: el.id.split('_')[1],
			que_order: index + 1
		});
	});
	fetch(url_update_order, {
		method: 'POST',
		headers: { 'Content-Type': 'application/json' },
		body: JSON.stringify({
			data_send
		})
	})
		.then(response => response.json())
		.then(async response => {
			if (!response.status) throw new Error(response.message);
			document.querySelector('.loading').style.display = "none";
			await load_questions();
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
 * The `save_question` function in JavaScript handles saving a new question by sending data to a server
 * and displaying success or error messages accordingly.
 */
const save_question = async () => {
    try {
        document.querySelector('.loading').style.display = "flex";
        const {que_question,que_order,que_parent} = await get_elements_form('new_question_form');
        //Validate fiels
        if (que_question == "") throw new Error("Question is required");
		if (que_order == "") throw new Error("Order is required");
		if (que_parent == "") throw new Error("Parent is required");
        let que_create_at = get_date_string();
        //URL Controller
        let url_question = `${base_url}Questions/save_question`;
        //Send data
        let data_send = {que_question,que_order,que_parent,que_create_at};
        send_data(url_question, data_send)
		    .then(response => {
				document.querySelector('.loading').style.display = "none";
				open_modal('modal_new_question','hide')
				if (response.status) {
					Swal.fire({
						icon: "success",
						title: "Success",
						text: response.message
					}).then(async (result) => {
						document.querySelector('.loading').style.display = "none";
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
    }
}

/**
 * The `load_questions` function fetches questions from a server, dynamically generates HTML elements
 * for each question along with a form to submit answers, and displays them on the webpage.
 * @param [que_parent=0] - The `que_parent` parameter in the `load_questions` function is used to
 * specify the parent question ID for which you want to load the child questions. If `que_parent` is
 * not provided or set to 0, it indicates that you want to load the top-level questions (questions
 * without a
 */
const load_questions = async (que_parent = 0) => {
	try {
		document.querySelector('.loading').style.display = "flex";
		let url_get_question = `${base_url}Questions/get_questions`;
		fetch(url_get_question, {
			method: 'POST',
			headers: { 'Content-Type': 'application/json' },
			body: JSON.stringify({
				que_parent
			})
		})
			.then(response => response.json())
			.then(async response => {
				if (!response.data.status) throw new Error(response.data.message);
				let question_html = '';
				for (const element of response.data.data) {
					question_html += `
						<div class="accordion question-card" id="question_${element.que_id}" data-id="${element.que_order}">
							<div class="card">
								<div class="card-header" id="heading_${element.que_id}">
									<h2 class="mb-0">
										<button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse" data-target="#body_question_${element.que_id}" aria-expanded="true" aria-controls="body_question_${element.que_id}">
											${element.que_question}
										</button>
									</h2>
								</div>

								<div id="body_question_${element.que_id}" class="collapse" aria-labelledby="heading_${element.que_id}" data-parent="#question_${element.que_id}">
									<div class="card-body">
										<div id="answer_content_${element.que_id}"></div>
										<hr>
										<form id="form_answer_${element.que_id}">
											<div class="input-group">
												<div class="custom-file">
													<input type="hidden" name="que_id" id="que_id_${element.que_id}" value="${element.que_id}">
													<input type="hidden" name="ans_order" id="ans_order_${element.que_id}" value="0">
													<input type="text" class="form-control" id="ans_text_${element.que_id}" name="ans_text" placeholder="Write your answer">
												</div>
												<div class="input-group-append">
													<button class="btn btn-success" type="button" onclick="save_answer('form_answer_${element.que_id}');"><span class="feather icon-save"></span></button>
												</div>
											</div>
										</form>
									</div>
								</div>
							</div>
						</div>
					`;
					//get answers
					await load_answer(element.que_id);
				}
				document.getElementById('user_question_content').innerHTML = question_html;
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
		console.log(error);
	}
}

/**
 * The `save_answer` function in JavaScript handles form submission, validation, sending data to a
 * server, and displaying success or error messages to the user.
 * @param [form] - The `form` parameter in the `save_answer` function is used to specify the form from
 * which data will be saved. If the `form` parameter is not provided or is an empty string, an error
 * "Form not found" will be thrown. The function then proceeds to extract data from the
 */
const save_answer = async (form="") => {
    try {
		if(form=="") throw new Error("Form not found");
        document.querySelector('.loading').style.display = "flex";
        const {que_id,ans_order,ans_text} = get_elements_form_sync(form);
		
        //Validate fiels
        if (ans_text == "" || ans_text == undefined) throw new Error("ans_text is required");
		if (ans_order == "" || ans_order == undefined) throw new Error("Order is required");
		if (que_id == "" || que_id == undefined) throw new Error("que_id is required");
        let ans_create_at = get_date_string();
        //URL Controller
        let url_question = `${base_url}Questions/save_answer`;
        //Send data
        let data_send = {ans_text,ans_order,que_id,ans_create_at};
        send_data(url_question, data_send)
		    .then(response => {
				document.querySelector('.loading').style.display = "none";
				open_modal('modal_new_question','hide')
				if (response.status) {
					Swal.fire({
						icon: "success",
						title: "Success",
						text: response.message
					}).then(async (result) => {
						document.querySelector('.loading').style.display = "none";
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
		document.querySelector('.loading').style.display = "none";
		Swal.fire({
			icon: "error",
			title: "Opss...",
			text: `${error}`
		})
    }
}

/**
 * The function `load_answer` asynchronously fetches answers for a given question ID and dynamically
 * displays them on the webpage.
 * @param [que_id] - The `load_answer` function is an asynchronous function that fetches answers for a
 * specific question ID (`que_id`). It first checks if the `que_id` is provided, then makes a POST
 * request to a specified URL to get the answers related to the provided question ID.
 * @returns The `load_answer` function is returning the `html_answer` variable, which is a string
 * containing HTML markup for a list of answers fetched from the server based on the provided `que_id`.
 * The function makes an asynchronous request to fetch answers for a specific question, processes the
 * response data to generate HTML markup for each answer, and then updates the DOM with the generated
 * HTML content. Finally, it returns
 */
const load_answer = async (que_id = "") => {
	console.log({que_id});
	
	try {
		if (que_id == "") throw new Error("que_id is required");
		document.querySelector('.loading').style.display = "flex";
		let url_get_question = `${base_url}Questions/get_answers`;
		fetch(url_get_question, {
			method: 'POST',
			headers: { 'Content-Type': 'application/json' },
			body: JSON.stringify({
				que_id: que_id
			})
		})
			.then(response => response.json())
			.then(async response => {
				let html_answer = "";
				if (!response.data.status) throw new Error(response.data.message);
				if(response.data.data.length > 0){
					response.data.data.forEach(element => {
						html_answer += `
						<ul class="list-group">
							<li class="list-group-item d-flex justify-content-between align-items-center">
								<span><strong>${element.ans_order}</strong> - ${element.ans_text}</span>
								<button class="btn btn-sm btn-danger" onclick="delete_answer(${element.ans_id})">
									<span class="feather icon-trash-2"></span>
								</button>
							</li>
						</ul>
						`;
					});
					document.getElementById(`answer_content_${que_id}`).innerHTML = html_answer;
					document.querySelector('.loading').style.display = "none";
				}else{
					html_answer = "";
				}

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
		console.log(error);
	}

}

const delete_answer = async (ans_id = "") => {
	if (ans_id == "") throw new Error("ans_id is required");
	document.querySelector('.loading').style.display = "flex";
	let url_delete_answer = `${base_url}Questions/delete_answer`;
	fetch(url_delete_answer, {
		method: 'POST',
		headers: { 'Content-Type': 'application/json' },
		body: JSON.stringify({
			ans_id
		})
	})
		.then(response => response.json())
		.then(async response => {
			if (!response.status) throw new Error(response.message);
			document.querySelector('.loading').style.display = "none";
			await load_questions();
			Swal.fire({
				icon: "success",
				title: "Success",
				text: response.message
			});
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
