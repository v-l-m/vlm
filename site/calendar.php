<?php
    include_once("includes/header.inc");
    include_once("config.php");
?>
    <script type='text/javascript' src='externals/jquery/jquery.js'></script>
    <script type='text/javascript' src='externals/fullcalendar/fullcalendar.min.js'></script>
    <link rel='stylesheet' type='text/css' href='externals/fullcalendar/fullcalendar.css' />
    <script type='text/javascript'>
        $(document).ready(function() {
            $('#calendar').fullCalendar({
                editable: false,
                header: { left: 'title', center: '', right:  'today prev,next'},
                firstDay: 1,
                events: "/feed/races.fullcalendar.php",
                timeFormat: 'H:mm',
                loading: function(bool) {
                    if (bool) $('#loading').show();
                    else $('#loading').hide();
                }
            });
        });
    </script>
    <style type='text/css'>
      #loading {
        position: absolute;
        top: 5px;
        right: 5px;
        }

      #calendar {
        width: 550px;
        margin: 0 auto;
        }
      .fc-header td {
          border-style: none;
      }
      .fc-header-title { margin-top: 20px; }
    </style>
        <div id='loading' style='display:none'>loading...</div>
        <div id='calendar'></div>
        <hr />
        <div id='ical-help-box'>
        <?php
            echo nl2br(getLocalizedString("icalhelpbox"));
            echo "&nbsp;<b>http://".$_SERVER['SERVER_NAME']."/feed/races.ical.php?lang=$lang</b>";
                
        ?>
        </div>
<?php
    include_once("includes/footer.inc");
?>
