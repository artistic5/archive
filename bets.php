
<?php

require 'functions.php';

if(!isset($argv[1])) die("Race Date Not Entered!!\n");

$raceDate = trim($argv[1]);

if(!isset($argv[2])) $venue = "ST";
else $venue = trim($argv[2]);

if(isset($argv[3])) $revision = trim($argv[3]);
else $revision = "";

$step = "bets$revision";
$history = include(__DIR__ . DIRECTORY_SEPARATOR . "history$revision.php");
$favhistory = include(__DIR__ . DIRECTORY_SEPARATOR . "favhistory.php");

function factorial($n){
    if($n <= 0) return 1;
    $fact = 1;
    for($i = 1; $i <= $n; $i++) $fact *= $i;
    return $fact;
}
function combination($p, $n){
    if($n < $p) return 0;
    return factorial($n) / (factorial($p) * factorial($n - $p));
}

$total = 0;
$totalPlaceEndF = 0;
$totalPlaceEndW = 0;
$totalPlaceW = 0;
$totalSurePlace = 0;
$totalWin = 0;

$currentDir = __DIR__ . DIRECTORY_SEPARATOR . $raceDate . $venue;

$oddsFile = $currentDir . DIRECTORY_SEPARATOR . "odds.php";
$winOddsFile = $currentDir . DIRECTORY_SEPARATOR . "winodds.php";
if(file_exists($winOddsFile)) $allWinOdds = include($winOddsFile);
$allRacesOdds = include($oddsFile);
$outFile = $currentDir . DIRECTORY_SEPARATOR . "$step.php";

if(file_exists($outFile)){
    $oldData = include($outFile);
}

$numberOfRaces = count($allRacesOdds);

$outtext = "<?php\n\n";
$outtext .= "return [\n";

$totalBets = [];
$totalRace = [];

