<?php
// Remote M3U URL
$m3uUrl = "https://raw.githubusercontent.com/shihaab-islam/iptv-playlist-by-shihaab/refs/heads/main/iptv-playlist-by-shihaab.m3u";

// Fetch remote playlist (with fallback)
function fetchM3U($url){
    // Use cURL if allow_url_fopen is disabled
    if(function_exists('curl_init')){
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    } else {
        return @file_get_contents($url);
    }
}

// Parse playlist
function parseM3UContent($text){
    $lines=preg_split("/\r\n|\n|\r/",$text);
    $channels=[];
    $title=""; $logo=""; $group="";
    foreach($lines as $line){
        $line=trim($line);
        if(strpos($line,"#EXTINF:")===0){
            $titlePart = explode(",", $line, 2);
            $title = isset($titlePart[1]) ? trim($titlePart[1]) : "Untitled";
            preg_match('/tvg-logo="(.*?)"/',$line,$m1); $logo=$m1[1]??"";
            preg_match('/group-title="(.*?)"/',$line,$m2); $group=$m2[1]??"General";
        }elseif($line && $line[0]!="#"){
            $channels[]=["title"=>$title,"logo"=>$logo,"group"=>$group,"url"=>$line];
            $title=$logo=$group="";
        }
    }
    return $channels;
}

// Get and parse
$data = fetchM3U($m3uUrl);
$channels = $data ? parseM3UContent($data) : [];
