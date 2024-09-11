window.addEventListener('load', async function () {
	await get_chats('chat_started', 1);
	await get_chats('chat_progress', 2);
	await get_chats('chat_success', 3);
	await get_chats('chat_error', 5);


});

const data_chats = [];


const get_chats = async (id_content, status) => {
	document.querySelector('.loading').style.display = "flex";
	fetch(`${base_url}Stores/get_chats_by_store`, {
		method: 'POST',
		headers: { 'Content-Type': 'application/json' },
		body: JSON.stringify({ us_id: us_id_view, st_status: status })
	})
		.then(response => response.json())
		.then(async data => {
			document.querySelector('.loading').style.display = "none";
			if (!data.status) throw new Error(data.message);

			if (data.status) {
				document.getElementById(id_content).innerHTML = Object.keys(data.data).length;
				data_chats.push({ status, data: data.data });
				if (status == 1) {
					load_chats_table();
				}
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

const load_chats_table = async (status = 1) => {
	try {
		let data_table = [];
		data_chats.forEach(element => {
			if (status == element.status) {
				element.data.forEach(key => {
					const data_chat = JSON.parse(key.st_information);
					data_table.push({
						phone: data_chat.from,
						name: data_chat.pushName,
						date: key.st_create_date,
						log: key.st_log
					})
				});
			}
		});
		const dateCounts = {};

		data_table.forEach((record) => {
			const recordDate = record.date;
			if (dateCounts[recordDate]) {
				dateCounts[recordDate]++;
			} else {
				dateCounts[recordDate] = 1;
			}
		});
		const formattedData = Object.keys(dateCounts).map((date) => ({
			label: date,
			y: dateCounts[date],
		}));
		var chart = new CanvasJS.Chart("chartContainer", {
			animationEnabled: true,
			theme: "light1", // "light1", "light2", "dark1", "dark2"
			title: {
				text: "Total chats by date"
			},
			axisY: {
				title: "Total chats"
			},
			data: [{
				type: "column",
				yValueFormatString: "# Chats",
				dataPoints: formattedData
			}]
		});
		chart.render();
		let table_data = data_table.map(item => {
			return [
				item.phone,
				item.name,
				item.date,
				item.log
			];
		});

		let columns = [
			{ title: 'Phone' },
			{ title: 'Name' },
			{ title: 'Date' },
			{ title: "Log" }
		];
		await paint_datatable('table_chats', columns, table_data);

	} catch (error) {
		console.log(error);
		Swal.fire({
			icon: "error",
			title: "Something went wrong!",
			text: error
		});
	}
}
