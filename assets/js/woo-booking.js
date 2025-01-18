(function ($) {
    $(document).ready(function () {
        const dateField = $('#cust_tour_dates');
        const minGroupSize = tourBookingConfig.minGroupSize || 2;
        const calDayCount = tourBookingConfig.calDayCount ? parseInt(tourBookingConfig.calDayCount) : 0;
        const monthCnt = calDayCount ? 1 : 2;

        // Initialize Flatpickr
        dateField.flatpickr({
            inline: true,
            mode: 'range',
            minDate: 'today',
            dateFormat: 'd-m-Y',
            //need to show year and month
            showMonths: monthCnt,
            onChange: function (selectedDates, dateStr, instance) {
                if (calDayCount && selectedDates.length === 1) {
                    // Automatically select two days if one date is picked
                    const nextDay = new Date(selectedDates[0]);
                    nextDay.setDate(nextDay.getDate() + calDayCount);
                    instance.setDate([selectedDates[0], nextDay], true);
                }
            }
        });

        // // Display a booking summary dynamically
        // const membersField = $('#cust_tour_members');
        // membersField.on('input', function () {
        //     const members = $(this).val();
        //     if (members < minGroupSize) {
        //         $('#booking-summary').text(`Minimum group size is ${minGroupSize}.`);
        //     } else {
        //         $('#booking-summary').text(`You selected ${members} members.`);
        //     }
        // });
    });
})(jQuery);
