var googleUser = {};
var signInInit = function() {

	gapi.load('auth2', function(){
      			// Retrieve the singleton for the GoogleAuth library and set up the client.
      			auth2 = gapi.auth2.init({
   				client_id: '244942183777-fi8bp76m3in1rueqjnkghp152d4hfpga.apps.googleusercontent.com',
				cookiepolicy: 'single_host_origin',
    				fetch_basic_profile: false,
    				scope: 'email'
  			});
      			auth2.attachClickHandler('customBtn', {}, onSuccess, onFailure);
			
			auth2.signIn().then(function() {
				 
				var googleUser = auth2.currentUser.get();
				var id = googleUser.getId();
				var id_token = googleUser.getAuthResponse().id_token;
				console.log(id);
				console.log(id_token);
				window.location.replace('auth.php?idtoken=' + id_token);

  			});
   		 });


	var onSuccess = function(googleUser) {
		console.log('Signed in as ' + user.getId());
	};

	var onFailure = function(error) {
		console.log(error);
		alert(JSON.stringify(error, undefined, 2));
	};

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
