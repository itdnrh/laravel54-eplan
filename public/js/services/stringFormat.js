app.service('StringFormatService', function(CONFIG, $http) {
	this.convToDbDate = function (date) {
		const [day, month, year] = date.split('/');

		return `${(parseInt(year) - 543)}-${month}-${day}`;
	}

	this.convFromDbDate = function (date) {
		const [year, month, day] = date.split('-');

		return `${day}/${month}/${(parseInt(year) + 543)}`;
	}
});