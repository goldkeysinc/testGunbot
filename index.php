<?php
	include("poloniex.php");
	include("settings.php");

	$linux = 0;
	$titles = array();
	if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    if (file_exists('titles.bat')) { exec('titles.bat', $titles); }
	} else {
	    exec('ps aux | grep gunbot | grep -v grep | awk "{print \$12}" | sort -u', $titles);
			$linux = 1;
	}
	$polo = new Poloniex($apiKey,$apiSecret);
	$ticker = $polo->returnTicker();
	$balances = $polo->returnCompleteBalances();
	$tradeHistoryWeek = $polo->returnTradeHistory($currencyPair='all', $start=strtotime('-1 week'));
	$tradeHistoryDay = $polo->returnTradeHistory($currencyPair='all', $start=strtotime('-1 day'));

	if (count($ticker) > 0) {
		foreach ($ticker as $pair => $row) {
			if (file_exists($gunbotFolder.$pair."-save.json")) {
			  $gunbotSave = @json_decode(file_get_contents($gunbotFolder.$pair."-save.json"), true);
			} else {
				$gunbotSave = false;
			}
		  $coin = explode('_', $pair)[1];
		  $pairs[$pair] = array();
		  $pairs[$pair]['pair'] = $pair;
			if($linux) {
				$strs = array_flip($titles);
				$pairs[$pair]['pid'] = isset($strs[$pairs[$pair]['pair']]);

			} else if ($matches = preg_grep("/.*".$pairs[$pair]['pair']."\"$/", $titles)) {
			$strs = array_values($matches)[0];
			$pairs[$pair]['pid'] = trim(split(' ', $strs)[0], '"');
			} else {
			$pairs[$pair]['pid'] = 0;
			}
		  $pairs[$pair]['available'] = $balances[$coin]['available'];
		  $pairs[$pair]['onOrders'] = $balances[$coin]['onOrders'];
		  $pairs[$pair]['btcValue'] = $balances[$coin]['btcValue'];
		  $pairs[$pair]['btcValue'] = $balances[$coin]['btcValue'];
		  $pairs[$pair]['last'] = $ticker[$pair]['last'];
		  if ($gunbotSave != false) { $pairs[$pair]['boughtPrice'] = $gunbotSave['boughtPrice']; }  else { $pairs[$pair]['boughtPrice'] = 0; }
		  if ($gunbotSave != false) { $pairs[$pair]['priceToSell'] = $gunbotSave['priceToSell']; } else { $pairs[$pair]['priceToSell'] = 0; }
		  if ($pairs[$pair]['priceToSell'] > 0) { $pairs[$pair]['percentToSell'] = round(((($pairs[$pair]['priceToSell']-$pairs[$pair]['last'])/$pairs[$pair]['priceToSell'])*100), 2); } else { $pairs[$pair]['percentToSell'] = 0; }
		  $pairs[$pair]['percentChange'] = $ticker[$pair]['percentChange'];
		  if (array_key_exists($pair, $tradeHistoryWeek)) {
			  $pairs[$pair]['lastTrade'] = (strtotime($tradeHistoryWeek[$pair][0]['date']) + (3600*$timezoneDiff));

				$profit = 0;
				foreach ($tradeHistoryWeek[$pair] as $row) {
					if ($row['type'] == 'sell') {
						$profit += ($row['total']-$row['total']*$row['fee']);
					} elseif ($row['type'] == 'buy') {
						$profit -= ($row['total']-$row['total']*$row['fee']);
					}
				}
				$pairs[$pair]['weekProfit'] = round($profit, 5);
		  } else {
			  $pairs[$pair]['lastTrade'] = 0;
			  $pairs[$pair]['weekProfit'] = 0;
			  }
		  if (array_key_exists($pair, $tradeHistoryDay)) {
			$pairs[$pair]['24hrCount'] = count($tradeHistoryDay[$pair]);

		  } else {
			$pairs[$pair]['24hrCount'] = 0;

		  }

		}
		  usort($pairs, function($b, $a) {
			return $a['lastTrade'] - $b['lastTrade'];
		  });
	}
?>
<!DOCTYPE HTML>

