window.addEventListener('load', async function () {
	await load_questions();
});



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
				let question_html = "";
				response.data.data.forEach(element => {
					question_html += `
						<div class="accordion" id="question_${element.que_id}">
							<div class="card">
								<div class="card-header" id="headingOne">
								<h2 class="mb-0">
									<button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse" data-target="#body_question_${element.que_id}" aria-expanded="true" aria-controls="body_question_${element.que_id}">
									${element.que_question}
									</button>
								</h2>
								</div>
			
								<div id="body_question_${element.que_id}" class="collapse" aria-labelledby="headingOne" data-parent="#question_${element.que_id}">
									<div class="card-body">
										<ul class="list-group">
										<li class="list-group-item">An item</li>
									</ul>
									<hr>
									<form id="form_answer_${element.que_id}">
										<div class="input-group">
											<div class="custom-file">
												<input type="hidden" name="que_id" id="que_id" value="${element.que_id}">
												<input type="hidden" name="ans_order" id="ans_order" value="0">
												<input type="text" class="form-control" id="ans_text" name="ans_text" placeholder="Wrire your answer">
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
				});
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

const save_answer = async (form="") => {
    try {
		if(form=="") throw new Error("Form not found");
        document.querySelector('.loading').style.display = "flex";
        const {que_id,ans_order,ans_text} = get_elements_form_sync(form);
		console.log({que_id,ans_order,ans_text});
		
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