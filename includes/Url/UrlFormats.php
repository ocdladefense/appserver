<?php

/**



    // Format 1: absolute URL
          //  https://appserver/some/path/to/resource/with/1/or/2/additonal-params?and=some&additonanl=params&here=!
        // Formst 2: relatieve URl
          // /some/path/to/resource?other=hello

        // Format 3: relative url
          // some/path/toresource

          // Format 4: instruct user-agent to use current protocol:
          // //appserver/some/path/to/resource
                // Given example resource: path/to/some/resource  = /resource = return 404
          // Format 5: path resolution modifiers
          //  ../go/back/one/directory?hello=world

          // Format 6: be explicity about requesting current directory
           // ./dont/go/back/just/use/this/directory?almost=done

           // Path resolution
            // /path/to/app.php/is/somewhere/else/like/here/(app.php)
            // https://appserver/path/to/app.php/
*/