app.service('DatetimeService', [function() {
    let service = this;

    service.lastDayOfMonth = function(strDate) {
        if(!strDate) return 31;

        return parseInt(moment(strDate).endOf('month').format('DD'));
    }
    
    service.fotmatYearMonth = function(my) {
        if(!my) return moment().format('YYYY-MM');

        let [month, year] = my.split('/');

        return `${year-543}-${month}`;
    };

    service.fotmatYearMonthBE = function(ym) {
        if(!ym) '';

        let [year, month] = ym.split('-');

        return `${month}/${parseInt(year)+543}`;
    };

    service.calcAge = function(birthdate, type) {
        return moment().diff(moment(birthdate), type);
    }
}]);