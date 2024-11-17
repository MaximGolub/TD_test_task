document.addEventListener("DOMContentLoaded", () => {
	function handleFormSubmit(formId, messageId, apiUrl) {
		const form = document.getElementById(formId);
		const message = document.getElementById(messageId);

		form.addEventListener("submit", (event) => {
			event.preventDefault();

			const formData = new FormData(form);

			fetch(apiUrl, {
				method: "POST",
				body: formData,
			})
				.then((response) => response.json())
				.then((data) => {
					if (data.success) {
						window.location.href = data.redirectUrl;
					} else {
						message.innerText = data.message || "Processing error.";
						message.style.color = "red";
					}
				})
				.catch((error) => {
					message.innerText = "An error occurred. Try it later.";
					message.style.color = "red";
					console.error("Error:", error);
				});
		});
	}

	// Привязываем обработчики к формам
	handleFormSubmit("contactform1", "response1", "send_form.php");
	handleFormSubmit("contactform2", "response2", "send_form.php");
});
