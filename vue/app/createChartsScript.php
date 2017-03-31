<div id="container" style="width:800px; height:460px"></div>
<script>
    Highcharts.chart('container', {
        data: {
            table: 'table_charts'
        },
        chart:{
            type: '<?= $type; ?>'
        },
        title:{
            text: '<?= $name ." ".$start ." ".$end;  ?>'
        },
        yAxis: {
            title:{
                text:'Pourcentage %',
                max: 100
            }
        },
        <?= $options ?>
        tooltip:{
            headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
            pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
            '<td style="padding:0"><b>{point.y:.1f} </b></td></tr>',
            footerFormat: '</table>',
            shared: true,
            useHTML: true
        }
    });
</script>