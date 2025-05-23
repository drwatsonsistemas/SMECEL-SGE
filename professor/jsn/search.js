/* ////////////////////////  SearchBox ((Placed in navbar)) //////////////////////////////////// */

'use strict';
$(function() {

    // initialise autocomplete
    $('input#search').autocomplete({
        data: {
            "avisos": null,
            "tutoriais": null,
            "senha": null,
            "foto": null

        },
        limit: 6, // total number of search row appear in result dropdown
        // Callback function when value is autcompleted.
        minLength: 0,
        onAutocomplete: function(value) {

            // Grabbing input after autocomplete is done
            value = value.toLowerCase().split(" ").join("-") + ".php";
            if (window.location.href.indexOf(value) == -1) {
                $('input#search+.autocomplete-content').hide();
                window.location.href = value;
            }

        },
    });
});