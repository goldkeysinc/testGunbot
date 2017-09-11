{
	"pairs": {
		"poloniex": {
			"BTC_XMR": {
				"strategy": "bb",
				"override": {}
			},
			"BTC_ETH": {
				"strategy": "bb",
				"override": {}
			}
		},
		
		"bittrex": {
			"BTC-LTC": {
				"strategy": "bb",
				"override": {}
			},
			"BTC-PIVX": {
				"strategy": "bb",
				"override": {}
			}
		}
	},

	"exchanges": {
		"poloniex": {
			"key": "",
			"secret": ""
		},
		"kraken": {
			"key": "",
			"secret": ""
		},
		"bittrex": {
			"key": "",
			"secret": ""
		}
	},

	"bot": {
		"debug": false,
		"period_storage_ticker": 300,
		"interval_ticker_update": 10000,

		"timeout_buy": 120,
		"timeout_sell": 60,

		"BTC_TRADING_LIMIT": 0.001,
		"MIN_VOLUME_TO_BUY": 0.0005,

		"WATCH_MODE": false
	},

	"strategies": {
		"bb": {
			"PERIOD": 15,
			"BUY_LEVEL": 0.1,
			"GAIN": 0.6,
			"HIGH_BB": 60,
			"LOW_BB": 40,
			"PANIC_SELL": false
		}
	}
}