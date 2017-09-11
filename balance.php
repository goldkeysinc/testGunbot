<?php
require_once("poloniex.php");
include("settings.php");

$polo = new Poloniex($apiKey,$apiSecret);
	
$ticker = $polo->returnTicker();
$balances = $polo->returnCompleteBalances();

$basedir = "./";
$total = 0;

foreach( $balances as $currency ) { 
	if( !empty( $currency['btcValue'] )) {
		$total += $currency['btcValue'];
	}   
}

if( $total > 0 ) { 
	if( is_array( $ticker ) && !empty( $ticker['USDT_BTC']['highestBid'] )) {
		$total = $total.",".$total * $ticker['USDT_BTC']['highestBid'];
		file_put_contents( $basedir . basename( preg_replace( '/\.php$/', '.csv', __FILE__ )), date( 'c', time() ).",$total\n", FILE_APPEND );
	}   
}
?>