<html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>Gunbot Monitor</title>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.28.7/js/jquery.tablesorter.min.js" integrity="sha256-dfyHSBujVyquBMangPERXV+xh4G6NXXvCQz2J99w08Y=" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.28.7/js/jquery.tablesorter.widgets.min.js" integrity="sha256-E4gtgLGjnTDFxJ+LIDPXDd7HMqSXDxA0oMrGB17eoP0=" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.28.7/js/widgets/widget-cssStickyHeaders.min.js" integrity="sha256-Wb7fCC3VMabS+rmrufJYuZD2NT6xCxMjQ+L0wMzXbsE=" crossorigin="anonymous"></script>
	<link rel="stylesheet" href="<?php print $custom_css ?>" type="text/css" media="screen"/>
	<script type="text/javascript">
		$(function(){
		  $("#gunbot-control").tablesorter({
          widgets: ['cssStickyHeaders'],
          widgetOptions: {
						cssStickyHeaders_offset        : 0,
			      cssStickyHeaders_addCaption    : true,
			      // jQuery selector or object to attach sticky header to
			      cssStickyHeaders_attachTo      : null,
			      cssStickyHeaders_filteredToTop : true
          }
        });
		});
	</script>
</head>
<body>
	<div id="header">
		<h1>Gunweb</h1>
		<div class="info-credits">
			<strong><?php echo date("Y-m-d h:i:s A"); ?></strong>
			<p>Made by soxinabox/Chris for Gunbot (by Gunthar). I'm a broke student so please consider donating :)<br />
			<strong>Donate ETH to: 0x1c8D516435026B6b9f342F196754349e74ff9716</strong><br />
			<strong>Donate BTC to: 1Eq7qp9qjt1dhTSVfNq2vYZzB4GTGXPek1</strong><br />
		</div>
	</div>
	<table id="gunbot-control">
	  <thead>
	    <tr>
			  <th class="align-center">Coin</th>
			  <th class="align-left">Available</th>
			  <th class="align-left">On Order</th>
			  <th class="align-left">BTC Value</th>
			  <th class="align-left">Last Market Price</th>
			  <th class="align-left">Bought Price</th>
			  <th class="align-left">Price To Sell</th>
			  <th class="align-left">Percent Change To Sell</th>
			  <th class="align-left">24hr Percent Change</th>
			  <th class="align-left">Trades in 24 hours</th>
			  <th class="align-left">1 Week Bitcoin Profit</th>
			  <th class="align-left">Last Trade</th>
			  <th class="align-center">Gunbot Active?</th>
	    </tr>
	  </thead>
	  <tbody>
	<?php foreach ($pairs as $pair => $row) {

		if($pairs[$pair]['available'] > 0) {
		  if ($pairs[$pair]['percentToSell'] > 0 and $pairs[$pair]['percentToSell'] < $colorDiff) {
			  echo '<tr class="row-upper">';
		  } elseif ($pairs[$pair]['percentToSell'] >= $colorDiff) {
			  echo '<tr class="row-lower">';
		  } else {
			  echo '<tr>';
		  }
		}
		  $gunbotSave = @json_decode(file_get_contents($pair."-save.json"), true) or
			$gunbotSave = false;
		  echo strtotime($pairs[$pair]['lastTrade']);
		  echo '<td class="coin-pair align-center">'.$pairs[$pair]['pair'].'</td>';
		  echo '<td class="align-left">'.$pairs[$pair]['available'].'</td>';
		  echo '<td class="align-left">'.$pairs[$pair]['onOrders'].'</td>';
		  echo '<td class="align-left">'.$pairs[$pair]['btcValue'].'</td>';
		  echo '<td class="align-left">'.$pairs[$pair]['last'].'</td>';
		  if ($pairs[$pair]['priceToSell'] > 0 & $pairs[$pair]['available'] > 0) {
		  echo '<td class="align-left">'.$pairs[$pair]['boughtPrice'].'</td>';
		  echo '<td class="align-left">'.$pairs[$pair]['priceToSell'].'</td>';
		  echo '<td class="align-left">'.$pairs[$pair]['percentToSell'].'</td>';
		  } else {
			echo '<td></td><td></td><td></td>';
		  }
		  echo '<td class="align-left">'.round(($pairs[$pair]['percentChange']*100), 2).'</td>';
		  echo '<td class="align-left">'.$pairs[$pair]['24hrCount'].'</td>';
		  echo '<td class="align-left">'.($pairs[$pair]['weekProfit']+$pairs[$pair]['btcValue']).'</td>';
		  echo '<td class="align-left">'.date("Y-m-d h:i:s A",$pairs[$pair]['lastTrade']).'</td>';



		  echo '<td class="align-center gunbot-active gunbot-active-' . ($pairs[$pair]['pid'] > 0) .'">';
		  if ($pairs[$pair]['pid'] > 0) { echo 'YES'; } else { echo 'NO'; }
		  echo '</td></tr>';
	}
	?>
	  </tbody>
	</table>
</body>

</html>
