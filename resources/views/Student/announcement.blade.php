<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Announcement</title>
    <link rel="icon" type="image/png" href="/favicon-96x96.png" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="/favicon.svg" />
    <link rel="shortcut icon" href="/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png" />    
    <link rel="stylesheet" href="{{ asset('assets/css/announcement.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

</head>
<body>

    <!-- Facebook SDK Root -->
    <div id="fb-root"></div>

    <div class="announcement-container">
        <div class="announcement-header">
            <div class="header-flex">
                <h2><i class="fas fa-bullhorn"></i> Announcements</h2>
                <div class="announcement-subtext">
                    <p>Stay updated with the latest announcements from the ITSBO.</p>
                    <p>For more details, visit our official Facebook page.</p>
                </div>
            </div>
        </div>

        <div class="fb-feed-container" id="fbFeed">
            <!-- Facebook Page Plugin -->
            <div class="fb-page" 
                 data-href="https://www.facebook.com/IBACMIBSIT" 
                 data-tabs="timeline" 
                 data-width="4000"
                    data-height="680"
                 data-adapt-container-width="false" 
                 data-hide-cover="false" 
                 data-show-facepile="true"
                 data-small-header="true">
                <blockquote cite="https://www.facebook.com/IBACMIBSIT" class="fb-xfbml-parse-ignore">
                    <a href="https://www.facebook.com/IBACMIBSIT">IBACMIBSIT Facebook Page</a>
            </div>
        </div>
    </div>

    <!-- Load JavaScript -->
    <script src="{{ asset('assets/js/Student.js') }}"></script>
    
    <script async defer crossorigin="anonymous"
        src="https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v17.0">
    </script>


</body>
</html>