for ($raceNumber = 1; $raceNumber <= $numberOfRaces; $raceNumber++) {
    $totalBets[$raceNumber] = 0;
    $totalRace[$raceNumber] = 0;
    if(isset($oldData)){
        if(isset($oldData[$raceNumber]['favorites'])) $oldFavorites = explode(", ", $oldData[$raceNumber]['favorites']);
        if(isset($oldData[$raceNumber]['official win'])) $officialWin = explode(", ", $oldData[$raceNumber]['official win']);
        if(isset($oldData[$raceNumber]['win amount'])) $winAmount = $oldData[$raceNumber]['win amount'];
        if(isset($oldData[$raceNumber]['qin amount'])) $qinAmount = $oldData[$raceNumber]['qin amount'];
        if(isset($oldData[$raceNumber]['trio amount'])) $trioAmount = $oldData[$raceNumber]['trio amount'];
        if(isset($oldData[$raceNumber]['place amount'])) $placeAmount = $oldData[$raceNumber]['place amount'];
    }
    if(isset($oldFavorites)) $favorites = $oldFavorites;
    else $favorites = [];
    $winsArray = $allRacesOdds[$raceNumber];
    if(empty($winsArray)) continue;
    asort($winsArray);
    $runners = array_keys($winsArray);
    if(isset($allWinOdds)){
        $tmpwinsArray = $allWinOdds[$raceNumber];
        asort($tmpwinsArray);
        $tmprunners = array_keys($tmpwinsArray);
        if(!in_array($tmprunners[0], $favorites)) $favorites[] = $tmprunners[0];
    }
    if(!in_array($runners[0], $favorites)) $favorites[] = $runners[0];
    $favorites = array_intersect($favorites, $runners);
    sort($favorites);
    $_2sets = get2Sets($favorites);
    $suggestions = ["win" => [], "qin" => [], "trio" => []];
    $firstShit = true;
    $winInter2 = [];
    foreach($_2sets as $example){
        if(isset($favhistory[$raceNumber][$example])) {
            if($firstShit){
                $winInter2 = $favhistory[$raceNumber][$example]["win"];
                $firstShit = false;
            }
            else $winInter2 = array_intersect($winInter2, $favhistory[$raceNumber][$example]["win"]);
            $suggestions["win"] = array_values(array_unique(array_merge($suggestions["win"], $favhistory[$raceNumber][$example]["win"])));
            $suggestions["qin"] = array_values(array_unique(array_merge($suggestions["qin"], $favhistory[$raceNumber][$example]["qin"])));
            $suggestions["trio"] = array_values(array_unique(array_merge($suggestions["trio"], $favhistory[$raceNumber][$example]["trio"])));
        }
    }
    $winInter2 = array_intersect($winInter2, $runners);
    $suggestions["win"] = array_intersect($suggestions["win"], $runners);
    $suggestions["qin"] = array_intersect($suggestions["qin"], $runners);
    $suggestions["trio"] = array_intersect($suggestions["trio"], $runners);
    sort($runners);
    $racetext = "";
    $racetext .= "\t'$raceNumber' => [\n";
    $racetext .= "\t\t/**\n";
    $racetext .= "\t\tRace $raceNumber\n";
    $racetext .= "\t\t*/\n";
    $racetext .= "\t\t'favorites' => '" . implode(", ", $favorites) . "',\n"; 
    $racetext .= "\t\t'runners' => '" . implode(", ", $runners) . "',\n"; 
    foreach($runners as $runner){
        if(!isset($history[$raceNumber][$runner])) $history[$raceNumber][$runner] =  ["win" => [], "qin" => [], "trio" => []];
    }
    if(isset($officialWin)){
        $racetext .= "\t\t'official win' => '" . implode(", ", $officialWin) . "',\n"; 
    }
    sort($suggestions["win"]);
    sort($suggestions["qin"]);
    sort($suggestions["trio"]);
    
    $racetext .= "\t\t'suggestions' => [\n";
    $racetext .= "\t\t\t'win' => '" . implode(", ", $suggestions["win"]) . "',\n";
    $racetext .= "\t\t\t'qin' => '" . implode(", ", $suggestions["qin"]) . "',\n";
    $racetext .= "\t\t\t'trio' => '" . implode(", ", $suggestions["trio"]) . "',//count trio: " . count($suggestions["trio"]) . "\n";
    $inter = array_intersect($favorites, $suggestions["win"]);
    $racetext .= "\t\t\t'inter' => '" . implode(", ", $inter) . "',\n";
    
    $racetext .= "\t\t],\n";
    if(isset($winAmount)){
        $racetext .= "\t\t'win amount' => " . $winAmount . ",\n"; 
    }
    if(isset($qinAmount)){
        $racetext .= "\t\t'qin amount' => " . $qinAmount . ",\n"; 
    }
    if(isset($trioAmount)){
        $racetext .= "\t\t'trio amount' => " . $trioAmount . ",\n"; 
    }
    if(isset($placeAmount)){
        $racetext .= "\t\t'place amount' => [\n";
        foreach($placeAmount as $place => $amount){
            $racetext .= "\t\t\t$place => $amount,\n";
        }
        $racetext .= "\t\t],\n"; 
    }
    $intersections = [];
    $firstSet = true;
    foreach($favorites as $F){
        $wincandidates = array_intersect($history[$raceNumber][$F]["win"], $runners);
        if($firstSet) {
            $winInter = $wincandidates;
            $firstSet = false;
        }
        else {
            $winInter = array_intersect($winInter, $wincandidates);
        }
    }
    sort($winInter);
    $racetext .= "\t\t'win inter' => '" . implode(", ", $winInter) . "',\n";
    if(!empty($winInter2)){
        $racetext .= "\t\t'win inter 2' => '" . implode(", ", $winInter2) . "',\n";
    }
    $unitBet = 100;
    $allValues = [];
    foreach($runners  as $one){
        foreach($runners as $two){
            if($two > $one){
                foreach($runners as $three){
                    if($three > $two){
                        $set = [$one, $two, $three];
                        $temp = array_intersect($history[$raceNumber][$one]["win"], $history[$raceNumber][$two]["win"], $history[$raceNumber][$three]["win"], $runners);
                        $temp = array_intersect($set, $temp);
                        foreach($runners as $four){
                            if($four > $three){
                                $set = [$one, $two, $three, $four];
                                $temp = array_intersect($temp, $history[$raceNumber][$four]["win"]);
                                $temp = array_intersect($set, $temp);
                                if(count($temp) >= 2){
                                    $allValues = array_values(array_unique(array_merge($allValues, $set)));
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    sort($allValues);
    $racetext .= "\t\t'allValues' => '" . implode(", ", $allValues) . "',\n";
    $condition1 = !empty($winInter2);
    $condition2 = !empty($winInter);
    $racetext .= "\t\t'bets' => [\n";
    if(count($favorites) > 1 && !empty($winInter) && empty(array_intersect($winInter, $favorites))) {
        $union = array_values(array_unique(array_merge($winInter, $favorites)));
        sort($union);
        if(count($union) === 4){
            $racetext .= "\t\t\t'win(union $revision)' => '" . implode(", ", $union) . "',\n"; 
            $totalBets[$raceNumber] += $unitBet * count($union);
            $totalWin -= $unitBet * count($union);
            if(isset($officialWin) && in_array($officialWin[0], $union)){
                $totalRace[$raceNumber] += 1/10 * $unitBet * $winAmount;
                $racetext .= "\t\t\t'1 won(win bet)' => " . 1/10 * $unitBet * $winAmount . ",\n";
                $totalWin += 1/10 * $unitBet * $winAmount;
            }
            $racetext .= "\t\t\t'win(experimental $revision)' => '" . implode(", ", $union) . "',\n"; 
        }
    }
    if($condition1 && $condition2 && count($favorites) >= 3){
        $racetext .= "\t\t\t'place(end-favorites $revision)' => '" .  end($favorites)  . "',\n"; 
        $totalBets[$raceNumber] += $unitBet;
        $totalPlaceEndF -= $unitBet;
        if(isset($officialWin) && in_array(end($favorites), array_slice($officialWin, 0, 3)) && isset($placeAmount[end($favorites)])){
            $totalRace[$raceNumber] += 1/10 * $unitBet * $placeAmount[end($favorites)];
            $racetext .= "\t\t\t'1 won(place bet)' => " . 1/10 * $unitBet * $placeAmount[end($favorites)] . ",\n";
            $totalPlaceEndF += 1/10 * $unitBet * $placeAmount[end($favorites)];
        }
    }
    $wp = array_intersect($allValues, $favorites);
    if(count($wp) === 3 && $condition1 && $condition2){
        $racetext .= "\t\t\t'place(end-wp $revision)' => '" . end($wp) . "',\n"; 
        $totalBets[$raceNumber] += $unitBet;
        $totalPlaceEndW -= $unitBet;
        if(isset($officialWin) && in_array(end($wp), array_slice($officialWin, 0, 3)) && isset($placeAmount[end($wp)])){
            $totalRace[$raceNumber] += (1 * $unitBet / 10) * $placeAmount[end($wp)];
            $racetext .= "\t\t\t'2 won(place bet)' => " . (1 * $unitBet / 10) * $placeAmount[end($wp)] . ",\n";
            $totalPlaceEndW += (1 * $unitBet / 10) * $placeAmount[end($wp)];
        }
    }
    if(!empty($suggestions["win"]) && count($winInter2) === 1 && count($wp) === 1){
        $wpBet = 3 * $unitBet;
        $racetext .= "\t\t\t'place(wp $revision)' => '" . implode(", ", $wp) . "',\n"; 
        $totalBets[$raceNumber] += $wpBet * count($wp);
        $totalPlaceW -= $wpBet * count($wp);
        if(isset($officialWin) && !empty(array_intersect($wp, array_slice($officialWin, 0, 3)))){
            $jackpot = array_intersect($wp, array_slice($officialWin, 0, 3));
            foreach($jackpot as $jacky){
                if(isset($placeAmount[$jacky])){
                    $totalRace[$raceNumber] += (1 * $wpBet / 10) * $placeAmount[$jacky];
                    $racetext .= "\t\t\t'3 won(place bet $jacky)' => " . (1 * $wpBet / 10) * $placeAmount[$jacky] . ",\n";
                    $totalPlaceW += (1 * $wpBet / 10) * $placeAmount[$jacky];
                }
            }
        }
    }
    if(count($favorites) >= 3 && count(array_intersect($winInter, $favorites)) >= 2 && $condition1) {
        $racetext .= "\t\t\t'super sure bet' => 'super sure place " . end($favorites) . "',\n" ;
        $totalBets[$raceNumber] += $unitBet;
        $totalSurePlace -= $unitBet;
        if(isset($officialWin) && in_array(end($favorites), array_slice($officialWin, 0, 3)) && isset($placeAmount[end($favorites)])){
            $totalRace[$raceNumber] += (1 * $unitBet / 10) * $placeAmount[end($favorites)];
            $totalSurePlace += (1 * $unitBet / 10) * $placeAmount[end($favorites)];
            $racetext .= "\t\t\t'5 won(place bet)' => " . (1 * $unitBet / 10) * $placeAmount[end($favorites)] . ",\n";
        }
    }
    $racetext .= "\t\t],\n";
    $racetext .= "\t\t'total bets' => $totalBets[$raceNumber],\n";
    $racetext .= "\t\t'wp' => '" . implode(", ", $wp) . "',//count wp: " . count($wp) . "\n";
    $totalRace[$raceNumber] -= $totalBets[$raceNumber];
    if(isset($officialWin) && $totalBets[$raceNumber] > 0){
        $racetext .= "\t\t'total won in race $raceNumber' => " . $totalRace[$raceNumber] . ",\n";
        $total += $totalRace[$raceNumber];
    }
    $racetext .= "\t],\n";
    unset($oldFavorites);
    unset($favorites);
    $outtext .= $racetext;
}
$outtext .= "];\n";
$outtext .= "//total place end favorites: $totalPlaceEndF\n";
$outtext .= "//total place end wp: $totalPlaceEndW\n";
$outtext .= "//total place wp: $totalPlaceW\n";
$outtext .= "//total sure place: $totalSurePlace\n";
$outtext .= "//total win: $totalWin\n";
$outtext .= "//total: $total\n";
file_put_contents($outFile, $outtext);
?>