<script>
    $(document).ready( function() {
    var loader ="<div class='loader'>"+
    "<img src='<?= URL ?>image/squares.gif' class='w3-round img-loader' /> "+
    "</div>";

    $("body").append(loader);

    $( "#dialog-form" ).dialog({ autoOpen:false });


    var sltd_type_kpi , sltd_or = null , delai = 0;
    var kpi_formate = function( typekpi , n , origine=null ){

    var kpi = typekpi.trim();
    if (origine != null)
    kpi= kpi+" "+origine;
    if ( n != 0)
    kpi = kpi.replace("n" , String (n));
    /** Remplissage des données dans le formulaire **/
    $(".abreviation").html( kpi.trim() ); $("input[name='abreviation']").val(kpi.trim());
    $("input[name='delai']").val( delai ); $("input[name='type_drgt']").val( origine );
    $("input[name='delai_time']").val(typekpi[typekpi.length-2]);
    /** Fin  **/
    if( isValid() )
    $("button.w3-teal").removeAttr("disabled");
    else{  $("button.w3-teal").attr("disabled");}

    };

    var isValid = function(){
    if(sltd_or == null  ){
    return false;
    }else if (delai == 0 ){
    return false;
    }
    return true;
    };

    $("select[name='type_kpi']").selectmenu({
    change: function( e , ui ){
    sltd_type_kpi = ui.item.label;
    kpi_formate(sltd_type_kpi , delai , sltd_or );
    }
    });

    $("#delai").change(function(){
    delai = $(this).val();
    kpi_formate(sltd_type_kpi , delai , sltd_or  );
    });

    $("select[name='origine']").selectmenu({
    change: function( e , ui ){
    sltd_or = ui.item.label;
    kpi_formate(sltd_type_kpi , delai , sltd_or );
    }
    });

    $( "select" ).selectmenu({width: 150})  .selectmenu( "menuWidget" ) ;

    $("#add_kpi").submit( function( e ){

    e.preventDefault();
    $("body").append(loader);

    $.ajax({
    url: '<?= URL ?>ajax/config',
    type: "POST", data: new FormData(this),
    contentType: false, cache: false, processData:false
    }).done(function( data ){

    window.setTimeout(function() { $("div.loader").remove(); }, 500);

    $("#data").html( verbose( "w3-teal", data ) );

    window.setTimeout(function() { $(".alert").fadeTo(2000, 500).slideUp(500, function(){ $(this).remove();  }); }, 2500);

    }).fail(function(){ console.log("impossile"); })

    });
    $(".kpi_config").click(function(){
    var titre = $(this).data('title');
    $( "#dialog-form" ).dialog({ modal:true, autoOpen:true, title:titre , height:500, width:600, modal :true, });
    });

    $("#form_add_kpi").submit( function( e ){
    e.preventDefault();
    $("body").append(loader);

    $.ajax({
    url: '<?= URL ?>ajax/config',
    type: "POST", data: new FormData(this),
    contentType: false, cache: false, processData:false
    }).done(function( data ){
    window.setTimeout(function() { $("div.loader").remove(); }, 500);
    if(parseInt(data) == 0 ){
    $("#rsl_d").html( verbose( "w3-yellow", "Ce KPI existe déjà " ) );
    }else if(parseInt(data) >= 1 ){
    $("#dialog-form").dialog( "close" );
    $("#rsl").html( verbose( "w3-teal", "KPI ajouté avec succés" ) );
    refreshList();
    }
    window.setTimeout(function() { $(".alert").fadeTo(2000, 500).slideUp(500, function(){ $(this).remove();  }); }, 2000);

    }).fail(function(){ console.log("impossile"); })
    });
    window.setTimeout(function() { $("div.loader").remove(); }, 500);
    });

    function refreshList (){
    var uL = $("#myUL");
    uL.append("<li><i class='fa fa-pulse fa-spin fa-fw' aria-hidden='true'></i></li>");
    window.setTimeout(function() {
    $.post("<?= URL ?>ajax/config" , {action : "list_table" , table:"kpi" })
    .done(function(data){
    var list = $.parseJSON(data);
    uL.html("");
    $.each( list ,function ( key , value ) {
    var li = "<li class='w3-border'><div class='w3-container'>"+
    "<div class='w3-left'><h6><b>"+ value.abreviation +"</b></h6></div>" +
    "<div class='w3-right'><button data-abrev='"+value.abreviation +"' data-id='"+ value.id +"' " +
    " class='w3-btn w3-hover-teal w3-btn-floating' onclick='suppr(this)'> " +
    "<i class='fa fa-trash'></i></button></div></div></li>";
    uL.append(li);
    });
    });
    }, 2500);
    };

    function verbose(color, txt ){
    var rt="<div class='w3-panel w3-leftbar w3-animate-zoom "+color+" '> <span class='w3-closebtn' onclick=\"this.parentElement.style.display='none'\"> x </span>"+
    "<p>"+txt+"</p></div>"  ;
    return rt;
    }

    function suppr( that ){
    $("#dialog-confirm").html(" <h5 class='w3-padding'>Le KPI <span class='w3-text-red'>"+that.getAttribute("data-abrev")+" </span> sera supprimé.</h5> ");
    $("#dialog-confirm").dialog({
    resizable:    false, height: 200, modal:true , width: 400, title: "Suppression" ,
    buttons: {
    "Continuer": function() {
    $( this ).dialog( "close" );
    $.post("<?= URL ?>ajax/config" , {action : "suppr_kpi" , id: that.getAttribute("data-id") })
    .done(function(data){
    $("#rsl").html( verbose("w3-red", "Vous avez supprimé le KPI "+ data ));
    refreshList();
    })},
    "Fermer": function() { $( this ).dialog( "close" ); },
    },
    });
    }

    function myFunction() {
    var input, filter, ul, li, a, i;
    input = document.getElementById("kpi_filter");
    filter = input.value.toUpperCase();
    ul = document.getElementById("myUL");
    li = ul.getElementsByTagName("li");
    for (i = 0; i < li.length; i++) {
    if ( li[i].innerHTML.toUpperCase().indexOf(filter) > -1 ) { li[i].style.display = ""; }
    else { li[i].style.display = "none"; }
    }
    }
</script>
