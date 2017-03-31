<?php

/**
 * Created by PhpStorm.
 * User: Daboss
 * Date: 24/02/2017
 * Time: 13:27
 */
class UploadCodiFile{


    private $fichier;
    private $table;
    private $colonne;
    private $nmb_added = 0;

    public  function __construct( $fichier , $table , $colonne ){

        $this->fichier = $fichier;
        $this->table   = $table;
        $this->colonne = $colonne;
    }

    public function enrg(){
        //ini_set("auto_detect_line_endings", true);

        if ( ($handle = fopen("uploads/codification/".$this->fichier, "r")) !== FALSE ){

            $signification = null ; $code = null;
            $entete_arr = explode( ";" , fgetcsv($handle, 1000 , "\n")[0] );

            foreach ($entete_arr as $key => $value){
                if( $value == "signification" )
                    $signification = $key ;
                elseif($value == "code")
                    $code = $key ;
            }
            
            if( $signification >= 0  and $code >=0 ):

                    while (($data = fgetcsv($handle, 1000 , "\n" )) !== FALSE ) {
                        $numLigne = count($data);
                        for ( $c=0; $c < $numLigne; $c++ ) {
                             $codeLine  = explode( ";" ,  $data[$c] ) ;
                             $this->addCode($codeLine , $signification , $code);
                        }
                    }
                echo $this->nmb_added;
                Database::getDb()->modif("colonnes" , "codification" , 1 , "col_label" , $this->colonne);

             else:
                 echo -1 ; // Le fichier n'a pas l'entete correcte.
             endif;

        }else{  echo  -2 ; }
    }

    private function addCode( $arrLine , $indexSign , $indexCode ){

        $valeur= array( 'signification'=> $arrLine[$indexSign],
                        'code'=> $arrLine[$indexCode] ,
                        'colonne' => $this->colonne );

        $r = Database::getDb()->rqt("SELECT COUNT(*) as n FROM  code  WHERE code ='".$valeur['code']."'  AND colonne='".$valeur['colonne']."'") ;
        if( $r[0]['n'] == 0 ){
            Database::getDb()->add("code", $valeur);
            $this->nmb_added ++ ;
        }
    }



}