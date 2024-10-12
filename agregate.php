<?php

if(!isset($argv[1])) die("Race Date Not Entered!!\n");
$raceDate = trim($argv[1]);

$currentDir = __DIR__ . DIRECTORY_SEPARATOR . $raceDate;
$outFile = $currentDir . DIRECTORY_SEPARATOR . "agregate.php";

if(file_exists($outFile)) $oldData = include($outFile);

$mainBetsFile = $currentDir . DIRECTORY_SEPARATOR . "bets.php";
$mainData = include($mainBetsFile);
$numberOfRaces = count($mainData);
$outtext = "<?php\n\n";
$outtext .= "return [\n";

$bets = [];
$placesEndFav = [];
$placesEndWP = [];
$placesWP = [];
$basicBet = 50;
$wpBet = 3 * $basicBet;
for($raceNumber = 1; $raceNumber <= $numberOfRaces; $raceNumber ++) $bets[$raceNumber] = ['favorites' => '(F) ' . $mainData[$raceNumber]['favorites']];
$dir = new DirectoryIterator($currentDir); 
foreach ($dir as $fileinfo) {
    if(!$fileinfo->isDot()&& preg_match("/(bets)/", $fileinfo->getFilename())) {
        $fullFilePath = $currentDir . DIRECTORY_SEPARATOR . $fileinfo->getFilename();
        $fileContents = include($fullFilePath);
        foreach($fileContents as $raceNumber => $data){
            if(isset($oldData[$raceNumber]["places(\$$basicBet)"])) $oldPlaces = explode(", ", $oldData[$raceNumber]["places(\$$basicBet)"]);
            else $oldPlaces = [];
            if(isset($oldData[$raceNumber]["placesWP(\$$wpBet)"])) $oldPlacesWP = explode(", ", $oldData[$raceNumber]["placesWP(\$$wpBet)"]);
            else $oldPlacesWP = [];
            if(isset($oldData[$raceNumber]["sures(\$$basicBet)"])) $oldSures = explode(", ", $oldData[$raceNumber]["sures(\$$basicBet)"]);
            else $oldSures = [];
            if(isset($oldData[$raceNumber]["super sures(\$$basicBet)"])) $oldSupersures = explode(", ", $oldData[$raceNumber]["super sures(\$$basicBet)"]);
            else $oldSupersures = [];
            if(!isset($placesWP[$raceNumber])) $placesWP[$raceNumber] = [];
            if(!isset($placesEndWP[$raceNumber])) $placesEndWP[$raceNumber] = [];
            if(!isset($placesEndFav[$raceNumber])) $placesEndFav[$raceNumber] = [];
            if(isset($data['bets'])) {
                foreach($data['bets'] as $key => $value){
                    if(!in_array($value, $bets[$raceNumber])) {
                        $bets[$raceNumber][$key] = $value;
                    }
                    if(strpos($key, "place(wp") === 0 && !in_array($value, $placesWP[$raceNumber])) $placesWP[$raceNumber][] = $value;
                    if(strpos($key, "place(end-wp") === 0 && !in_array($value, $placesEndWP[$raceNumber])) $placesEndWP[$raceNumber][] = $value;
                    if(strpos($key, "place(end-fa") === 0 && !in_array($value, $placesEndFav[$raceNumber])) $placesEndFav[$raceNumber][] = $value;
                    if(strpos($key, "super sure") === 0){
                        $parts = explode(" ", $value);
                        if(!in_array(end($parts), $oldSupersures)) $oldSupersures[] = end($parts);
                    }
                }
            }
            $newSures = array_intersect($placesEndFav[$raceNumber], $placesEndWP[$raceNumber]);
            $oldPlaces = array_values(array_unique(array_merge($oldPlaces, $placesEndFav[$raceNumber], $placesEndWP[$raceNumber])));
            $oldPlacesWP = array_values(array_unique(array_merge($oldPlacesWP, $placesWP[$raceNumber])));
            if(!empty($places)) {
                $oldSures = array_values(array_unique(array_merge($oldSures, $newSures)));
            }
            sort($oldPlaces);
            sort($oldSures);
            sort($oldSupersures);
            if(!empty($oldPlaces)) $bets[$raceNumber]["places(\$$basicBet)"] = implode(", ", $oldPlaces);
            if(!empty($oldSures)) $bets[$raceNumber]["sures(\$$basicBet)"] = implode(", ", $oldSures);
            if(!empty($oldPlacesWP)) $bets[$raceNumber]["placesWP(\$$wpBet)"] = implode(", ", $oldPlacesWP);
            if(!empty($oldSupersures)) $bets[$raceNumber]["super sures(\$$basicBet)"] = implode(", ", $oldSupersures);
        }
    }
}
foreach($bets as $raceNumber => $data){
    if(!empty($data)){
        $racetext = "\t'$raceNumber' => [\n";
        $racetext .= "\t\t/**\n";
        $racetext .= "\t\tRace $raceNumber\n";
        $racetext .= "\t\t*/\n";
        foreach($data as $betDescription => $betContent) $racetext .= "\t\t'$betDescription' => '$betContent',\n";
        $racetext .= "\t],\n";
        $outtext .= $racetext;
    }
}
$outtext .= "];\n";
file_put_contents($outFile, $outtext);
?>
