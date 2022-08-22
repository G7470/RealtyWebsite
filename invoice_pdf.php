<?php

session_start();

require_once "./includes/config.php";
require_once "./includes/functions.php";
require('invoice_core.php');

if(!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] <> true) {
	// Redirect user to home page
    header("location: index.php");
	exit;
}
if(isset($_SESSION['vcode']) && $_SERVER['REQUEST_URI'] <> "/~jsb/ver_conf.php") {
	//Redirect user to confirmation page
	header("location: ver_conf.php");
	exit;
}
if(!isset($_GET['id']) || !is_numeric($_GET['id'])) {
		header("location: view_invoices.php");
		exit;
}
$getuser = $db->query("SELECT * FROM users WHERE username = ?", $_SESSION['username'])->fetchArray();

if(!$getuser) {
	header("location:view_invoices.php");
	exit;
}

$query = "SELECT i.*, o.userid, o.prop_addr1, o.prop_addr2, o.prop_city, o.prop_state, o.prop_zip, SUM(NVL(p.amount, 0)) as payment FROM invoices i JOIN orders o ON i.orderid = o.id LEFT JOIN payments p ON p.inv_id = i.id WHERE o.userid = ? AND i.id = ?";
$rows = $db->query($query, array($getuser['id'], $db->escape($_GET['id'])))->numRows();
if($rows <> 1 && !checkAdmin($getuser['usertype'])) {
	header("location: view_invoices.php");
	exit;
}

$invarr = $db->fetchArray();

// Get Charges
$getcharges = $db->query("SELECT c.* FROM charges c WHERE c.inv_id = ?", $db->escape($_GET['id']))->fetchAll();

// If admin, retrieve the correct data
if(checkAdmin($getuser['usertype'])) {
	$query = "SELECT i.*, o.userid, o.prop_addr1, o.prop_addr2, o.prop_city, o.prop_state, o.prop_zip, SUM(NVL(p.amount, 0)) as payment FROM invoices i JOIN orders o ON i.orderid = o.id LEFT JOIN payments p ON p.inv_id = i.id WHERE i.id = ?";
	$rows = $db->query($query, $db->escape($_GET['id']))->numRows();
	if($rows <> 1) {
		header("location: view_invoices.php");
		exit; 
	}
	$invarr = $db->fetchArray();
	$getuser = $db->query("SELECT * FROM users WHERE id = ?", $invarr['userid'])->fetchArray();
}

$compaddress = $getuser['company_addr1'] . "\n";
if($getuser['company_addr2'] <> "") {
	$compaddress .= $getuser['company_addr2'] . "\n";
}
$compaddress .= $getuser['company_city'] . ", " . $getuser['company_state'] . " " . $getuser['company_zip'] . "\n";

$propaddress = $invarr['prop_addr1'] . "\n";
if($invarr['prop_addr2'] <> "") {
	$propaddress .= $invarr['prop_addr2'] . "\n";
}
$propaddress .= $invarr['prop_city'] . ", " . $invarr['prop_state'] . " " . $invarr['prop_zip'];

$servnames = "";
$i = 1;
$ordservs = $db->query("SELECT s.serv_name, s.cost FROM order_services o JOIN services s ON o.serviceid = s.id WHERE o.orderid = ?", $invarr['orderid'])->fetchAll();

$admincompanyname = getConfig('admin_company_name');
$admincompanyaddr1 = getConfig('admin_company_addr1');
$admincompanyaddr2 = getConfig('admin_company_addr2');
$admincompanycity = getConfig('admin_company_city');
$admincompanystate = getConfig('admin_company_state');
$admincompanyzip = getConfig('admin_company_zip');

// Set Address
$addr = $admincompanyaddr1 . " \n";
if($admincompanyaddr2 <> '') {
	$addr .= $admincompanyaddr2 . " \n";
}
$addr .= $admincompanycity . ', ' . $admincompanystate . ' ' . $admincompanyzip;

$time_zone_from="UTC";
$time_zone_to='America/New_York';

$pdf = new PDF_Invoice( 'P', 'mm', 'A4' );
$pdf->AddPage();
$pdf->SetTitle("Invoice For " . $getuser['company']);
$pdf->addSociete( $admincompanyname,
                  $addr . "\n");
