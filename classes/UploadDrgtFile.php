<?php
/**
 * Created by PhpStorm.
 * User: Daboss
 * Date: 16/02/2017
 * Time: 10:19
 */

require_once 'Database.php';

class UploadDrgtFile {

    
     private $fichier;
     private $table;
     private $nbre_doublon = 0 ; //  nombre de doublons
     private $nbre_enrg = 0;     //  nombre de lignes enregistrées
     private $id_fic ;
     private $entete = array("nd","nom_du_client","nd_contact","commentaire_contact","segment",
                             "categorie","acces_reseau","acces_adsl","acces_tv","etat","origine","csig",
                             "commentaire_signalisation","agent_sig","date_sig","hsi","date_ess","h_date_ess",
                             "defaut","commentaire_essai","agent_ess","date_ori","h_date_ori","agent_ori","ui",
                             "equipe","date_plan","h_date_plan","date_rel","h_date_rel","releve","locali","cause",
                             "commentaire_releve","agent_rel" );

    public  function __construct( $fichier, $table  ){

        $this->fichier = $fichier;
        $this->table   = $table;
        
    }

    public  function enrg( $extension ){

        ini_set("auto_detect_line_endings", true);

        $this->checkCode();

        if(  $this->notExist() == true ){

            $this->id_fic= Database::getDb()->add("fichier" , array('nom_fichier'=>$this->fichier , 'date_ajout'=>Date('Y-m-d') , 'etat_fin' => 0 ) );

            $fx = "enrg".$extension;

            $res = $this->$fx();

        }else
            $res = array('code' => 0 ,'texte' => $this->fichier ." a déjà été enregistré.");

        return $res;
    }

    private function enrglis(){

        $handle = fopen("datas/uploads/drgt/".$this->fichier, "r") or die("Impossible d'ouvrir le fichier ");

        if ($handle) {
            $row = $rowNum = 0 ;
            while ( ($buffer = fgets($handle, 1000) ) !== false) {

                if ( trim($buffer) == null or strlen($buffer) < 10 )
                    continue;

                if ( preg_match( "/^[\ -]+$/", $buffer )) // Detection de la ligne
                    $rowNum = $row;

                $filesLine[] = $buffer;
                $row++;
            }
            fclose($handle);
        }
        else
           $txt = " Le fichier ne peut pas être ouvert ";     

       if($rowNum != 0 ){

            $colonneNum =  $filesLine[$rowNum];
            $numbers = explode( " ",  trim($colonneNum) );

            $start = 0;
            for( $i = 0 ; $i < count($numbers) ; $i ++ ){

                $pointer[] = array("start" => $start , "end" => (strlen($numbers[$i])+1) ) ;
                $start  =  $start + 1 + (int) strlen( $numbers[$i]);

            }
            /******************************
            * Construction de l'entete
            *******************************/
            foreach ($pointer as $p ){
                $entete[] = strtolower( str_replace(" " , "_" ,trim( substr( $filesLine[$rowNum-1], $p['start'] , $p['end'] ) )));
            }
            if($this->compare($entete) ){

                for( $i = $rowNum+1 ; $i < count( $filesLine) ; $i++ ) {

                    $arr= null;

                    foreach ($pointer as $p )
                        $arr[] = substr( $filesLine[$i], $p['start'] , $p['end'] ) ;

                        $this->add_drgt( $entete , $arr );
                }

                Database::getDb()->modif( "fichier" , "etat_fin", 1 , "id" , $this->id_fic );
                return $this->feedback();

            }else
                 $txt = " L'entête du fichier est incorrect ";
      } else
            $txt = " Veillez vérifier si le fichier contient une entête ";

         $this->interrupt();
         return array( 'code'=> 0 , 'texte'=> $txt );
    }

    private function enrgcsv(){

        if ( ($handle = fopen("datas/uploads/drgt/".$this->fichier, "r")) !== FALSE ){

                $data = fgetcsv($handle, 1000 , "\n" ) ;
                $arr_entete = explode(";" , $data[0]);
                $entete = null;
            
                for($i=0 ; $i < count($arr_entete) ; $i++ ){

                    $champ = str_replace(" " , "_" , strtolower($arr_entete[$i]) );

                    if($champ == "")
                        $champ = "h_".$entete[$i-1];

                    $entete[$i] = $champ;

                    var_dump($entete);

                }

                while (($data = fgetcsv($handle, 1000 , "\n" )) !== FALSE ) {
                    if ( preg_match( "/^[;]+$/" , $data ) )
                        continue;

                    $num = count($data);
                    for ($c=0; $c < $num; $c++) {
                        $data[$c] . "\n ";
                        $arr = explode(";", $data[$c] );
                        if($arr[0] != "")
                            $this->add_drgt( $entete , $arr );
                    }
                }
                fclose($handle);

                Database::getDb()->modif("fichier" , "etat_fin", 1 , "id" , $this->id_fic );
                return $this->feedback();


        }else
            $txt = " Impossible d'ouvrir le fichier ";

        $this->interrupt();
        return array('code' => 0 , 'texte' => $txt );
    }

