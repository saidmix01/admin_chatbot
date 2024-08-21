window.addEventListener('load', async function () {
	await load_profiles();
});

/**
 * The function `load_profiles` fetches user profiles data from a server, processes the response, and
 * displays it in a select dropdown or shows an error message if there is an issue.
 */
const load_profiles = async () => {
	try {
		fetch(`${base_url}Users/load_profiles`, {
			method: 'POST',
			headers: { 'Content-Type': 'application/json' }
		})
			.then(response => response.json())
			.then(async data => {
				if (!data.status) throw new Error(data.message);
				let data_resp = [];
				data.data.forEach(element => {
					data_resp.push({id:element.pro_id,name:element.pro_description});
				});
				if (data.status) {
					paint_select(data_resp,'pro_id');
				} else {
					Swal.fire({
						icon: "error",
						title: "Opss...",
						text: `${data.message}`
					})
				}
			})
			.catch(error => {
				console.log(error);
				Swal.fire({
					icon: "error",
					title: "Something went wrong!",
					text: error
				});
			});
	} catch (error) {
		console.log({error});
		Swal.fire({
			icon: "error",
			title: "Something went wrong!",
			text: error
		});
	}
}
