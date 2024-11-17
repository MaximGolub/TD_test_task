document.querySelectorAll('input[type="tel"]').forEach(function (input) {
	input.addEventListener("input", function () {
		this.value = this.value.replace(/[^0-9+]/g, "");
	});
});
