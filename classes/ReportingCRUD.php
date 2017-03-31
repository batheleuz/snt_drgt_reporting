<?php
/**
 * Created by PhpStorm.
 * User: Daboss
 * Date: 20/03/2017
 * Time: 20:26
 * Reporting Reader and Writer
 *
 * CRUD XML REPORTING FILES
 *
 */

class ReportingCRUD {

    const PATH = "datas/services/";

    /**
     * @function écrit le reporting dans le fichier de reporting du service
     * @param $service
     * @param $nom_reporting
     * @param $periodicite
     * @param $serialized
     * @param $id
     * @return  boolean
     */
    public static function append($service , $nom_reporting , $periodicite , $user  , $type , $serialized , $date ){
        
        $doc = new DOMDocument();
        $doc->load( self::PATH.$service."/reportings.xml" );
        
        $xpath = new DOMXPath($doc);
        
        $results = $xpath->query('/reportings');
        
        $r= $results->item(0);

            $b = $doc->createElement( "reporting" );

            $name = $doc->createElement( "name", $nom_reporting );
            $b->appendChild( $name );

            $per = $doc->createElement( "periodicite" , self::periodicite($periodicite) );
            $b->appendChild( $per );

            $us = $doc->createElement( "utilisateur" , $user );
            $b->appendChild( $us );

            $tp = $doc->createElement( "type" , $type );
            $b->appendChild( $tp );

            $content = $doc->createElement( "contenue" , $serialized);
            $b->appendChild( $content );

        $b->setAttribute( "id" , $date );
        $r->appendChild( $b );
        $doc->saveXML();
        return $doc->save( self::PATH.$service."/reportings.xml" );
    }

    /**
     * @function qui supprime un reporting dans le fichier de reporting du service.
     * @param $service
     * @param $id_reporting
     * @return int
     */
    public static function remove($service , $id_reporting ){

        $doc = new DOMDocument();
        $doc->load(self::PATH.$service."/reportings.xml");
        $xpath = new DomXpath($doc);
        try{
            $reportings = $doc ->documentElement;
            $reporting = $xpath->query("reporting[@id='".$id_reporting."']" , $reportings )->item(0);
            $reportings->removeChild($reporting);
            $doc->saveXML();
            return $doc->save( self::PATH.$service."/reportings.xml" );
        }
        catch ( Exception $e ){ return 0 ;  }
    }

    /**
     * @function créé le fichier de reporing pour un nouveau service.
     * @param $service
     */
    public static function createFile($service ){

        $doc = new DOMDocument();
        $doc->formatOutput = true;

        $r = $doc->createElement( "reportings" );

        $doc->appendChild( $r );

        $doc->saveXML();

        $doc->save( self::PATH.$service."/reportings.xml");

    }

    /**
     * @function lit le fichier de reporting du service et lui donne le reporting demandée
     * @param $service
     * @param null $id_reporting
     * @return mixed
     */
    public static function getReportingById ($service , $id_reporting=null ){

        $doc = new DOMDocument();
        $doc->load(self::PATH.$service."/reportings.xml");
        $xpath = new DomXpath($doc);
        if($id_reporting != null )
            $reporting = $xpath->query("//reporting[@id='".$id_reporting."']")->item(0);

        $reporting->nodeName;

        $node = $xpath->query("name", $reporting );
        $result['nom'] = $node->item(0)->nodeValue;

        $node = $xpath->query("periodicite", $reporting );
        $result['periodicite'] = $node->item(0)->nodeValue;

        $node = $xpath->query("utilisateur", $reporting);
        $result['user'] = $node->item(0)->nodeValue;

        $node = $xpath->query("type", $reporting);
        $result['type'] = $node->item(0)->nodeValue;

        $node = $xpath->query("contenue", $reporting);
        $result['contenue'] = $node->item(0)->nodeValue;

        return $result;
    }

    /**
     * @param $service
     * @param null $nom_reporting
     * @return int|mixed
     */
    public static function getReportingByName($service , $nom_reporting=null){
        foreach ( self::getReportings($service) as $rep ){
            if( $rep['name'] == $nom_reporting ){
                return self::getReportingById( $service, $rep['id'] );
            }
        }
        return 0;
    }
    
    /**
     * @function lit le fichier XML des reporting su $service
     * @param $service
     * @return array la liste de tous les reportings du service
      */
    public static function getReportings ($service) {

        $doc = new DOMDocument();
        $doc->load(self::PATH.$service."/reportings.xml");
        $xpath = new DomXpath($doc);

        $reportings = $xpath->query("//reporting");
        $results = array();

        foreach ($reportings as  $reporting ){

            $node = $xpath->query("attribute::id", $reporting );
            $result['id'] = $node->item(0)->value;

            $node = $xpath->query("name", $reporting );
            $result['name'] = $node->item(0)->nodeValue;

            $node = $xpath->query("periodicite", $reporting);
            $result['periodicite'] = $node->item(0)->nodeValue;

            $node = $xpath->query("type", $reporting);
            $result['type'] = $node->item(0)->nodeValue;

            $node = $xpath->query("utilisateur", $reporting);
            $result['user'] = $node->item(0)->nodeValue;

            $results[] = $result ;
        }
        return $results;
    }

    /**
     * @function qui check si un reporting du même nom existe dans le fichier.
     * @param $service
     * @param $name
     * @param $per
     * @return bool
     */
    public static function  isExistant ($service, $name , $per ){

        foreach ( self::getReportings($service) as $rep ){
            if( trim(strtolower($rep['name'])) == trim(strtolower($name)) and  $rep['periodicite'] == self::periodicite($per) ){
                return TRUE;
            }
        }
        return FALSE;
    }

    /**
     * @param $per :  (journaliere, hebdomadaire,  mensuelle, trimestrielle )
     * @return le nombre de jours relatif à la périodicité
     */
    private static function periodicite($per){
        $arr = array(
            'day' => 1 ,
            'week' => 7 ,
            'month' => 30 ,
            'trimester' => 90
            ) ;

        return $arr[$per];
    }

}