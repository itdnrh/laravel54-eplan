/**
	AngularJS
*/

var env = {};

// Import variables if present (from env.js)
if(window){  
	Object.assign(env, window.__env);
}

var app = angular.module('app', ['ngRoute', 'xeditable','ngTagsInput','toaster','ngAnimate','angularModalService']);

/** App Config */
app.constant('CONFIG', env);

app.run(function(editableOptions) {
    editableOptions.theme = 'bs3'; // bootstrap3 theme. Can be also 'bs2', 'default'
    editableOptions.activate = 'select';
});

/** Global functions */
app.run(function ($rootScope, $window, $http, toaster) {
	$rootScope.formError = null;

	$rootScope.formValidate = function (event, URL, validData, form, callback) {
		event.preventDefault();

		$http.post(env.baseUrl + URL, { ...validData })
			.then(function (res) {
				$rootScope.formError = res.data;
				console.log($rootScope.formError);
				if ($rootScope.formError.success === 0) {
					toaster.pop('error', "", "คุณกรอกข้อมูลไม่ครบ !!!");
				} else {
					callback(event, form);
				}
			})
			.catch(function (res) {
				console.log(res);
			});
	};

	$rootScope.checkValidate = function (validObj, field) {
		let status = false;
		
		if($rootScope.formError) {
			status = $rootScope.formError.errors.hasOwnProperty(field) ? true : false;
		}

		return status;
	};
}); /** Global functions */

/** Filter functions */
app.filter('thdate', function($filter)
{
	return function(input)
	{
		if(input == null){ return ""; } 

		var arrDate = input.split('-');
		var thdate = arrDate[2]+ '/' +arrDate[1]+ '/' +(parseInt(arrDate[0])+543);

		return thdate;
	};
});/** Filter functions */