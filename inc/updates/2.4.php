<?php
    /**
     * UPDATE VERSION to 2.4
     *
     * In 2.4 we changed the previous usage of PHP $_SESSION's to browser cookies
     * As such, on update lets rename the previous dts_session_lifetime option (which also stored seconds)
     * to a new name which better represents the new convension; dts_cookie_lifespan
     */
    //Set an option to store the plugin cookie name
    update_option('dts_cookie_name', DTS_Core::build_cookie_name());

    //Add the new option using the new name and existing dts_session_lifetime value
    update_option('dts_cookie_lifespan', get_option('dts_session_lifetime'));
    
    //Remove the old option
    delete_option('dts_session_lifetime');
    
    //we're done here..