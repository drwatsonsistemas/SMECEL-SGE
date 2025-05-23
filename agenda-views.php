<?php require_once('Connections/SmecelNovo.php'); ?>
<?php
if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  if (PHP_VERSION < 6) {
    $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
  }

  $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}
}

mysql_select_db($database_SmecelNovo, $SmecelNovo);
$query_Calendario = "SELECT agenda_id, agenda_title, agenda_start, agenda_end, agenda_color FROM smc_agenda ORDER BY agenda_id DESC";
$Calendario = mysql_query($query_Calendario, $SmecelNovo) or die(mysql_error());
$row_Calendario = mysql_fetch_assoc($Calendario);
$totalRows_Calendario = mysql_num_rows($Calendario);
?>
<!DOCTYPE html>
<html>
<head>
<meta charset='utf-8' />
<link href='sistema/css/fullcalendar.min.css' rel='stylesheet' />
<link href='sistema/css/fullcalendar.print.min.css' rel='stylesheet' media='print' />
<script src='sistema/js/moment.min.js'></script>
<script src='sistema/js/jquery.min.js'></script>
<script src='sistema/js/fullcalendar.min.js'></script>
<script src='sistema/js/pt-br.js'></script>
<script>

  $(document).ready(function() {

    $('#calendar').fullCalendar({
      header: {
        left: 'prev,next today',
        center: 'title',
        right: 'month,agendaWeek,agendaDay,listWeek'
      },
      defaultDate: Date(),
      navLinks: true, // can click day/week names to navigate views
      editable: false,
      eventLimit: true, // allow "more" link when too many events
      events: [
	  
	  <?php do { ?>
  		{
          title: '<?php echo $row_Calendario['agenda_title']; ?>',
          start: '<?php echo $row_Calendario['agenda_start']; ?>',
		  end:	 '<?php echo $row_Calendario['agenda_end']; ?>',	
		  color: '<?php echo $row_Calendario['agenda_color']; ?>'
        },
	  <?php } while ($row_Calendario = mysql_fetch_assoc($Calendario)); ?>
      ]
    });

  });

</script>
<style>

  body {
    margin: 40px 10px;
    padding: 0;
    font-family: "Lucida Grande",Helvetica,Arial,Verdana,sans-serif;
    font-size: 14px;
  }

  #calendar {
    max-width: 900px;
    margin: 0 auto;
  }

</style>
<link rel="apple-touch-icon" sizes="180x180" href="https://www.smecel.com.br/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="https://www.smecel.com.br/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="https://www.smecel.com.br/favicon-16x16.png">
<link rel="manifest" href="https://www.smecel.com.br/site.webmanifest">
</head>
<body>
		   
<div id='calendar'></div>

</body>
</html>
<?php
mysql_free_result($Calendario);
?>
