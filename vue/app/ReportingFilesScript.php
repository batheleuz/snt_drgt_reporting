<link rel="stylesheet" href="<?= URL ?>js/Highcharts/code/css/highcharts.css">
<script src="<?= URL ?>js/Highcharts/code/js/highcharts.js"></script>
<script src="<?= URL ?>js/Highcharts/code/js/highcharts-more.js"></script>
<script src="<?= URL ?>js/Highcharts/code/js/modules/data.js"></script>
<script src="<?= URL ?>js/Highcharts/code/js/modules/exporting.js"></script>
<script>
    var loader = "<div class='loader'> <img src='<?= URL; ?>image/squares.gif' class='w3-round img-loader' /></div>",
        liste_gi = <?= json_encode($_SESSION['groupe_intervention']) ?>,
        chartsTypeActive , projectActive , reportingActive ;

    function printRepList(data , id ) {
        var innerHTML =  $("#list_report").html("");
        $.each( data , function (key , val ) {
            var buffer ="<li class='file' style='cursor: pointer;' data-id=' "+id+" ' data-start ='"+val.start+"' data-end='"+val.end+"' "+
                        " onclick='showReporting(this)'> <i class='fa fa-table' ></i> "+
                        " <b> du "+ val.start +" au "+ val.end +"</b> "+
                        "<span class='w3-right w3-padding-right'> <i class='fa fa-angle-double-right'></i></span>"+
                        "</li>";
            innerHTML.append(buffer);
        });
    }

    function showReporting( li ) {
        $("body").append(loader);
        $("li").removeClass("w3-teal");
        $(li).addClass("w3-teal");
        var liDatas = $(li).data();
        if( liDatas.start+"-"+liDatas.end != reportingActive ){
            reportingActive = liDatas.start+"-"+liDatas.end;
            $.post("<?= URL ?>ajax/calcule" ,  { action: "viewReporting",  debut: liDatas.start ,  fin: liDatas.end ,  id: liDatas.id })
                .done(function(data){
                    $("#result").html(data);
                    switcher("content" , "result" );
                    window.setTimeout(function () { $("div.loader").remove(); } , 600);
                });
        }else{
            switcher("content" , "result" );
            window.setTimeout(function () { $("div.loader").remove(); } , 300);
        }


    }

    function printProjConfig(data) {
        $("#titre_report").html("<i class='fa fa-folder-open'></i> " + data.nom);
        $("#kpi_report ,#column_report , #periodicite_report , #user_report ").html("");
        $("#periodicite_report").html("<h5 class='w3-text-teal'><b> Chaque " + data.periodicite + " jour(s) </b></h5>");
        $("#user_report").html("<h5 class='w3-text-teal'><b> " + data.user + " </b></h5>");
        $.each(data.kpi, function (k, v) {
            $("#kpi_report").append(" <span class='w3-tag w3-round-large w3-teal' style='display:inline-block;margin-top:5px;margin-bottom:5px;'> " + v.abreviation + " </span> ");
        });
        if (data.gi)
            $.each(data.gi, function (k, v) {
                $("#column_report")
                    .append(" <span class='w3-tag w3-round-large w3-teal' style='display:inline-block;margin-top:5px;margin-bottom:5px;'> " + liste_gi[v - 1].nom + " </span> ");
            });

        if (data.column)
            $.each(data.column, function (k, v) {
                $("#column_report")
                    .append(" <span class='w3-tag w3-round-large w3-teal' style='display:inline-block;margin-top:5px;margin-bottom:5px;'> " + k + " : " + v + " </span> ");
            });
    }

    function extract(jsonDatas) {
        var data = $.parseJSON(jsonDatas);
        var arr = {
            nom: data['nom'],
            periodicite: data['periodicite'],
            user: data['user'],
            par : data['contenue']['par'] ,
            dateStart: data['contenue']['dates']['start'],
            dateEnd: data['contenue']['dates']['end'],
            kpi: data['contenue']['column_kpi'],
            gi: data['contenue']['groupe_intervention'],
            column: data['contenue']['column']
        };
        return arr;
    }

    function switcher(id_1, id_2) {
        $("#" + id_1).css({"display": "none"});
        $("#" + id_2).css({"display": "block"});
    }

    function exporter ( name ) {

        $("#tabl").table2excel({
            exclude: ".noExl",
            name: "Excel Document Name",
            filename: "Exportation_"+name,
            fileext: ".xls",
            exclude_img: true,
            exclude_links: true,
            exclude_inputs: true
        });
    }

    function createCharts( name ,start , end , type ){
        var dialogBox  = $("#dialog-confirm") ;
        dialogBox.dialog({title: "Graphe" , modal:true ,show: { effect: "fade", duration: 600 }});
        if( type != chartsTypeActive ){
            chartsTypeActive = type;
            dialogBox.html("<h2 class='w3-text-teal w3-center'><i class='fa fa-spinner fa-pulse fa-4x'></i></h2>" +
                "<h2 class='w3-text-teal w3-center'> Chargement ... </h2>");
            $.post("<?= URL ?>ajax/charts" , { name:name , start:start , end:end , type:type })
                .done(function(data){
                    dialogBox.html(data);
                    dialogBox.dialog({height:600 , width:840, show: { effect: "fade", duration: 600 },
                        buttons: {
                            "Fermer" : function(){
                                $(this).dialog("close");
                            }
                        }
                    });
                });
        }else{
            dialogBox.dialog("open");
        }
    }
    
    $("body").append(loader);

    $(document).ready(function () {
        window.setTimeout(function () {
            $("div.loader").remove();
        }, 500);
    });

    $(".suppr").click(function () {
        var id = $(this).parents("tr").attr("id");
        var dialogBox  = $("#dialog-confirm") ;
        dialogBox.html("<h3 class='w3-center w3-text-red'>Voulez vous vremment supprimer ce projet ?</h3>");
        dialogBox.dialog({
            resizable:false, modal:true , title: "Confirmation" ,
            buttons: {
                "Supprimer": function () {
                    dialogBox.html("<h1 class='w3-center w3-text-red'><i class='fa fa-2x fa-spinner fa-pulse fa-fw'></i></h1>");
                    $.post("<?= URL ?>ajax/reporting", { action: "supprReporting", id:id })
                        .done( function (data) {
                            window.setTimeout(function () {
                                if( data == "1" ){
                                    dialogBox.html("<h3 class='w3-center w3-text-red'><i class='fa fa-check'></i> Le projet a été supprimé. </h3>");
                                    window.setTimeout(function() { dialogBox.dialog("close");} , 1000 ) ;
                                    window.setTimeout(function() { table.rows( '#'+id ).remove().draw(); } , 2000 ) ;
                                } else if ( data == "0" )
                                    dialogBox.html("<h3 class='w3-center w3-text-red'><i class='fa fa-times'></i>Le projet ne peut pas être supprimé.</h3>");
                            }, 1000);
                        });
                } ,
                "Annuler" : function(){
                    $(this).dialog("close");
                }
            }
        });
    });

    $(".ouvrir").click(function () {

        $("body").append(loader);
        var id = $(this).parents("tr").attr("id") , reportingData;
        switcher( "reporting-view" , "content" );

        if( id != projectActive ){
            $("#list_report").html("<h5 class='w3-center'><i class='fa fa-pulse fa-spinner fa-fw ' aria-hidden='true' ></i> Chargement ...</h5>");
            projectActive = id ; chartsTypeActive = null;

            $.post("<?= URL ?>ajax/reporting", { action: "openReporting", id: id })
                .done( function (data) {
                    reportingData = extract(data) ;
                    printProjConfig( reportingData  );
                    window.setTimeout(function () {  $("div.loader").remove(); }, 600);
                });
                window.setTimeout(function () {
                    $.post ( "<?= URL ?>ajax/reporting" ,
                        {   action:"getAvailableReportings" , id:id , periodicite: reportingData.periodicite, dateD : reportingData.dateStart, dateF : reportingData.dateEnd , par : reportingData.par}
                    ).done(function(data){
                        try{
                            printRepList( $.parseJSON(data), id );
                        }catch(err){ console.log ( err.message ) }
                    });

                } , 1200);
            }else
                 window.setTimeout(function () {  $("div.loader").remove(); }, 600);
    });

    $("tr").click(function () {
        $("tr").removeClass("w3-grey");
        $(this).addClass("w3-grey");
    });
</script>