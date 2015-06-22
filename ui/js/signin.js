var signInInit = function() {
	var values = {
		client_id: '244942183777-fi8bp76m3in1rueqjnkghp152d4hfpga.apps.googleusercontent.com',
		fetch_basic_profile: false,
		scope: 'openid'
	};

	var onSignIn = function(googleUser) {
		 document.getElementById('googleButton').innerText = "Signed in";
	};

	var onError = function(error) {
		alert(JSON.stringify(error, undefined, 2));
	};

	auth2 = gapi.auth2.init(values);
	console.log("gapi initialised with given values.");

	div = document.getElementById('googleButton');
	auth2.attachClickHandler(element, {}, onSignIn, onError);
};

var onSignIn = function() {
	var auth2 = gapi.auth2.getAuthInstance();
	var googleUser = auth2.currentUser.get();
	var id = googleUser.getId();
	var id_token = googleUser.getAuthResponse().id_token;

	console.log(id);
	console.log(id_token);

	window.location.replace('auth.php?idtoken=' + id_token);
};

var signOut = function() {
	var auth2 = gapi.auth2.getAuthInstance();
	var callback = function() {
		console.log("User signed Out.");
	};
	if (auth2.isSignedIn.get()) {
		auth2.signOut().then(callback);
	} else {
		console.log("No user is signed in.");
	}
}

var revokeAll = function() {
	var auth2 = gapi.auth2.getAuthInstance();
	if (auth2.isSignedIn.get()) {
		auth2.disconnect();
	} else {
		console.log("No user is signed in.");
	}
};