    private function add_drgt( $entete ,  $line ){

        for ($i=0; $i < count($entete) ; $i++) {

            $valeurs[ $entete[$i] ] = trim($line[$i]);

        }


        if( $valeurs['nd'] != "ND" ){

            $valeurs['h_date_ess'] =$this->formate_hour($valeurs['h_date_ess']  , $valeurs['date_ess']);
            $valeurs['h_date_sig'] =$this->formate_hour($valeurs['h_date_sig']  , $valeurs['date_sig']);
            $valeurs['h_date_ori'] =$this->formate_hour($valeurs['h_date_ori']  , $valeurs['date_ori'] ) ;
            $valeurs['h_date_plan']=$this->formate_hour($valeurs['h_date_plan'] , $valeurs['date_plan'] );
            $valeurs['h_date_rel'] =$this->formate_hour($valeurs['h_date_rel']  , $valeurs['date_rel'] );

            $valeurs['date_ess'] = $this->formate_date($valeurs['date_ess']);
            $valeurs['date_sig'] = $this->formate_date($valeurs['date_sig']);
            $valeurs['date_ori'] = $this->formate_date($valeurs['date_ori']);
            $valeurs['date_plan']= $this->formate_date($valeurs['date_plan']);
            $valeurs['date_rel'] = $this->formate_date($valeurs['date_rel']);
        
            $valeurs['id_fichier'] = $this->id_fic ;


            if( $this->checkDoublon( $valeurs['nd'] , $valeurs['date_sig'] ,$valeurs['date_ori'] , $valeurs['date_rel']) == true ) {

                if(Database::getDb()->add($this->table, $valeurs) > 0 )
                    $this->nbre_enrg ++;

            }else
                $this->nbre_doublon ++;
        }

    }

    private function notExist(){

        $rqt = " SELECT * FROM fichier WHERE nom_fichier='$this->fichier' AND etat_fin=1 " ;
        $fic = Database::getDb()->rqt($rqt);
        if( $fic[0]['nom_fichier'] != $this->fichier )
            return true;
        else
            return false;

    }

    private function formate_hour( $hour , $date ){

     if ($hour == null )
        if( preg_match("#^([0-9]{2})/([0-9]{2})/([0-9]{4}) ([0-9:]+)$#" , $date) )
            return preg_replace("#^([0-9]{2})/([0-9]{2})/([0-9]{4}) ([0-9:]+)$#", "$4", $date );

        if( preg_match("#^([0-9]{2})/([0-9]{2})/([0-9]{2}) ([0-9:]+)$#" , $date))
            return preg_replace("#^([0-9]{2})/([0-9]{2})/([0-9]{2}) ([0-9:]+)$#", "$4", $date );

        return $hour;
    }

    private function formate_date($date){

       if ( preg_match("#^([0-9]{2})/([0-9]{2})/([0-9]+)$#" , $date) )
          return preg_replace("#^([0-9]{2})/([0-9]{2})/([0-9]+)$#", "$3-$2-$1", $date );
   
       else if ( preg_match("#^([0-9]{2})/([0-9]{2})/([0-9]{4}) ([0-9:]+)$#" , $date)  )
           return preg_replace ("#^([0-9]{2})/([0-9]{2})/([0-9]{4}) ([0-9:]+)$#", "$3-$2-$1", $date);

       else
           return "0000-00-00";
    }

    private function checkDoublon( $nd , $date_sig  , $date_ori , $date_rel ){

         $rqt = " SELECT nd FROM ".$this->table." WHERE nd = '$nd' ".
                " AND date_sig ='$date_sig' AND date_ori ='$date_ori' AND date_rel ='$date_rel'  " ;

        $line = Database::getDb()->rqt($rqt);

        if( isset($line[0]['nd']) )
            return false;

        return true;

    }

    private function checkCode(){}

    private function compare( $arr ) {

        return true ;
    }
   
    private function interrupt(){

      Database::getDb()->suppr( "fichier" , "id" , $this->id_fic );
      Database::getDb()->suppr( $this->table  ,  "id_fichier" , $this->id_fic );

    } 
    
    public function feedback(){

       if( $this->nbre_doublon != 0 or $this->nbre_enrg != 0  ){

        return array( 
                'code' => 1, 
                'texte' =>"Enregistrement du fichier ".$this->fichier. " réussi",
                'doublon' => $this->nbre_doublon , 
                'enrg' => $this->nbre_enrg 
                );
       }
       // else do nothing     
    }
    
}
