window.addEventListener('load', async function () {
    await load_services(1,'single');
});


const load_services = async (ser_status = "",ser_type = "") => {
    try {
        if (ser_status == "") throw new Error("ser_status is required");
        let url_get_question = `${base_url}Page/get_services`;
        fetch(url_get_question, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                ser_status,
                ser_type
            })
        })
            .then(response => response.json())
            .then(async response => {
                let html_answer = "";
                if (!response.status) throw new Error(response.data.message);
                if (response.data.length > 0) {
                    response.data.forEach(element => {
                        html_answer += element.ser_html
                    });
                    document.getElementById(`service_content`).innerHTML = html_answer;
                } else {
                    html_answer = "";
                }

            })
            .catch(error => {
                console.log(error);
                
            });
    } catch (error) {
        console.log(error);
    }

}