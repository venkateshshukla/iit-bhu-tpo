var signInInit = function() {
	var params = {
		client_id: '244942183777-fi8bp76m3in1rueqjnkghp152d4hfpga.apps.googleusercontent.com',
		fetch_basic_profile: false,
		scope: 'email'
	};
	var auth2 = gapi.auth2.init(params);
	var sbtn = document.getElementById("google-signin");
	sbtn.style.visibility = "inherit";
};

var onSignIn = function() {
	var auth2 = gapi.auth2.getAuthInstance();
	var onSuccess = function() {
		var user = auth2.currentUser.get();
		var id_token = user.getAuthResponse().id_token;
		window.location.replace('auth.php?idtoken=' + id_token);
	};
	var onFailure = function(reason) {
		console.log(JSON.stringify(reason, undefined, 2));
	};
	auth2.signIn().then(onSuccess, onFailure);
};
