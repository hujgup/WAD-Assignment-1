function formatErrorMessage(errors) {
	errors = "<p>Could not complete registration:<br />"+errors+"</p>";
	errors = errors.replace(/\<br \/\>\<br \/\>/g,"</p><p>");
	return errors;
}

window.addEventLisnener("DOMContentLoaded",function() {
	var email = document.getElementById("email");
	var pwd = document.getElementById("pwd");
	var pwdConfirm = document.getElementById("pwdConfirm");
	var name = document.getElementById("cname");
	var phone = document.getElementById("phone");
	var register = document.getElementById("register");
	var responseOutput = document.getElementById("response");
	register.onsubmit = function(event) {
		var errors = "";
		if (pwd.value !== pwdConfirm.value) {
			errors += "<br />Passwords do not match.";
		}
		var phoneValue = phone.value.replace(/\s/g,"");
		if (/[^\d\+][^\d]+/.test(phoneValue)) {
			errors += "<br />Phone number may only contain letters, whitespace, and optionally a plus symbol at the beginning.";
		}
		if (phoneValue.length > 10) {
			errors += "<br />Phone number too long: a maximum of 10 non-whitespace characters is enforced.";
		}
		var res = errors === "";
		if (res) {
			response.innerHTML = "";
			var req = new AJAXRequest(HTTPMethods.POST,register.getAttribute("target"));
			req.setPostData({
				email: email.value,
				pwd: pwd.value,
				pwdConfirm: pwdConfirm.value,
				name: name.value,
				phone: phone.value
			});
			req.execute(function(response) {
				response.innerHTML = response.text;
			});
		} else {
			response.innerHTML = formatErrorMessage(errors);
		}
		return false;
	};
});