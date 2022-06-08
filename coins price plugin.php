<?php

/**
 * Plugin Name: coins price
 * Description: display turkish lira by different coins
 * Version: 1.1
 * Author: Mohammad Hasan
 */



//global variables
$profitPercent = 1.033;
$tetherAmount = 2000;
$binanceApiGeneralUrl = 'https://api.binance.com/api/v3/depth?symbol=';
$nobitexApiGeneralUrl = 'https://api.nobitex.ir/v2/orderbook/';
$symbolBids = 'bids';
$symbolAsks = 'asks';
$symbolDOGEUSDT = 'DOGEUSDT';
$symbolDOGEIRT = 'DOGEIRT';
$symbolDOGETRY = 'DOGETRY';
$symbolADAIRT = 'ADAIRT';
$symbolADAUSDT = 'ADAUSDT';
$symbolADATRY = 'ADATRY';
$symbolEOSIRT = 'EOSIRT';
$symbolEOSUSDT = 'EOSUSDT';
$symbolEOSTRY = 'EOSTRY';
$symbolDOTIRT = 'DOTIRT';
$symbolDOTUSDT = 'DOTUSDT';
$symbolDOTTRY = 'DOTTRY';
$symbolFTMIRT = 'FTMIRT';
$symbolFTMUSDT = 'FTMUSDT';
$symbolFTMTRY = 'FTMTRY';
$symbolTRXIRT = 'TRXIRT';
$symbolTRXUSDT = 'TRXUSDT';
$symbolTRXTRY = 'TRXTRY';



function calcualteAveragePrice($url, $amountLimit, $orderType)
{

    $sumAmount = 0;
    $sumPrice = 0;
    $finalPrice = 0;

    $apiRunResult = file_get_contents($url); //call api to get orders

    if ($apiRunResult == true) {
        $apiDataArray = json_decode($apiRunResult, true);
        if (!empty($apiDataArray)) {
            foreach ($apiDataArray as $key => $value) {
                if ($key == $orderType) {
                    foreach ($value as $order) {
                        if ($sumAmount <= $amountLimit) {
                            $price = $order[0];
                            $amount = $order[1];
                            $sumPrice += $price * $amount;
                            $sumAmount += $amount;
                        } else
                            break;
                    }

                    $finalPrice = $sumPrice / $sumAmount;
                }
            }
        }
    }

    return $finalPrice;
}


function getBinancePrice($coinSymbol, $amountLimit)
{

    $api = $GLOBALS['binanceApiGeneralUrl'] . $coinSymbol;
    $averagePrice = calcualteAveragePrice($api, $amountLimit, 'asks');
    return $averagePrice;
}


function getNobitexPrice($coinSymbol, $amountLimit)
{

    $api = $GLOBALS['nobitexApiGeneralUrl'] . $coinSymbol;
    $averagePrice = calcualteAveragePrice($api, $amountLimit, 'bids');
    $averagePrice = round($averagePrice, 0);
    return substr($averagePrice, 0, -1);  //substr is for convert rials to tomans(ignore last digit of number)
}


function calculateTetherSalePrice()
{
    $finalPrice = 0;
    $nobitexPrice = getNobitexPrice('USDTIRT', $GLOBALS['tetherAmount']);
    $binancePrice = getBinancePrice('USDTTRY', $GLOBALS['tetherAmount']);
    if ($nobitexPrice != 0 && $binancePrice != 0) {
        $finalPrice = round($nobitexPrice / $binancePrice);
        return round($finalPrice * $GLOBALS['profitPercent'], 0);
    }

    return 0;
}
add_shortcode('calculateTetherSalePrice', 'calculateTetherSalePrice');



function calculateTetherBuyPrice()
{
    $liraSellPrice = calculateTetherSalePrice();
    if ($liraSellPrice != 0)
        return $liraSellPrice - 80;

    return 0;
}
add_shortcode('calculateTetherBuyPrice', 'calculateTetherBuyPrice');


function calculateCoinsPriceBasedOnTether($coinSymbolUSDT, $coinSymbolIRT, $coinSymbolTRY)
{
    $coinPriceBasedOnTether = getBinancePrice($coinSymbolUSDT, 1);
    $coinAmount = round($GLOBALS['tetherAmount'] / $coinPriceBasedOnTether);

    $salePriceBasedOnRials = getNobitexPrice($coinSymbolIRT, $coinAmount);
    $salePriceBasedOnLira = getBinancePrice($coinSymbolTRY, $coinAmount);

    if ($salePriceBasedOnRials != 0 && $salePriceBasedOnLira != 0) {
        $finalPrice = round($salePriceBasedOnRials / $salePriceBasedOnLira);
        return round($finalPrice * $GLOBALS['profitPercent'], 0);
    }

    return 0;
}


