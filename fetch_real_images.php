<?php

$seedDir = __DIR__ . '/public/images/seed';
if (!is_dir($seedDir)) {
    mkdir($seedDir, 0755, true);
}

// Map of local file -> [Wikipedia page title, fallback Wikimedia CDN URL]
$items = [
    // Museums
    'british_museum.jpg' => [
        'page' => 'British Museum',
        'fallback' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/3/3a/British_Museum_from_NE_2.JPG/1200px-British_Museum_from_NE_2.JPG'
    ],
    'met_museum.jpg' => [
        'page' => 'Metropolitan Museum of Art',
        'fallback' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/3/30/Metropolitan_Museum_of_Art_%28left_side%2C_2020%29.jpg/1200px-Metropolitan_Museum_of_Art_%28left_side%2C_2020%29.jpg'
    ],
    'louvre_museum.jpg' => [
        'page' => 'Louvre',
        'fallback' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/6/66/Louvre_Museum_Courtyard_Pyramid_Paris.jpg/1200px-Louvre_Museum_Courtyard_Pyramid_Paris.jpg'
    ],
    'egyptian_museum.jpg' => [
        'page' => 'Egyptian Museum',
        'fallback' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/3/31/Cairo_Egyptian_Museum_2022.jpg/1200px-Cairo_Egyptian_Museum_2022.jpg'
    ],
    'vatican_museums.jpg' => [
        'page' => 'Vatican Museums',
        'fallback' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/e/e3/Bramante_Staircase_in_the_Vatican_Museums.jpg/1200px-Bramante_Staircase_in_the_Vatican_Museums.jpg'
    ],
    'tokyo_museum.jpg' => [
        'page' => 'Tokyo National Museum',
        'fallback' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/9/91/Tokyo_National_Museum_Honkan_201705.jpg/1200px-Tokyo_National_Museum_Honkan_201705.jpg'
    ],
    'mexico_anthropology.jpg' => [
        'page' => 'National Museum of Anthropology (Mexico)',
        'fallback' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/c/c5/Museo_Nacional_de_Antropolog%C3%ADa_e_Historia_%28M%C3%A9xico%29_-_Fountain.jpg/1200px-Museo_Nacional_de_Antropolog%C3%ADa_e_Historia_%28M%C3%A9xico%29_-_Fountain.jpg'
    ],
    'acropolis_museum.jpg' => [
        'page' => 'Acropolis Museum',
        'fallback' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/d/d4/Acropolis_Museum_Athens_2017.jpg/1200px-Acropolis_Museum_Athens_2017.jpg'
    ],
    'iraq_museum.jpg' => [
        'page' => 'Iraq Museum',
        'fallback' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/c/c8/Iraq_Museum_Baghdad.jpg/1200px-Iraq_Museum_Baghdad.jpg'
    ],
    'pergamon_museum.jpg' => [
        'page' => 'Pergamon Museum',
        'fallback' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8e/Pergamonmuseum_Berlin_2007.jpg/1200px-Pergamonmuseum_Berlin_2007.jpg'
    ],
    'taipei_museum.jpg' => [
        'page' => 'National Palace Museum',
        'fallback' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8c/National_Palace_Museum_main_building_2019.jpg/1200px-National_Palace_Museum_main_building_2019.jpg'
    ],

    // Artifacts
    'rosetta_stone.jpg' => [
        'page' => 'Rosetta Stone',
        'fallback' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/2/23/Rosetta_Stone.JPG/1200px-Rosetta_Stone.JPG'
    ],
    'sutton_hoo_helmet.jpg' => [
        'page' => 'Sutton Hoo helmet',
        'fallback' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/a/a2/Sutton_Hoo_helmet_original_%28frontal_view%29.jpg/1200px-Sutton_Hoo_helmet_original_%28frontal_view%29.jpg'
    ],
    'temple_of_dendur.jpg' => [
        'page' => 'Temple of Dendur',
        'fallback' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/e/ee/The_Temple_of_Dendur%2C_Sackler_Wing%2C_Metropolitan_Museum_of_Art_%2848149179948%29.jpg/1200px-The_Temple_of_Dendur%2C_Sackler_Wing%2C_Metropolitan_Museum_of_Art_%2848149179948%29.jpg'
    ],
    'washington_crossing.jpg' => [
        'page' => 'Emanuel Leutze',
        'fallback' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/9/9d/Emanuel_Leutze_-_Washington_Crossing_the_Delaware_-_Metropolitan_Museum_of_Art.jpg/800px-Emanuel_Leutze_-_Washington_Crossing_the_Delaware_-_Metropolitan_Museum_of_Art.jpg'
    ],
    'venus_de_milo.jpg' => [
        'page' => 'Venus de Milo',
        'fallback' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/c/c2/Front_views_of_the_Venus_de_Milo.jpg/1200px-Front_views_of_the_Venus_de_Milo.jpg'
    ],
    'code_of_hammurabi.jpg' => [
        'page' => 'Code of Hammurabi',
        'fallback' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/e/e8/Code_of_Hammurabi_Stele_-_Louvre_-_MR_6_-_2021-08-31.jpg/1200px-Code_of_Hammurabi_Stele_-_Louvre_-_MR_6_-_2021-08-31.jpg'
    ],
    'tut_mask.jpg' => [
        'page' => 'Mask of Tutankhamun',
        'fallback' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/3/38/Tutankhamun_Mask.JPG/1200px-Tutankhamun_Mask.JPG'
    ],
    'narmer_palette.jpg' => [
        'page' => 'Narmer Palette',
        'fallback' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/b/b1/Narmer_Palette.jpg/1200px-Narmer_Palette.jpg'
    ],
    'laocoon.jpg' => [
        'page' => 'Laocoön and His Sons',
        'fallback' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/17/Laocoon_and_His_Sons.jpg/1200px-Laocoon_and_His_Sons.jpg'
    ],
    'lyre_of_ur.jpg' => [
        'page' => 'Lyres of Ur',
        'fallback' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/a/a2/Queen%27s_Lyre_from_Sumerian_Royal_Cemetery_at_Ur%2C_Mesopotamia%2C_c._2600_BC_%28British_Museum%29_%2827943547844%29.jpg/1200px-Queen%27s_Lyre_from_Sumerian_Royal_Cemetery_at_Ur%2C_Mesopotamia%2C_c._2600_BC_%28British_Museum%29_%2827943547844%29.jpg'
    ],
    'jadeite_cabbage.jpg' => [
        'page' => 'Jadeite Cabbage',
        'fallback' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/9/99/National_Palace_Museum_Jadeite_Cabbage.jpg/1200px-National_Palace_Museum_Jadeite_Cabbage.jpg'
    ],
    'ishtar_gate.jpg' => [
        'page' => 'Ishtar Gate',
        'fallback' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/e/e3/Ishtar_Gate_at_Pergamon_Museum.jpg/1200px-Ishtar_Gate_at_Pergamon_Museum.jpg'
    ],
    'roman_fibula.jpg' => [
        'page' => 'Fibula (brooch)',
        'fallback' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/a/a3/Roman_bronze_fibula_%28FindID_637213%29.jpg/1200px-Roman_bronze_fibula_%28FindID_637213%29.jpg'
    ],
    'greek_amphora.jpg' => [
        'page' => 'Black-figure pottery',
        'fallback' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/b/b3/Herakles_Nemean_lion_Louvre_F255.jpg/1200px-Herakles_Nemean_lion_Louvre_F255.jpg'
    ],

    // Collections & Exhibitions
    'col_egypt.jpg' => [
        'page' => 'Ancient Egypt',
        'fallback' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/c/c2/All_Giza_Pyramids.jpg/1200px-All_Giza_Pyramids.jpg'
    ],
    'col_classical.jpg' => [
        'page' => 'Classical antiquity',
        'fallback' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/d/d2/Parthenon_from_south_west.jpg/1200px-Parthenon_from_south_west.jpg'
    ],
    'col_mesopotamia.jpg' => [
        'page' => 'Mesopotamia',
        'fallback' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/87/Lamassu_from_the_North-West_Palace_of_Ashurnasirpal_II_%28British_Museum%29.jpg/1200px-Lamassu_from_the_North-West_Palace_of_Ashurnasirpal_II_%28British_Museum%29.jpg'
    ],
    'exh_sun_kings.jpg' => [
        'page' => 'Ramesses II',
        'fallback' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/5/53/Great_Temple_of_Ramesses_II_at_Abu_Simbel.jpg/1200px-Great_Temple_of_Ramesses_II_at_Abu_Simbel.jpg'
    ],
    'exh_athena.jpg' => [
        'page' => 'Acropolis of Athens',
        'fallback' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/c/c1/The_Acropolis_of_Athens_viewed_from_the_Hill_of_the_Muses_%2814220794964%29.jpg/1200px-The_Acropolis_of_Athens_viewed_from_the_Hill_of_the_Muses_%2814220794964%29.jpg'
    ],
    'exh_silk_road.jpg' => [
        'page' => 'Silk Road',
        'fallback' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/f/fa/Mogao_Caves_9.jpg/1200px-Mogao_Caves_9.jpg'
    ]
];

