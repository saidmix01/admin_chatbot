window.addEventListener('load', async function () {
	await get_chats('chat_started',1);
	await get_chats('chat_progress',2);
	await get_chats('chat_success',3);
	await get_chats('chat_error',5);
});


const get_chats = async(id_content,status) =>{
	document.querySelector('.loading').style.display = "flex";
	fetch(`${base_url}Stores/get_chats_by_store`, {
		method: 'POST',
		headers: { 'Content-Type': 'application/json' },
		body: JSON.stringify({us_id:us_id_view, st_status: status})
	})
		.then(response => response.json())
		.then(async data => {
			document.querySelector('.loading').style.display = "none";
			if (!data.status) throw new Error(data.message);
			
			if (data.status) {
				document.getElementById(id_content).innerHTML = Object.keys(data.data).length;
				console.log(Object.keys(data.data).length);
				
			} else {
				Swal.fire({
					icon: "error",
					title: "Opss...",
					text: `${data.message}`
				})
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
}
