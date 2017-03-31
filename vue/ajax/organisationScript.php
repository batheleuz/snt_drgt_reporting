<script>
    <?php
    $liste_gi = json_encode ( $_SESSION['groupe_intervention'] );
    $liste_ui = json_encode ( $_SESSION['ui'] );  ?>

    var lgi = <?= $liste_gi; ?> ,
        lui = <?= $liste_ui; ?> ,
        categorie_active = null ,
        gi_active =  null ,
        loader ="<div class='loader'><img src='<?= URL ?>image/squares.gif' class='w3-round img-loader'/></div>";

    $("body").append(loader);

    $("input[name='categorie']").checkboxradio();

    function verbose(color, txt ){
        return "<div class='w3-panel w3-leftbar "+color+" '><span class='w3-closebtn' onclick=\"this.parentElement.style.display='none'\"> x </span><p>"+txt+"</p></div>" ;
    }

    function refreshListGI  (categorie = null ){

        $("#list_gi").html("");
        $.each(lgi , function( key , value ){

            var tr ="<tr onclick='afficherGI(this)' class='w3-border gi' id='"+value['id']+"' style='cursor:pointer;' > "+
                "<td><b> "+ value['nom']+"</b></td> </tr>" ;

            if (categorie == null )
                $("#list_gi").append(tr);

            else if(categorie == value.categorie )
                $("#list_gi").append(tr);

        });

    }

    function __get ( list , id ){
        var stuff = null ;
        $.each( list , function( key , value ){
            if( value['id'].toString() == id.toString() ) {
                stuff = value;
            }
        }) ;
        return stuff;
    }

    function getListUI (id_gi ){

        var listNomUi = Array() ,
            this_gi = __get ( lgi , id_gi),
            this_list_ui = this_gi['id_uis'].split(";");

        if( this_list_ui[0] == "" ){
            listNomUi.push("Aucune UI");
        }else{
            $.each(this_list_ui , function( key  , val ){
                var this_ui = __get( lui ,val );
                listNomUi.push( this_ui.nom );
                $("input#ui_"+this_ui.id ).prop("checked" , true );
            });
        }
        return listNomUi;
    }

    function afficherGI (tr){

        $("#list_ui").html("");
        $(".w3-checkbox").each(function(){
            $(this).prop({ checked: false });
        });

        $("tr.gi").removeClass("w3-grey active");
        $(tr).addClass("w3-grey active");

        $("#gi_title").html( "Groupe : " + $(tr).text() ) ;
        gi_active =  $(tr).attr("id");
        var liste_ui = getListUI(gi_active);

        var content = "<div style='padding-bottom:20px;' class='w3-container w3-border w3-white '>" +
            "<h3>"+ $(tr).text()+"</h3> <hr class='w3-border w3-border-teal'> " +
            "<table class='w3-table w3-striped  w3-bordered'> "+
            "<tr><th>Catégorie : </th><td>"+__get(lgi , gi_active)['categorie']+"</td></tr>"+
            "<tr><th>Nom Groupe :</th><td>"+__get(lgi, gi_active)['nom']+"</td></tr>" +
            "<tr><th>Liste des UI</th><td></td></tr> <tr><td colspan='2'>" +
            "<div class='w3-row w3-padding'>";
        $.each(liste_ui , function(key , value){
            content = content + "<div class='w3-half'><li><b><span class='w3-label'>"+ value +"</span></b></li></div>" ;
        });
        content = content + "</td></tr></div></table></div>"+
            "<div class='w3-container w3-padding w3-right'>" +
            "<button class='w3-btn w3-margin w3-btn w3-hover-border-white w3-teal w3-border w3-large' " +
            "style='cursor:pointer;' "+
            "onclick=\"document.getElementById('modal_add_existant_ui').style.display='block'\">"+
            "<i class='fa fa-cog'></i> ui </button>" ;

        if( __get(lgi , gi_active)['is_modifiable'] == 1 )
            content = content + "<button class='w3-btn w3-hover-border-white w3-border w3-red w3-padding' onclick='supprGI("+gi_active+");' >"+
                "<i class='fa fa-trash'></i> suppr </button> ";

        content = content +	"</div>";
        $("#list_ui").html(content);
    }

    function supprGI (id_gi ){

        console.log(id_gi);

    }

    function refreshListUI(){
        var  ul, div, i;
        ul  = document.getElementById("myUL_ui");
        div = ul.getElementsByTagName("div");
        for (i = 0; i < div.length; i++) {

        }
    }

    function filterListUI() {
        var input, filter, ul, div, i;
        input  = document.getElementById("search_ui");
        filter = input.value.toUpperCase();
        ul  = document.getElementById("myUL_ui");
        div = ul.getElementsByTagName("div");
        for (i = 0; i < div.length; i++) {
            if (div[i].innerHTML.toUpperCase().indexOf(filter) > -1) {
                div[i].style.display = "";
            } else {
                div[i].style.display = "none";
            }
        }
    }

    $('#multi_grpe').chosen({ width: "430px" ,  no_results_text: "Pas de resultat!" });

    $("tr.cat").click(function () {

        $("tr.cat").removeClass("w3-light-green") ;
        $(this).addClass("w3-light-green");
        categorie_active = $(this).data("cat") ;
        refreshListGI( categorie_active );

    });

    $("#form_add_gi").submit(function(e){

        e.preventDefault();

        $("body").append(loader);
        $.ajax({
            url: '<?= URL ?>ajax/config',
            type: "POST", data: new FormData(this),
            contentType: false, cache: false, processData:false
        }).done(function( data ){

            if( parseInt(data) == 0 ){

                $("#gi_modal_rsl").html(verbose( "w3-yellow", " Un groupe d'intervention du même nom existe déjà."));

            }else if(parseInt(data) >= 1 ){

                $("#gi_rsl").html(verbose( "w3-teal", " Création du groupe réussis."));
                document.getElementById('modal_add_gi').style.display='none';
                $("#list_gi").append("<tr><td><i class='fa fa-spin fa-pulse'></i></td><td><td></tr>");
                window.setTimeout( function(){
                    $.post("<?= URL ?>ajax/config" , { action : "list_table" , table : "groupe_intervention" })
                        .done(function(data){
                            console.log(data);
                            lgi = $.parseJSON(data);
                            refreshListGI( categorie_active );
                        });

                }, 2500);

            }
            window.setTimeout(function() { $("div.loader").remove(); }, 500);

        }).fail(function(){ alert("impossile"); });
    });

    $("#form_add_ui").submit(function(e){

        e.preventDefault();

        $("body").append(loader);
        $.ajax({
            url: '<?= URL ?>ajax/config',
            type: "POST", data: new FormData(this),
            contentType: false, cache: false, processData:false

        }).done(function( data ){

            if( parseInt(data) == 0 ){

                $("#ui_rsl").html(verbose( "w3-yellow", " Un groupe d'intervention du même nom existe déjà."));

            }else if(parseInt(data) >= 1 ){

                $("#ui_rsl").html(verbose( "w3-teal", " Création de l'ui réussis."));
                document.getElementById('modal_add_ui').style.display='none';
                $("#list_ui").append("<i class='fa fa-spin fa-2x fa-pulse'></i>");

                window.setTimeout( function(){
                    $.post("<?= URL; ?>/ajax/config" , { action : "list_table" , table : "ui" })
                        .done(function(data){
                            try{
                                lui = $.parseJSON(data);
                            }catch(err ){ console.log( err.message ); }
                            var table =  document.getElementById("list_gi");
                            afficherGI( table.getElementsByClassName("active")[0] );
                        });

                }, 2500);

            }
            window.setTimeout(function() { $("div.loader").remove(); }, 500);

        }).fail(function(){ alert("impossile"); })
    });

    $("#form_add_existant_ui").submit(function(e){
        e.preventDefault();
        $("body").append(loader);
        var datas = new FormData(this);
        datas.append("id_gi" , gi_active );
        $.ajax({

            url: '<?= URL ?>ajax/config',
            type: "POST", data: datas,
            contentType: false, cache: false, processData:false

        }).done(function( data ){

            console.log(data);

            if( parseInt(data) == 0 ){

                $("#ui_modal_rsl").html(verbose( "w3-yellow", " "));

            }else if(parseInt(data) >= 1 ){

                $("#ui_rsl").html(verbose( "w3-teal", " Modifications enregistrées."));
                document.getElementById('modal_add_existant_ui').style.display='none';
                $("#list_ui").append("<i class='fa fa-spin fa-2x fa-pulse'></i>");

                window.setTimeout( function(){
                    $.post("<?= URL; ?>/ajax/config" , { action : "list_table" , table : "groupe_intervention" })
                        .done(function(data){

                            lgi = $.parseJSON(data);
                            let table =  document.getElementById("list_gi");
                            afficherGI( table.getElementsByClassName("active")[0] );

                        });
                }, 2500);
            }
            window.setTimeout(function() { $("div.loader").remove(); }, 500);
        })
    });

    $(document).ready(function(){
        window.setTimeout(function() { $("div.loader").remove(); }, 500);
    });

</script>