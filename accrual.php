<?php
const NODAG_NAME = 'no dag';
$datefield = $_GET["datefield"];
$pid = $_GET["pid"];
global $module;
/**
 * @var $module \uzgent\AccrualReport\AccrualReport
 */

list($datemapPerDag, $dagList, $dags) = $module->getDagMapForField($datefield);
require_once APP_PATH_DOCROOT . 'ProjectGeneral/header.php';

?>

<head>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
    google.charts.load('current', {'packages':['corechart']});
    google.charts.setOnLoadCallback(drawChart);
function drawChart() {
    var data = google.visualization.arrayToDataTable([
        ['Date', <?php echo $dagList ?>],
        <?php
            $dateKeys = $module->printDagMap($datemapPerDag, $dags)
        ?>
    ]);

    var options = {
        isStacked: true,
        title: 'Accrual graph for <?php echo $datefield; ?>',
        hAxis: {title: '<?php echo $datefield; ?>',  titleTextStyle: {color: '#333'}},
        vAxis: {minValue: 0}
    };

    var chart = new google.visualization.AreaChart(document.getElementById('chart_div'));
    chart.draw(data, options);
}
    </script>
</head>
<body>
<H1>
    Accrual graph for <?php echo $datefield; ?>
</H1>
<div>
    </div>
    <div id="chart_div"></div>
<BR/>

</body>
<?php