//$pdf->fact_dev( "Devis ", "TEMPO" );
//$pdf->temporaire( "PAID" );
$pdf->addDate(getDateFromUNIX($invarr['createdate']));
$pdf->addClient("CL" . $invarr['userid']);
$pdf->addPageNumber("1");
$pdf->addClientAdresse($getuser['company'] . "\n" . $propaddress);
$pdf->addReglement("PayPal");
$pdf->addEcheance(getDateFromUNIX($invarr['duedate']));
$pdf->addNumTVA($invarr['orderid']);
//$pdf->addReference("Devis ... du ....");
$cols=array( "Address"    => 100,
             "Service"  => 49,
             "Cost" => 41);
$pdf->addCols( $cols);
$cols=array( "Address"    => "L",
             "Service"  => "L",
             "Cost" => "R");
$pdf->addLineFormat( $cols);
$pdf->addLineFormat($cols);

$y    = 109;
$i = 1;
foreach($ordservs as $serv) {
	if($i > 1) {
		$propaddress = "";
	}
	$line = array( "Address"    => $propaddress,
				   "Service"  => $serv['serv_name'],
				   "Cost" => "$" . number_format($serv['cost'], 2));
	$size = $pdf->addLine( $y, $line );
	$y   += $size + 2;
	$i++;
	$service_cost += $serv['cost'];
}
foreach($getcharges as $charge) {
	$propaddress = "";
	$line = array( "Address"    => $propaddress,
				   "Service"  => $charge['charge_name'] . ' - ' . getDateFromUNIX($charge['charge_date']),
				   "Cost" => "$" . number_format($charge['amount'], 2));
	$size = $pdf->addLine( $y, $line );
	$y   += $size + 2;
	$i++;
	$charge_cost += $charge['amount'];
}
$totcost = $invarr['amount'] + $charge_cost;

if(($service_cost + $charge_cost) <> $totcost) {
	$oth_cost = $totcost - $service_cost;
	$line = array( "Address"    => "",
				   "Service"  => "Miscellaneous",
				   "Cost" => "$" . number_format($oth_cost), 2);
	$size = $pdf->addLine( $y, $line );
	$y   += $size + 2;
}

$pdf->addRemarque("Please pay for this invoice before due date");
//$pdf->addCadreTVAs();
        
// invoice = array( "px_unit" => value,
//                  "qte"     => qte,
//                  "tva"     => code_tva );
// tab_tva = array( "1"       => 19.6,
//                  "2"       => 5.5, ... );
// params  = array( "RemiseGlobale" => [0|1],
//                      "remise_tva"     => [1|2...],  // {la remise s'applique sur ce code TVA}
//                      "remise"         => value,     // {montant de la remise}
//                      "remise_percent" => percent,   // {pourcentage de remise sur ce montant de TVA}
//                  "FraisPort"     => [0|1],
//                      "portTTC"        => value,     // montant des frais de ports TTC
//                                                     // par defaut la TVA = 19.6 %
//                      "portHT"         => value,     // montant des frais de ports HT
//                      "portTVA"        => tva_value, // valeur de la TVA a appliquer sur le montant HT
//                  "AccompteExige" => [0|1],
//                      "accompte"         => value    // montant de l'acompte (TTC)
//                      "accompte_percent" => percent  // pourcentage d'acompte (TTC)
//                  "Remarque" => "texte"              // texte
$tot_prods = array( array ( "px_unit" => 600, "qte" => 1, "tva" => 1 ),
                    array ( "px_unit" =>  10, "qte" => 1, "tva" => 1 ));
$tab_tva = array( "1"       => 19.6,
                  "2"       => 5.5);
$params  = array( "RemiseGlobale" => 1,
                      "remise_tva"     => 1,       // {la remise s'applique sur ce code TVA}
                      "remise"         => 0,       // {montant de la remise}
                      "remise_percent" => 10,      // {pourcentage de remise sur ce montant de TVA}
                  "FraisPort"     => 1,
                      "portTTC"        => 10,      // montant des frais de ports TTC
                                                   // par defaut la TVA = 19.6 %
                      "portHT"         => 0,       // montant des frais de ports HT
                      "portTVA"        => 19.6,    // valeur de la TVA a appliquer sur le montant HT
                  "AccompteExige" => 1,
                      "accompte"         => 0,     // montant de l'acompte (TTC)
                      "accompte_percent" => 15,    // pourcentage d'acompte (TTC)
                  "Remarque" => "Avec un acompte, svp..." );

//$pdf->addTVAs( $params, $tab_tva, $tot_prods);
$pdf->displaytotals($totcost, $invarr['payment']);
$pdf->addCadreEurosFrancs();
$pdf->Output();
?>