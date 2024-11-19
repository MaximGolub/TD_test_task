document.addEventListener("DOMContentLoaded", () => {
	function handleFormSubmit(formId, messageId, apiUrl) {
		const form = document.getElementById(formId);
		const message = document.getElementById(messageId);

		form.addEventListener("submit", (event) => {
			event.preventDefault();

			const formData = new FormData(form);

			const csrfInput = document.querySelector('input[name="csrf_token"]');
			if (csrfInput) {
				formData.append("csrf_token", csrfInput.value);
			} else {
				console.error("CSRF token input not found!");
				return;
			}

			fetch(apiUrl, {
				method: "POST",
				body: formData,
			})
				.then((response) => response.json())
				.then((data) => {
					if (data.success) {
						document.getElementById("modalMessage").textContent = data.message;
						document.getElementById("successModal").style.display = "flex";
						setTimeout(() => (window.location.href = data.redirectUrl), 2000);
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

		document
			.querySelector(".modal_content")
			.addEventListener("click", function () {
				document.getElementById("successModal").style.display = "none";
			});

		document
			.getElementById("successModal")
			.addEventListener("click", function (e) {
				if (e.target === this) {
					this.style.display = "none";
				}
			});
	}

	// Привязываем обработчики к формам
	handleFormSubmit("contactform1", "response1", "send_form.php");
	handleFormSubmit("contactform2", "response2", "send_form.php");
});
