<?php
/**
 * @author Ouz Deville , MASS_DABOSS
 */

class Database{

    private static $pdo;
    private static $_instance ;

     public function __construct(){

        date_default_timezone_set('Africa/Dakar');

   	    if (!extension_loaded('pdo'))
		   die('Le serveur ne supporte l\'extension  PDO .');
		else
	       self::connection();

    }


    static function connection(){

          try{
          self::$pdo = new pdo("mysql:host=".ADD_BD.";dbname=".NOM_BD , USER_BD , PASS_BD);
             // Connexion a la bd réussi
          }catch( PDOException $e){
             die($e->getMessage() );
          }
    }

   /**
    * @return  une instance de la connexion de la base de données
    * Transforme la classe en Singleton.
    */
    public static function getDb(){

           if(is_null(self::$pdo)){
               self::$_instance = new Database();
           }
           return self::$_instance;
    }

    /**
     * @param $table = la table sur laquel on fait l'opération
     * @param $valeurs = tableau des valeurs a ajouter
     * @return L'id de l'élément ajouté
     */
     public function add($table , $valeurs){

          foreach ($valeurs as $key => $value)
          $champ_valeur[] = $key.'=:'.$key ;
          $sql = "INSERT INTO " .$table. " SET " . implode(' , ', $champ_valeur);
          //echo $sql;
          $stmt = self::$pdo->prepare($sql);
          foreach ($valeurs as $key => $value)
          $stmt->bindValue(':'.$key , $value);
           if($stmt->execute())
             return self::$pdo->lastInsertId();
     }

     /**
     * @param $table = la table sur laquel on fait l'opération
     * @param $id = valeur du champ id  pour indexé la ligne
     * @return boolean  true si l'ajout passe
     */
    public function suppr($table ,$champ_id , $id) {
         $sql = " DELETE FROM ". $table . " WHERE $champ_id = :id ";
         $stmt = self::$pdo->prepare($sql);
         $stmt->bindParam(':id', $id, PDO::PARAM_STR);
         $stmt->execute();
         return true;

    }

     /**
     * @param $table = la table sur laquel on fait l'opération
     * @param $champ = Le Champ à modifier
     * @param $valeur = La nouvelle valeur à mettre
     * @param $id = valeur du champ id pour indexé la ligne
     * @return boolean  true si l'ajout passe
     */
    public function modif($table , $champ, $valeur , $champ_id , $id){ //un seul champ
          $sql = "UPDATE ".$table." SET ".$champ. " =:valeur WHERE  $champ_id =:id ";
          $stmt=self::$pdo->prepare($sql);
          $stmt->bindValue(':valeur', $valeur);
          $stmt->bindParam('id', $id, PDO::PARAM_STR);
          if ($stmt->execute())
          return true;
     }

     /**
     * @param $table = la table sur laquel on fait l'opération
     * @param $valeurs = tableau de des nouvelles valeurs indexé par ls noms des tables
     * @param $id = valeur du champ id pour indexé la ligne
     * @return boolean  true si l'ajout passe
     */
    public function modif_plus($table , $valeurs ,$champ_id , $id){

        foreach ($valeurs as $key => $value)
          $champ_valeur[] = $key .' = :' . $key;
          $sql = "UPDATE ".$table." SET " .implode(' , ' , $champ_valeur)." WHERE $champ_id = :id ";
          $stmt = self::$pdo->prepare($sql);
          foreach ($valeurs as $key => $value)
          $stmt->bindValue(':'.$key , $value);
          $stmt->bindParam('id' ,$id , PDO::PARAM_STR);
          if ( $stmt->execute() )
            return true;
    }

    /**
     * @param $table = la table sur laquel on fait l'opération
     * @param $valeurs = tableau de des nouvelles valeurs indexé par ls noms des tables
     * @param $champ_id = Le champ pour idenfier la recherche
     * @param $id = valeur du champ_id pour indexé la ligne
     * @return Un Array de Array contenant le resultat du select
     */
    public function cherche($table , $champs , $champ_id , $id){

       $tab_champ = implode(' , ' , $champs) ;
       $sql="SELECT $tab_champ FROM $table WHERE $champ_id = :id ";
       //echo $sql;
       $stmt= self::$pdo->prepare($sql);
       $stmt->bindParam('id' , $id ,PDO::PARAM_STR);
       $stmt->execute();
       return  $stmt->fetchAll();

    }


     /**
     * @param $table = la table sur laquel on fait l'opération
     * @param $champ_id = Le champ pour idenfier la recherche
     * @param $id = valeur du champ_id pour indexé la ligne
     * @return Un Array de Array contenant le resultat du select
     */
    public function all( $table , $champ_id = null , $id = null ){

        $sql = "SELECT * FROM $table ";

        if($champ_id == null and $id == null )

           return self::rqt($sql);

        else {

           $sql  .= " WHERE $champ_id = :id ORDER BY $champ_id DESC ";
           $stmt = self::$pdo->prepare($sql);
           $stmt->bindParam('id' , $id , PDO::PARAM_STR );
           $stmt->execute();

           return $stmt->fetchAll();
        }
        
        return false;
    }


     /**
     * @param $stmt = Une requete sur une table de la base de données
     * @return Un Array de Array contenant le resultat de la recherche
     */
    public static function rqt( $stmt ){
      //echo $stmt;
	  return self::$pdo->query($stmt)->fetchAll(PDO::FETCH_ASSOC);
    }


    public static function compress_html($compress) {
        $search = array(
            '/\n/',                 // replace end of line by a space
            '/\>[^\S ]+/s',         // strip whitespaces after tags, except space
            '/[^\S ]+\</s',         // strip whitespaces before tags, except space
            '/\> \</s',
            '/(\s)+/s',             // shorten multiple whitespace sequences
            '/<!--(.|\s)*?-->/', // supprime les commentaires html
            '!/\*[^*]*\*+([^/][^*]*\*+)*/!' // supprime les commentaires css
        );

        $replace = array(
            ' ',
            '>',
            '<',
            '><',
            '\\1',
            '',
            ''
        );
        return preg_replace($search, $replace, $compress);
    }
    

} //Fin Database