function calculateDogeSalePrice()
{
    return calculateCoinsPriceBasedOnTether($GLOBALS['symbolDOGEUSDT'], $GLOBALS['symbolDOGEIRT'], $GLOBALS['symbolDOGETRY']);
}
add_shortcode('calculateDogeSalePrice', 'calculateDogeSalePrice');


function calculateDogeBuyPrice()
{
    $dogeSalePrice = calculateDogeSalePrice();
    if ($dogeSalePrice != 0)
        return $dogeSalePrice - 80;

    return 0;
}
add_shortcode('calculateDogeBuyPrice', 'calculateDogeBuyPrice');



function calculateAdaSalePrice()
{
    return calculateCoinsPriceBasedOnTether($GLOBALS['symbolADAUSDT'], $GLOBALS['symbolADAIRT'], $GLOBALS['symbolADATRY']);
}
add_shortcode('calculateAdaSalePrice', 'calculateAdaSalePrice');


function calculateAdaBuyPrice()
{
    $adaSalePrice = calculateAdaSalePrice();
    if ($adaSalePrice != 0)
        return $adaSalePrice - 80;

    return 0;
}
add_shortcode('calculateAdaBuyPrice', 'calculateAdaBuyPrice');


function calculateEosSalePrice()
{
    return calculateCoinsPriceBasedOnTether($GLOBALS['symbolEOSUSDT'], $GLOBALS['symbolEOSIRT'], $GLOBALS['symbolEOSTRY']);
}
add_shortcode('calculateEosSalePrice', 'calculateEosSalePrice');


function calculateEosBuyPrice()
{
    $eosSalePrice = calculateEosSalePrice();
    if ($eosSalePrice != 0)
        return $eosSalePrice - 80;

    return 0;
}
add_shortcode('calculateEosBuyPrice', 'calculateEosBuyPrice');


function calculateDotSalePrice()
{
    return calculateCoinsPriceBasedOnTether($GLOBALS['symbolDOTUSDT'], $GLOBALS['symbolDOTIRT'], $GLOBALS['symbolDOTTRY']);

}
add_shortcode('calculateDotSalePrice', 'calculateDotSalePrice');


function calculateDotBuyPrice()
{
    $dotSalePrice = calculateDotSalePrice();
    if ($dotSalePrice != 0)
        return $dotSalePrice - 80;

    return 0;
}
add_shortcode('calculateDotBuyPrice', 'calculateDotBuyPrice');


// function calculateLtcSalePrice()
// {
//     return calculateCoinsPriceBasedOnTether($GLOBALS['symbolLTCUSDT'], $GLOBALS['symbolLTCIRT'], $GLOBALS['symbolLTCTRY']);

// }
// //add_shortcode('calculateLtcSalePrice', 'calculateLtcSalePrice');

// function calculateLtcBuyPrice()
// {
//     $ltcSalePrice = calculateLtcSalePrice();
//     if ($ltcSalePrice != 0)
//         return $ltcSalePrice - 80;

//     return 0;
// }
// //add_shortcode('calculateLtcBuyPrice', 'calculateLtcBuyPrice');


function calculateFtmSalePrice()
{
    return calculateCoinsPriceBasedOnTether($GLOBALS['symbolFTMUSDT'], $GLOBALS['symbolFTMIRT'], $GLOBALS['symbolFTMTRY']);

}
add_shortcode('calculateFtmSalePrice', 'calculateFtmSalePrice');

function calculateFtmBuyPrice()
{
    $ftmSalePrice = calculateFtmSalePrice();
    if ($ftmSalePrice != 0)
        return $ftmSalePrice - 80;

    return 0;
}
add_shortcode('calculateFtmBuyPrice', 'calculateFtmBuyPrice');


function calculateTrxSalePrice()
{
    return calculateCoinsPriceBasedOnTether($GLOBALS['symbolTRXUSDT'], $GLOBALS['symbolTRXIRT'], $GLOBALS['symbolTRXTRY']);

}
add_shortcode('calculateTrxSalePrice', 'calculateTrxSalePrice');

function calculateTrxBuyPrice()
{
    $trxSalePrice = calculateTrxSalePrice();
    if ($trxSalePrice != 0)
        return $trxSalePrice - 80;

    return 0;
}
add_shortcode('calculateTrxBuyPrice', 'calculateTrxBuyPrice');