function downloadImage($url, $destination) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36 Artevo/1.0');
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $data = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($status === 200 && $data && strlen($data) > 1000) {
        file_put_contents($destination, $data);
        return true;
    }
    return false;
}

echo "Starting download of real museum and artifact images...\n";

foreach ($items as $filename => $info) {
    $targetPath = $seedDir . '/' . $filename;
    
    // Check if already exists and valid size (>10KB)
    if (file_exists($targetPath) && filesize($targetPath) > 10000) {
        echo "Already downloaded: $filename\n";
        continue;
    }

    echo "Fetching image for: {$info['page']} -> $filename ... ";
    
    $imageUrl = null;
    // Try Wikipedia API first
    $apiUrl = 'https://en.wikipedia.org/w/api.php?action=query&titles=' . urlencode($info['page']) . '&prop=pageimages&format=json&pithumbsize=1200';
    
    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Artevo/1.0 (contact@artevo.test) Mozilla/5.0');
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $apiRes = curl_exec($ch);
    curl_close($ch);

    if ($apiRes) {
        $json = json_decode($apiRes, true);
        if (isset($json['query']['pages'])) {
            foreach ($json['query']['pages'] as $pageData) {
                if (isset($pageData['thumbnail']['source'])) {
                    $imageUrl = $pageData['thumbnail']['source'];
                    break;
                }
            }
        }
    }

    $success = false;
    if ($imageUrl) {
        $success = downloadImage($imageUrl, $targetPath);
    }
    
    if (!$success && !empty($info['fallback'])) {
        $success = downloadImage($info['fallback'], $targetPath);
    }

    if ($success) {
        echo "SUCCESS (" . round(filesize($targetPath) / 1024, 1) . " KB)\n";
    } else {
        echo "FAILED\n";
    }
}

echo "\nAll done.\n";
