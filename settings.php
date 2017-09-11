<?php
/*
Made by soxinabox/Chris for Gunbot (by Gunthar). I'm a broke student so please consider donating :)
Donate ETH to: 0x1c8D516435026B6b9f342F196754349e74ff9716
Donate BTC to: 1Eq7qp9qjt1dhTSVfNq2vYZzB4GTGXPek1
*/

$version = '2017-04-19';
$apiKey = 'QC7DF5V6-V5I5YF1X-HL3BB6C6-J43II4LY';
$apiSecret = '24049958d9c93017a9dc5deda75d6df1713080e44f409605b22a331aefd07a6aabefab4012c1f634a3dc84cc87691934beb072295f0e1bd6f69811c756ce5b0c';
$gunbotFolder = '/root/gunbot3/';
$hideMode = 0; //0 will hide non-running pairs; 1 will hide pairs in pairList below; 2 will show only pairs in pairList below; 3 will show all pairs
$pairList = array(
'XMR_DASH', 'XMR_MAID'
);
$balanceChart = true; // true will show BTC/USD vs. time chart
$colorDiff = 3;
$timezoneDiff = -4;
date_default_timezone_set('America/Detroit');

/*
THEMING
*/

$custom_css = 'custom.css';
