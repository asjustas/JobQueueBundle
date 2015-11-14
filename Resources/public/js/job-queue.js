$(document).ready(function () {
    var $jobReports = $('.job-reports');

    $('.job-reports .alert .output').readmore({
        moreLink: '<a href="#">' + $jobReports.data('show_more') + '</a>',
        lessLink: '<a href="#">' + $jobReports.data('close') + '</a>'
    });
});