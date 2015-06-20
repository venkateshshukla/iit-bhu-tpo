var signInInit = function() {
	var values = {
		client_id: '244942183777-fi8bp76m3in1rueqjnkghp152d4hfpga.apps.googleusercontent.com',
		scope: 'openid email'
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

var sendToServer = function(url, idtoken) {
	var xhr = new XMLHttpRequest();
	xhr.open('POST', url);
	xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
	xhr.onload = function() {
		console.log('Signed in as: ' + xhr.responseText);
	};
	xhr.send('idtoken=' + idtoken);
};

var onSignIn = function() {
	var auth2 = gapi.auth2.getAuthInstance();
	if (auth2.isSignedIn.get()) {
		document.getElementById('g-signout').style.visibility = "visible";
		document.getElementById('g-disconnect').style.visibility = "visible";
		var googleUser = auth2.currentUser.get();
		var id = googleUser.getId();
		var id_token = googleUser.getAuthResponse().id_token;

		var profile = googleUser.getBasicProfile();
		var name = profile.getName();
		var email = profile.getEmail();
		console.log('ID: ' + id);
		console.log('ID Token: ' + id_token);
		console.log('Name: ' + name);
		console.log('Email: ' + email);

		sendToServer('signin.php', id_token);
		console.log('Sent to server');
	} else {
		console.log("No user is signed in.")
	}
};

var signOut = function() {
	var auth2 = gapi.auth2.getAuthInstance();
	var callback = function() {
		document.getElementById('g-signout').style.visibility = "hidden";
		document.getElementById('g-disconnect').style.visibility = "hidden";
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
		document.getElementById('g-signout').style.visibility = "hidden";
		document.getElementById('g-disconnect').style.visibility = "hidden";
	} else {
		console.log("No user is signed in.");
	}
};
