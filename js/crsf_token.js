function generateCSRFToken() {
	const array = new Uint32Array(4);
	window.crypto.getRandomValues(array);
	return Array.from(array, (dec) => dec.toString(16)).join("-");
}

document.addEventListener("DOMContentLoaded", () => {
	const csrfToken = generateCSRFToken();
	document.cookie = `csrf_token=${csrfToken}; path=/`;

	document.querySelectorAll("form").forEach((form) => {
		const hiddenInput = document.createElement("input");
		hiddenInput.type = "hidden";
		hiddenInput.name = "csrf_token";
		hiddenInput.value = csrfToken;
		form.appendChild(hiddenInput);
	});
});
