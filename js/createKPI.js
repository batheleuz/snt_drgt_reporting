/**
* User: Daboss
*/
$(document).ready( function() {
    let sltd_type_kpi , sltd_or = null , delai = 0;
    var kpi_formate = function( typekpi , n , origine=null ){

        var kpi = typekpi.trim();
        if (origine != null)
           kpi= kpi+" "+origine;
        if ( n != 0)
           kpi = kpi.replace("n" , String (n));
        /** Remplissage des données dans le formulaire **/
        $(".abreviation").html( kpi.trim() ); $("input[name='abreviation']").val(kpi.trim()); $("input[name='delai']").val( delai ); $("input[name='type_drgt']").val( origine ); $("input[name='delai_time']").val(typekpi[typekpi.length-2]);
        /** Fin  **/
        if( isValid() )
          $("button.w3-teal").removeAttr("disabled");
        else
          $("button.w3-teal").attr("disabled");
    }

    var isValid = function(){
      if(sltd_or == null  ){
        return false;
      }else if (delai == 0 ){
        return false;
      }
      return true;
    }

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

    $( "select" ).selectmenu({width: 150})  .selectmenu( "menuWidget" )

});
