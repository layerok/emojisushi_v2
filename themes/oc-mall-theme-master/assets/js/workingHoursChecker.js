
My.namespace('Classes.WorkingHoursChecker')
My.namespace('Inst.Checker');

My.Classes.WorkingHoursChecker = function () {
    var Hours = {};
    function WorkingHoursChecker()
    {
        Hours = this;
        this.showSpotsDropdown = false;
        this.timeout = 30;
        this.now = new Date();
        this.lastVisitDate = new Date(localStorage.lastActivity);

    }

    WorkingHoursChecker.prototype.check = function () {
        _dbg();
        if (!My.Inst.Storage.exists('lastActivity')) {
            this.showSpotsDropdown = true;
        } else {
            this.showSpotsDropdown = _diff_minutes(this.lastVisitDate, this.now) > this.timeout;
        }
    }

    WorkingHoursChecker.prototype.init = function () {

        this.check();

        if (this.showSpotsDropdown) {
            $.magnificPopup.open({
                items: {
                    src: '#js-mfp-location', // can be a HTML string, jQuery object, or CSS selector
                    type: 'inline'
                }
            });
        }


        localStorage.lastActivity = new Date();
    }

    function _diff_minutes(dt2, dt1)
    {

        var diff =(dt2.getTime() - dt1.getTime()) / 1000;
        diff /= 60;
        return Math.abs(Math.round(diff));

    }

    function _dbg()
    {
        console.log("Последняя активность была: ", _diff_minutes(Hours.lastVisitDate, Hours.now), " минут назад")
    }

    return WorkingHoursChecker;
}();
My.Inst.Checker = new My.Classes.WorkingHoursChecker();
