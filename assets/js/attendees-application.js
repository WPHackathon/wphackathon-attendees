jQuery('document').ready(function($){

    // Variables
    var empty_class = "error_empty_field";


    attendee_form = {
          addEventListeners: function(){

                 // When the user clicks to send the form
                $('#submit').click(function(e){
                    var result = true;

                    // Check "wph-attendee-name" field is not empty
                    var wph_attendee_name = $('#wph-attendee-name');
                    if ( $(wph_attendee_name).val() === "" ) {

                        // Add the class error
                        $(wph_attendee_name).addClass(empty_class);

                        // Set the flag as false
                        result = false;
                    }
                    else {
                       $(wph_attendee_name).removeClass(empty_class);
                    }



                    // Check "wph-attendee-email" field is not empty
                    var wph_attendee_email = $('#wph-attendee-email');
                    if ( $(wph_attendee_email).val() === "" ) {

                        // Add the class error
                        $(wph_attendee_email).addClass(empty_class);

                        // Set the flag as false
                        result = false;

                    }
                    else {

                        // Check "wph-attendee-email" field is a correct email address
                        if( !ValidateEmail( $(wph_attendee_email).val() ) ){

                            // Add the class error
                            $(wph_attendee_email).addClass(empty_class);

                            // Set the flag as false
                            result = false;

                        }
                        else {
                            $(wph_attendee_email).removeClass(empty_class);
                        }

                    }



                    // Check "wph-attendee-description" field is not empty
                    var wph_attendee_description = $('#wph-attendee-description');
                    if ( $(wph_attendee_description).val() === "" ) {

                        // Add the class error
                        $(wph_attendee_description).addClass(empty_class);

                        // Set the flag as false
                        result = false;
                    }
                    else {
                        $(wph_attendee_description).removeClass(empty_class);
                    }






                    // Check "wph-attendee-explanation" field is not empty
                    var wph_attendee_explanation = $('#wph-attendee-explanation');
                    if ( $(wph_attendee_explanation).val() === "" ) {

                        // Add the class error
                        $(wph_attendee_explanation).addClass(empty_class);

                        // Set the flag as false
                        result = false;
                    }
                    else {
                        $(wph_attendee_explanation).removeClass(empty_class);
                    }



                    // If there was an empty or erronous field in the form...
                    if (!result){

                        // Show the message
                        $('.alert').removeClass('hidden');
                        e.preventDefault();

                    }
                    else{

                        // Remove the message
                        $('.alert').addClass('hidden');

                    }


                 });

          },
          init: function(){
             attendee_form.addEventListeners();
          }
    };

    attendee_form.init();


    /**
     * This function check if the parameter is a valid email address
     * @param       pEmail      string
     * @returns     {boolean}   Returns true if the email is correct
     */
    function ValidateEmail(pEmail)
    {
        return (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(pEmail));
    }


});
