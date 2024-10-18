<?php

if(!isset($argv[1])) die("Race Date Not Entered!!\n");
$raceDate = trim($argv[1]);
if(!isset($argv[2])) $venue = "ST";
else $venue = trim($argv[2]);

$currentDir = __DIR__ . DIRECTORY_SEPARATOR . $raceDate . $venue;
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
$unions = [];
$experimental = [];
$basicBet = 10;
$winBet = 10;
$wpBet = 3 * $basicBet;
foreach($mainData as $raceNumber => $shit) $bets[$raceNumber] = ['favorites' => '(F) ' . $mainData[$raceNumber]['favorites']];
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
            if(isset($oldData[$raceNumber]["unions(\$$winBet)"])) $oldUnions = explode(", ", $oldData[$raceNumber]["unions(\$$winBet)"]);
            else $oldUnions = [];
            if(isset($oldData[$raceNumber]["experimental(\$$winBet)"])) $oldExperimental = explode(", ", $oldData[$raceNumber]["experimental(\$$winBet)"]);
            else $oldExperimental = [];
            if(isset($oldData[$raceNumber]["sures(\$$basicBet)"])) $oldSures = explode(", ", $oldData[$raceNumber]["sures(\$$basicBet)"]);
            else $oldSures = [];
            if(isset($oldData[$raceNumber]["super sures(\$$basicBet)"])) $oldSupersures = explode(", ", $oldData[$raceNumber]["super sures(\$$basicBet)"]);
            else $oldSupersures = [];
            if(!isset($placesWP[$raceNumber])) $placesWP[$raceNumber] = [];
            if(!isset($unions[$raceNumber])) $unions[$raceNumber] = [];
            if(!isset($experimental[$raceNumber])) $experimental[$raceNumber] = [];
            if(!isset($placesEndWP[$raceNumber])) $placesEndWP[$raceNumber] = [];
            if(!isset($placesEndFav[$raceNumber])) $placesEndFav[$raceNumber] = [];
            if(isset($data['bets'])) {
                foreach($data['bets'] as $key => $value){
                    if(!in_array($value, $bets[$raceNumber])) {
                        $bets[$raceNumber][$key] = $value;
                    }
                    if(strpos($key, "win(union") === 0){
                        $unions[$raceNumber] = array_values(array_unique(array_merge($unions[$raceNumber], explode(", ", $value))));
                    } 
                    if(strpos($key, "win(experimental") === 0){
                        $experimental[$raceNumber] = array_values(array_unique(array_merge($experimental[$raceNumber], explode(", ", $value))));
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
            $oldUnions = array_values(array_unique(array_merge($oldUnions, $unions[$raceNumber])));
            sort($oldUnions);
            $oldExperimental = array_values(array_unique(array_merge($oldExperimental, $experimental[$raceNumber])));
            sort($oldExperimental);
            if(!empty($newSures)) {
                $oldSures = array_values(array_unique(array_merge($oldSures, $newSures)));
            }
            sort($oldPlaces);
            sort($oldSures);
            sort($oldSupersures);
            if(!empty($oldPlaces)) $bets[$raceNumber]["places(\$$basicBet)"] = implode(", ", $oldPlaces);
            if(!empty($oldSures)) $bets[$raceNumber]["sures(\$$basicBet)"] = implode(", ", $oldSures);
            if(!empty($oldPlacesWP)) $bets[$raceNumber]["placesWP(\$$wpBet)"] = implode(", ", $oldPlacesWP);
            if(!empty($oldUnions)) $bets[$raceNumber]["unions(\$$winBet)"] = implode(", ", $oldUnions);
            if(!empty($oldExperimental)) $bets[$raceNumber]["experimental(\$$winBet)"] = implode(", ", $oldExperimental);
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