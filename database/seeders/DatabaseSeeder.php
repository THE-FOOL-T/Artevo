<?php

namespace Database\Seeders;

use App\Models\Artifact;
use App\Models\ArtifactCategory;
use App\Models\ArtifactImage;
use App\Models\ArtifactMaterial;
use App\Models\ArtifactTag;
use App\Models\Auction;
use App\Models\Collection;
use App\Models\Exhibition;
use App\Models\ExhibitionSection;
use App\Models\Museum;
use App\Models\MuseumContact;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Fixed demo accounts ─────────────────────────────────────────────
        $admin = User::updateOrCreate(
            ['email' => 'admin@artevo.test'],
            [
                'name'              => 'Artevo Admin',
                'email_verified_at' => now(),
                'password'          => Hash::make('password'),
                'role'              => User::ROLE_ADMIN,
                'remember_token'    => Str::random(10),
            ]
        );

        $curator = User::updateOrCreate(
            ['email' => 'curator@artevo.test'],
            [
                'name'              => 'Nadia Farouk',
                'email_verified_at' => now(),
                'password'          => Hash::make('password'),
                'role'              => User::ROLE_CURATOR,
                'remember_token'    => Str::random(10),
            ]
        );

        $collector = User::updateOrCreate(
            ['email' => 'collector@artevo.test'],
            [
                'name'              => 'Marcus Webb',
                'email_verified_at' => now(),
                'password'          => Hash::make('password'),
                'role'              => User::ROLE_COLLECTOR,
                'remember_token'    => Str::random(10),
            ]
        );

        User::updateOrCreate(
            ['email' => 'visitor@artevo.test'],
            [
                'name'              => 'Visitor Account',
                'email_verified_at' => now(),
                'password'          => Hash::make('password'),
                'role'              => User::ROLE_VISITOR,
                'remember_token'    => Str::random(10),
            ]
        );

        // Extra curators for the non-demo museums
        $curator2 = User::updateOrCreate(
            ['email' => 'curator2@artevo.test'],
            [
                'name'              => 'James Whitmore',
                'email_verified_at' => now(),
                'password'          => Hash::make('password'),
                'role'              => User::ROLE_CURATOR,
                'remember_token'    => Str::random(10),
            ]
        );

        $curator3 = User::updateOrCreate(
            ['email' => 'curator3@artevo.test'],
            [
                'name'              => 'Amara Osei',
                'email_verified_at' => now(),
                'password'          => Hash::make('password'),
                'role'              => User::ROLE_CURATOR,
                'remember_token'    => Str::random(10),
            ]
        );

        // ── Artifact categories (real museum taxonomy) ──────────────────────
        $categories = [];
        $categoryData = [
            ['name' => 'Sculpture',      'description' => 'Three-dimensional works carved, modeled, or cast from stone, clay, metal, or other materials.'],
            ['name' => 'Painting',        'description' => 'Works executed in pigment on canvas, wood, paper, or other surfaces.'],
            ['name' => 'Pottery & Ceramics', 'description' => 'Vessels, tiles, and objects made from fired clay.'],
            ['name' => 'Jewelry',         'description' => 'Personal adornments crafted from precious metals, gemstones, and other materials.'],
            ['name' => 'Manuscript',      'description' => 'Handwritten texts, scrolls, and illuminated codices.'],
            ['name' => 'Weapons & Armor', 'description' => 'Swords, spears, shields, helmets, and complete suits of armor.'],
            ['name' => 'Coins & Currency','description' => 'Minted coins, medallions, and other monetary objects.'],
            ['name' => 'Textile',         'description' => 'Woven, embroidered, and printed fabrics including tapestries and garments.'],
            ['name' => 'Architecture',    'description' => 'Architectural fragments, reliefs, column capitals, and structural elements.'],
            ['name' => 'Tools & Utensils','description' => 'Everyday implements used for food preparation, agriculture, and craftsmanship.'],
        ];

        foreach ($categoryData as $cat) {
            $categories[] = ArtifactCategory::firstOrCreate(
                ['name' => $cat['name']],
                ['slug' => Str::slug($cat['name']), 'description' => $cat['description']]
            );
        }

        // ── Artifact materials ───────────────────────────────────────────────
        $materials = [];
        $materialNames = [
            'Marble', 'Limestone', 'Bronze', 'Gold', 'Silver', 'Iron',
            'Terracotta', 'Papyrus', 'Wood', 'Ivory', 'Obsidian', 'Lapis Lazuli',
        ];
        foreach ($materialNames as $mat) {
            $materials[] = ArtifactMaterial::firstOrCreate(
                ['name' => $mat]
            );
        }

        $matByName = collect($materials)->keyBy('name');
        $catByName = collect($categories)->keyBy('name');

        // ── 8 Real World-Famous Museums ─────────────────────────────────────
        $museumsData = [
            [
                'curator'          => $curator,
                'name'             => 'The British Museum',
                'tagline'          => 'A journey through two million years of human history.',
                'cover_photo_url'  => '/images/seed/british_museum.jpg',
                'logo_path'        => '/images/seed/logos/british_museum.svg',
                'description'      => "Founded in 1753, the British Museum in London is the world's oldest national public museum and one of the largest in existence. It holds a permanent collection of some eight million works, spanning the entirety of human history and culture from prehistoric times to the present day. The collection represents civilizations from every continent, including the Rosetta Stone, the Elgin Marbles, the Lewis Chessmen, and the Sutton Hoo helmet.\n\nThe museum was established by an Act of Parliament in 1753, based on the collections of the physician and scientist Sir Hans Sloane. The museum opened to the public in 1759, at Montagu House in Bloomsbury. Since then, it has been significantly expanded through gifts, purchases, and field expeditions. Today the museum attracts some six million visitors annually.",
                'foundation_year'  => 1753,
                'website'          => 'https://www.britishmuseum.org',
                'address'          => 'Great Russell Street',
                'city'             => 'London',
                'country'          => 'United Kingdom',
                'latitude'         => 51.5194,
                'longitude'        => -0.1270,
                'featured'         => true,
                'verification_status' => Museum::VERIFICATION_VERIFIED,
                'opening_hours'    => ['monday' => 'Closed', 'tuesday' => '10:00–17:00', 'wednesday' => '10:00–17:00', 'thursday' => '10:00–20:30', 'friday' => '10:00–20:30', 'saturday' => '10:00–17:00', 'sunday' => '10:00–17:00'],
                'social_links'     => ['twitter' => 'https://twitter.com/britishmuseum', 'instagram' => 'https://instagram.com/britishmuseum', 'facebook' => 'https://facebook.com/britishmuseum'],
                'contacts'         => [
                    ['label' => 'General Enquiries', 'email' => 'info@britishmuseum.org', 'phone' => '+44 20 7323 8299'],
                    ['label' => 'Press Office',      'email' => 'press@britishmuseum.org', 'phone' => '+44 20 7323 8583'],
                ],
                'artifacts' => [
                    [
                        'name'                => 'The Rosetta Stone',
                        'short_description'   => 'A granodiorite stele inscribed with a decree in three scripts that unlocked the secrets of Egyptian hieroglyphics.',
                        'description'         => "The Rosetta Stone is a stele of granodiorite inscribed with three versions of a decree issued at Memphis, Egypt, in 196 BC during the Ptolemaic dynasty on behalf of King Ptolemy V Epiphanes. The top and middle texts are in Ancient Egyptian using hieroglyphic and Demotic scripts respectively, while the bottom is in Ancient Greek. The stone was discovered in July 1799 by French officer Pierre-François Bouchard during the Napoleonic expedition to Egypt. It became the key to deciphering Egyptian hieroglyphics — unlocking two thousand years of a lost writing system.\n\nSince its arrival at the British Museum in 1802, it has become the most-visited object in the museum and one of the most famous artifacts in the world. The stone measures 112.3 cm × 75.7 cm × 28.4 cm.",
                        'civilization'        => 'Ancient Egyptian',
                        'era'                 => 'Ptolemaic Period',
                        'century'             => '2nd century BC',
                        'country_of_origin'   => 'Egypt',
                        'region'              => 'Nile Delta',
                        'discovery_location'  => 'Rashid (Rosetta), Egypt',
                        'dimensions'          => '112.3 cm × 75.7 cm × 28.4 cm',
                        'weight'              => '760 kg',
                        'condition'           => 'Good',
                        'estimated_value'     => null,
                        'status'              => 'public',
                        'category'            => 'Architecture',
                        'material'            => 'Limestone',
                        'tags'                => ['hieroglyphics', 'egypt', 'decree', 'ptolemy', 'scripts'],
                    ],
                    [
                        'name'                => 'Sutton Hoo Helmet',
                        'short_description'   => 'A spectacular iron helmet decorated with bronze and silver, buried with an Anglo-Saxon king around AD 625.',
                        'description'         => "The Sutton Hoo helmet is a decorated iron helmet found among the treasures discovered in 1939 at the site of an Anglo-Saxon ship burial at Sutton Hoo in Suffolk, England. The helmet dates from the early 7th century and is closely associated with rulers of the East Angles. It is one of only four complete Anglo-Saxon helmets known to exist and is perhaps the most iconic object in the Sutton Hoo treasure.\n\nThe helmet is decorated with embossed bronze panels depicting a variety of scenes, including a warrior on horseback, two warriors in a hall, and interlaced animal designs. The full-face mask covering would have made the wearer appear almost supernatural in battle, its fearsome expression combining military function with ceremonial grandeur.",
                        'civilization'        => 'Anglo-Saxon',
                        'era'                 => 'Early Medieval',
                        'century'             => '7th century AD',
                        'country_of_origin'   => 'England',
                        'region'              => 'East Anglia',
                        'discovery_location'  => 'Sutton Hoo, Suffolk, England',
                        'dimensions'          => '31.8 cm height',
                        'weight'              => '2.5 kg',
                        'condition'           => 'Restored',
                        'estimated_value'     => null,
                        'status'              => 'public',
                        'category'            => 'Weapons & Armor',
                        'material'            => 'Iron',
                        'tags'                => ['helmet', 'burial', 'anglo-saxon', 'warrior', 'suffolk'],
                    ],
                ],
            ],
            [
                'curator'          => $curator,
                'name'             => 'The Metropolitan Museum of Art',
                'tagline'          => 'Art of the world, for the world.',
                'cover_photo_url'  => '/images/seed/met_museum.jpg',
                'logo_path'        => '/images/seed/logos/met_museum.svg',
                'description'      => "The Metropolitan Museum of Art, colloquially known as 'The Met', is the largest art museum in the United States and one of the most visited art museums in the world. Located on the eastern edge of Central Park in Manhattan, New York City, the Met holds a permanent collection of about 1.5 million works of art spanning 5,000 years of world culture.\n\nThe museum was founded in 1870 by businessmen, financiers, and leaders of the arts, with the stated goal of bringing art and art education to the American people. The collection spans the entire breadth of human culture and artistic creativity: Egyptian antiquities, Greek and Roman art, European paintings, arms and armor, Islamic art, African art, pre-Columbian art, musical instruments, and much more. Its collection of European paintings includes works by virtually every major master.",
                'foundation_year'  => 1870,
                'website'          => 'https://www.metmuseum.org',
                'address'          => '1000 Fifth Avenue',
                'city'             => 'New York',
                'country'          => 'United States',
                'latitude'         => 40.7794,
                'longitude'        => -73.9632,
                'featured'         => true,
                'verification_status' => Museum::VERIFICATION_VERIFIED,
                'opening_hours'    => ['monday' => 'Closed', 'tuesday' => '10:00–17:00', 'wednesday' => 'Closed', 'thursday' => '10:00–17:00', 'friday' => '10:00–21:00', 'saturday' => '10:00–21:00', 'sunday' => '10:00–17:00'],
                'social_links'     => ['twitter' => 'https://twitter.com/metmuseum', 'instagram' => 'https://instagram.com/metmuseum', 'facebook' => 'https://facebook.com/metmuseum'],
                'contacts'         => [
                    ['label' => 'Visitor Information', 'email' => 'communications@metmuseum.org', 'phone' => '+1 212 535 7710'],
                ],
                'artifacts' => [
                    [
                        'name'                => 'Temple of Dendur',
                        'short_description'   => 'An ancient Roman Egyptian sandstone temple dedicated to Isis, gifted to the USA by Egypt and housed in a glass wing.',
                        'description'         => "The Temple of Dendur is an ancient Roman Egyptian temple built by the Roman governor of Egypt, Petronius, around 15 BC, dedicated to Isis and Osiris, as well as two deified sons of a local Nubian ruler. The temple was commissioned by Emperor Augustus of Rome.\n\nIn 1965, the temple was dismantled and moved from its original location to save it from being submerged by the rising waters of Lake Nasser following the construction of the Aswan High Dam. Egypt gifted the temple to the United States in recognition of American assistance in saving Nubian monuments. It was awarded to the Metropolitan Museum in 1967 and installed in the Sackler Wing in 1978 where it stands beside a reflecting pool in a massive glass pavilion.",
                        'civilization'        => 'Roman Egypt',
                        'era'                 => 'Roman Period',
                        'century'             => '1st century BC',
                        'country_of_origin'   => 'Egypt',
                        'region'              => 'Lower Nubia',
                        'discovery_location'  => 'Dendur, Nubia, Egypt',
                        'dimensions'          => '25 m length × 8 m height',
                        'weight'              => '800 tons',
                        'condition'           => 'Restored',
                        'estimated_value'     => null,
                        'status'              => 'public',
                        'category'            => 'Architecture',
                        'material'            => 'Limestone',
                        'tags'                => ['egypt', 'temple', 'nubia', 'isis', 'roman-period'],
                    ],
                    [
                        'name'                => 'Washington Crossing the Delaware',
                        'short_description'   => 'Emanuel Leutze\'s iconic 1851 painting depicting George Washington\'s daring crossing of the Delaware River on the night of December 25–26, 1776.',
                        'description'         => "Washington Crossing the Delaware is a large-scale oil painting completed in 1851 by the German-American artist Emanuel Gottlieb Leutze. It commemorates General George Washington's surprise crossing of the Delaware River on the night of December 25–26, 1776 during the American Revolutionary War, prior to the Battle of Trenton.\n\nThe painting measures 3.78 m × 6.47 m (12 ft 5 in × 21 ft 3 in) and is one of the most famous images in American culture. It was purchased by the Metropolitan Museum in 1897 and has remained on continuous display since. Leutze painted three versions of the composition; this is the second and by far the best known.",
                        'civilization'        => 'American',
                        'era'                 => '19th Century',
                        'century'             => '19th century',
                        'country_of_origin'   => 'United States',
                        'region'              => 'North America',
                        'dimensions'          => '378 cm × 647 cm',
                        'condition'           => 'Good',
                        'estimated_value'     => 65000000,
                        'status'              => 'public',
                        'category'            => 'Painting',
                        'material'            => 'Wood',
                        'tags'                => ['american-revolution', 'oil-painting', 'george-washington', 'leutze', '19th-century'],
                    ],
                ],
            ],
            [
                'curator'          => $curator2,
                'name'             => 'The Louvre',
                'tagline'          => 'The world\'s most visited art museum, home to the Mona Lisa.',
                'cover_photo_url'  => '/images/seed/louvre_museum.jpg',
                'logo_path'        => '/images/seed/logos/louvre_museum.svg',
                'description'      => "The Louvre, or the Louvre Museum, is the world's most-visited art museum, with 7.7 million visitors in 2023. It is a historic monument and the central landmark of Paris. The museum occupies the Palais du Louvre, a former royal palace built by King Philip II in the late 12th century.\n\nThe museum's collection is divided into eight curatorial departments: Egyptian Antiquities; Near Eastern Antiquities; Greek, Etruscan, and Roman Antiquities; Islamic Art; Sculpture; Decorative Arts; Paintings; and Prints and Drawings. The permanent collection contains around 35,000 works, of which 31,000 are currently on display. Among its most celebrated exhibits are Leonardo da Vinci's Mona Lisa, the Venus de Milo, and the Winged Victory of Samothrace.",
                'foundation_year'  => 1793,
                'website'          => 'https://www.louvre.fr/en',
                'address'          => 'Rue de Rivoli',
                'city'             => 'Paris',
                'country'          => 'France',
                'latitude'         => 48.8606,
                'longitude'        => 2.3376,
                'featured'         => true,
                'verification_status' => Museum::VERIFICATION_VERIFIED,
                'opening_hours'    => ['monday' => 'Closed', 'tuesday' => '09:00–18:00', 'wednesday' => '09:00–21:45', 'thursday' => '09:00–18:00', 'friday' => '09:00–21:45', 'saturday' => '09:00–18:00', 'sunday' => '09:00–18:00'],
                'social_links'     => ['twitter' => 'https://twitter.com/MuseeLouvre', 'instagram' => 'https://instagram.com/museelouvre', 'facebook' => 'https://facebook.com/museedulouvre'],
                'contacts'         => [
                    ['label' => 'General Information', 'email' => 'info@louvre.fr', 'phone' => '+33 1 40 20 50 50'],
                ],
                'artifacts' => [
                    [
                        'name'                => 'Venus de Milo',
                        'short_description'   => 'An ancient Greek marble sculpture representing the goddess Aphrodite, created between 130 and 100 BC.',
                        'description'         => "The Venus de Milo is an ancient Greek marble sculpture that was created sometime between 130 and 100 BC. It is believed to depict Aphrodite, the Greek goddess of love and beauty. It is a marble sculpture, slightly larger than life size at 2.03 m (6 ft 8 in) high, and weighs 900 kg.\n\nThe statue was discovered by farmer Yorgos Kentrotas in 1820 on the island of Milos (also known as Melos) in the Aegean Sea, after which it is named. The sculpture was purchased by the French ambassador and presented to King Louis XVIII, who donated it to the Louvre. Although the original context of the statue is uncertain, it is considered one of the greatest achievements of ancient Greek sculpture.",
                        'civilization'        => 'Ancient Greek',
                        'era'                 => 'Hellenistic',
                        'century'             => '2nd century BC',
                        'country_of_origin'   => 'Greece',
                        'region'              => 'Aegean Islands',
                        'discovery_location'  => 'Milos, Cyclades, Greece',
                        'dimensions'          => '2.03 m height',
                        'weight'              => '900 kg',
                        'condition'           => 'Good (arms missing)',
                        'estimated_value'     => null,
                        'status'              => 'public',
                        'category'            => 'Sculpture',
                        'material'            => 'Marble',
                        'tags'                => ['aphrodite', 'greek', 'goddess', 'hellenistic', 'marble'],
                    ],
                    [
                        'name'                => 'Code of Hammurabi',
                        'short_description'   => 'One of the oldest and most complete written legal codes, carved on a basalt stele around 1754 BC.',
                        'description'         => "The Code of Hammurabi is a well-preserved Babylonian code of law of ancient Mesopotamia, dated to about 1754 BC. It is one of the oldest deciphered writings of significant length in the world. The sixth Babylonian king, Hammurabi, enacted the code. A stone stele with the code inscribed on it was discovered in 1901 by French archaeologist Gustave Jéquier at the ancient city of Susa.\n\nThe stele stands 2.25 metres tall and comprises 282 laws, with scaled punishments reflecting the principle of lex talionis (an eye for an eye). The laws concern commercial interactions and set prices, tariffs, trade regulations, and labor regulations. The text is inscribed in Akkadian cuneiform script and is nearly complete — missing only the first five columns of text which were defaced in antiquity.",
                        'civilization'        => 'Babylonian',
                        'era'                 => 'Old Babylonian Period',
                        'century'             => '18th century BC',
                        'country_of_origin'   => 'Iraq',
                        'region'              => 'Susa, Elam (Iran)',
                        'discovery_location'  => 'Susa, Iran',
                        'dimensions'          => '2.25 m height',
                        'condition'           => 'Good',
                        'estimated_value'     => null,
                        'status'              => 'public',
                        'category'            => 'Sculpture',
                        'material'            => 'Stone',
                        'tags'                => ['law', 'babylonian', 'hammurabi', 'cuneiform', 'mesopotamia'],
                    ],
                ],
            ],
            [
                'curator'          => $curator2,
                'name'             => 'Egyptian Museum, Cairo',
                'tagline'          => 'The greatest collection of ancient Egyptian antiquities on Earth.',
                'cover_photo_url'  => '/images/seed/egyptian_museum.jpg',
                'logo_path'        => '/images/seed/logos/egyptian_museum.svg',
                'description'      => "The Egyptian Museum in Tahrir Square, Cairo, officially known as the Museum of Egyptian Antiquities, is home to the world's most extensive collection of ancient Egyptian artifacts. With over 120,000 items, the museum houses an extensive collection of ancient Egyptian antiquities. The museum was established in 1902 and holds 5,000 years of Egyptian history within its walls.\n\nThe collection spans from prehistoric times through the Greco-Roman period and includes remarkable collections from the Valley of the Kings, including the treasures of Tutankhamun — 5,398 objects in total discovered intact in the tomb in 1922. The museum also holds the Royal Mummy Room, which houses 27 royal mummies including Ramesses II, Seti I, and Thutmose III.",
                'foundation_year'  => 1902,
                'website'          => 'https://www.egyptianmuseum.gov.eg',
                'address'          => 'Tahrir Square',
                'city'             => 'Cairo',
                'country'          => 'Egypt',
                'latitude'         => 30.0478,
                'longitude'        => 31.2336,
                'featured'         => true,
                'verification_status' => Museum::VERIFICATION_VERIFIED,
                'opening_hours'    => ['monday' => '09:00–17:00', 'tuesday' => '09:00–17:00', 'wednesday' => '09:00–17:00', 'thursday' => '09:00–17:00', 'friday' => '09:00–17:00', 'saturday' => '09:00–17:00', 'sunday' => '09:00–17:00'],
                'social_links'     => ['facebook' => 'https://facebook.com/EgyptianMuseum'],
                'contacts'         => [
                    ['label' => 'Main Office', 'email' => 'info@egyptianmuseum.gov.eg', 'phone' => '+20 2 2579 6948'],
                ],
                'artifacts' => [
                    [
                        'name'                => 'Golden Death Mask of Tutankhamun',
                        'short_description'   => 'The magnificent solid gold funerary mask of the boy pharaoh Tutankhamun, weighing 10.23 kg of solid gold.',
                        'description'         => "The death mask of Tutankhamun is a gold mask of the mummy of the 18th-dynasty pharaoh Tutankhamun who reigned 1332–1323 BC. It was discovered in the innermost chamber of his tomb in the Valley of the Kings in 1925, inside the third (innermost) coffin by Howard Carter and his team.\n\nThe mask is 54 cm (21 in) high, 39.3 cm (15.5 in) wide, and 49 cm (19.3 in) deep, weighing 10.23 kg (22.6 lb) of solid gold. The face is that of the young king, adorned with lapis lazuli, carnelian, quartz, obsidian, turquoise, and coloured glass. The back of the mask is inscribed with a protective spell from the Book of the Dead. It is widely considered one of the greatest masterpieces of ancient Egyptian art.",
                        'civilization'        => 'Ancient Egyptian',
                        'era'                 => 'New Kingdom',
                        'century'             => '14th century BC',
                        'country_of_origin'   => 'Egypt',
                        'region'              => 'Upper Egypt',
                        'discovery_location'  => 'Valley of the Kings, Luxor, Egypt',
                        'dimensions'          => '54 cm × 39.3 cm × 49 cm',
                        'weight'              => '10.23 kg',
                        'condition'           => 'Excellent',
                        'estimated_value'     => null,
                        'status'              => 'public',
                        'category'            => 'Jewelry',
                        'material'            => 'Gold',
                        'tags'                => ['tutankhamun', 'pharaoh', 'gold', 'funerary', 'new-kingdom'],
                    ],
                    [
                        'name'                => 'Narmer Palette',
                        'short_description'   => 'A 63 cm tall decorated ceremonial palette that provides one of the earliest historical records of Egyptian pharaohs.',
                        'description'         => "The Narmer Palette, also known as the Great Hierakonpolis Palette or the Palette of Narmer, is a significant Egyptian archaeological find dating from about the 31st century BC. It contains some of the earliest hieroglyphic inscriptions ever found, and is thought by some to depict the unification of Upper and Lower Egypt under the king Narmer.\n\nOn one side, Narmer is depicted wearing the white crown of Upper Egypt, raising a mace above a captive's head. On the other side, he wears the red crown of Lower Egypt while inspecting decapitated enemies. The central circular depression (the 'palette') is formed by two long-necked serpopards — mythical animals — whose entwined necks circle around the grinding area. It was found at Hierakonpolis by James Quibell and Frederick Green in 1897–1898.",
                        'civilization'        => 'Ancient Egyptian',
                        'era'                 => 'Early Dynastic Period',
                        'century'             => '31st century BC',
                        'country_of_origin'   => 'Egypt',
                        'region'              => 'Upper Egypt',
                        'discovery_location'  => 'Hierakonpolis, Egypt',
                        'dimensions'          => '63 cm × 42 cm',
                        'condition'           => 'Very Good',
                        'estimated_value'     => null,
                        'status'              => 'public',
                        'category'            => 'Sculpture',
                        'material'            => 'Limestone',
                        'tags'                => ['narmer', 'palette', 'unification', 'early-dynastic', 'hieroglyphs'],
                    ],
                ],
            ],
            [
                'curator'          => $curator3,
                'name'             => 'The Vatican Museums',
                'tagline'          => 'Two thousand years of art and history in the heart of the Eternal City.',
                'cover_photo_url'  => '/images/seed/vatican_museums.jpg',
                'logo_path'        => '/images/seed/logos/vatican_museums.svg',
                'description'      => "The Vatican Museums are the public art and sculpture museums in Vatican City, and display works collected by the Roman Catholic Church and the papacy over the centuries. They include some of the most renowned works of Renaissance art in the world. The museums contain roughly 70,000 works, of which 20,000 are on display.\n\nFounded by Pope Julius II in the early 16th century, the museums encompass the Sistine Chapel (with Michelangelo's famous ceiling and The Last Judgment), the Raphael Rooms, the Gallery of Maps, the Egyptian Museum, and the Pinacoteca Vaticana. Each year, approximately six million visitors walk through these hallowed halls, making it one of the most visited attractions in the world.",
                'foundation_year'  => 1506,
                'website'          => 'https://www.museivaticani.va/content/museivaticani/en.html',
                'address'          => 'Viale Vaticano',
                'city'             => 'Vatican City',
                'country'          => 'Vatican City',
                'latitude'         => 41.9064,
                'longitude'        => 12.4534,
                'featured'         => true,
                'verification_status' => Museum::VERIFICATION_VERIFIED,
                'opening_hours'    => ['monday' => '09:00–18:00', 'tuesday' => '09:00–18:00', 'wednesday' => '09:00–18:00', 'thursday' => '09:00–18:00', 'friday' => '09:00–18:00', 'saturday' => '09:00–18:00', 'sunday' => 'Closed (free last Sunday of month)'],
                'social_links'     => ['twitter' => 'https://twitter.com/museivaticani', 'instagram' => 'https://instagram.com/museivaticani'],
                'contacts'         => [
                    ['label' => 'Visitor Services', 'email' => 'info@scv.va', 'phone' => '+39 06 6988 4947'],
                ],
                'artifacts' => [
                    [
                        'name'                => 'Laocoön and His Sons',
                        'short_description'   => 'A marble sculptural group depicting the Trojan priest Laocoön and his sons being attacked by sea serpents.',
                        'description'         => "The Laocoön and His Sons, also called the Laocoön Group, is a monumental ancient marble sculpture from the Hellenistic period. The sculpture was found on January 14, 1506, in a vineyard near the ruins of the Domus Aurea of Emperor Nero in Rome, Italy. Pope Julius II, immediately upon hearing news of its unearthing, sent Giuliano da Sangallo, Michelangelo, and the artist-architect Bramante to inspect and acquire it for the Vatican Museums.\n\nThe sculpture depicts the Trojan priest Laocoön, who warned the Trojans not to accept the wooden horse left by the Greeks, and his sons Antiphantes and Thymbraeus being strangled by sea serpents sent by the gods. The group stands 2.42 m high and is now displayed in the Octagonal Courtyard. It has been called the work that most influenced ancient art since its rediscovery.",
                        'civilization'        => 'Ancient Greek',
                        'era'                 => 'Hellenistic',
                        'century'             => '2nd–1st century BC',
                        'country_of_origin'   => 'Greece',
                        'region'              => 'Mediterranean',
                        'discovery_location'  => 'Esquiline Hill, Rome, Italy',
                        'dimensions'          => '2.42 m height',
                        'condition'           => 'Good (partially restored)',
                        'estimated_value'     => null,
                        'status'              => 'public',
                        'category'            => 'Sculpture',
                        'material'            => 'Marble',
                        'tags'                => ['laocoon', 'trojan', 'hellenistic', 'marble', 'serpents'],
                    ],
                ],
            ],
            [
                'curator'          => $curator3,
                'name'             => 'National Museum of Iraq',
                'tagline'          => 'Guardians of Mesopotamia — the cradle of civilization.',
                'cover_photo_url'  => '/images/seed/iraq_museum.jpg',
                'logo_path'        => '/images/seed/logos/iraq_museum.svg',
                'description'      => "The National Museum of Iraq in Baghdad is one of the most important museums in the Arab world and holds one of the world's greatest collections of antiquities. The museum was first established in 1923 by Gertrude Bell, a British writer, traveler, and intelligence officer who played a central role in the creation of modern Iraq.\n\nIts collection covers the entire span of Mesopotamian history, from the prehistoric Halaf period through the Assyrian, Babylonian, Sumerian, and Islamic eras. Among its greatest treasures are artifacts from Ur, Nineveh, Babylon, and Nimrud. The museum suffered devastating looting during the 2003 invasion of Iraq, with an estimated 15,000 objects stolen. Many have since been recovered, and the museum reopened to the public in 2015 after extensive restoration.",
                'foundation_year'  => 1923,
                'website'          => 'https://www.iraqmuseum.org',
                'address'          => 'Al-Qadisiyya Street, Al-Karkh',
                'city'             => 'Baghdad',
                'country'          => 'Iraq',
                'latitude'         => 33.3355,
                'longitude'        => 44.3900,
                'featured'         => false,
                'verification_status' => Museum::VERIFICATION_VERIFIED,
                'opening_hours'    => ['monday' => '08:00–14:00', 'tuesday' => '08:00–14:00', 'wednesday' => '08:00–14:00', 'thursday' => '08:00–14:00', 'friday' => 'Closed', 'saturday' => '08:00–14:00', 'sunday' => '08:00–14:00'],
                'social_links'     => [],
                'contacts'         => [
                    ['label' => 'Museum Office', 'email' => 'info@iraqmuseum.org', 'phone' => '+964 1 422 1287'],
                ],
                'artifacts' => [
                    [
                        'name'                => 'Bull\'s Head Lyre of Ur',
                        'short_description'   => 'A 4,500-year-old golden lyre with a magnificent bull\'s head decoration, discovered in the Royal Tombs of Ur.',
                        'description'         => "The Great Lyre with the Golden Bull's Head is one of three lyres discovered in the Royal Cemetery at Ur by archaeologist Leonard Woolley during excavations conducted between 1922 and 1934. The lyre dates to approximately 2550–2450 BC and is one of the world's oldest surviving musical instruments.\n\nThe lyre is adorned with a magnificent bull's head crafted from gold and lapis lazuli. The beard of the bull is also made of lapis lazuli, while the body of the instrument is made of wood covered in silver and gold leaf. The front panel depicts mythological scenes including a scorpion-man and various animals performing human activities, possibly reflecting a scene from an ancient Sumerian epic poem. The original was looted from the National Museum of Iraq in 2003 and recovered in 2006.",
                        'civilization'        => 'Sumerian',
                        'era'                 => 'Early Dynastic Period III',
                        'century'             => '26th–25th century BC',
                        'country_of_origin'   => 'Iraq',
                        'region'              => 'Southern Mesopotamia',
                        'discovery_location'  => 'Royal Cemetery of Ur, Tell el-Muqayyar, Iraq',
                        'dimensions'          => '112.5 cm height',
                        'condition'           => 'Reconstructed',
                        'estimated_value'     => null,
                        'status'              => 'public',
                        'category'            => 'Jewelry',
                        'material'            => 'Gold',
                        'tags'                => ['sumerian', 'lyre', 'music', 'ur', 'royal-tomb', 'gold'],
                    ],
                ],
            ],
            [
                'curator'          => $curator3,
                'name'             => 'National Palace Museum',
                'tagline'          => 'The world\'s greatest repository of Chinese imperial art and history.',
                'cover_photo_url'  => '/images/seed/taipei_museum.jpg',
                'logo_path'        => '/images/seed/logos/taipei_museum.svg',
                'description'      => "The National Palace Museum in Taipei, Taiwan, houses one of the world's largest collections of Chinese imperial artifacts and artworks. With approximately 700,000 items in its permanent collection, the museum holds an extraordinary treasury of Chinese history spanning 8,000 years — from the Neolithic period through the Qing dynasty.\n\nThe collection was originally assembled by emperors of China over many centuries, particularly during the Ming and Qing dynasties, and was housed in the Forbidden City in Beijing. During China's civil war, the Nationalist government transported the most valuable objects to Taiwan for safekeeping in 1949. Today the collection includes masterpieces of Chinese bronzes, jade, calligraphy, painting, porcelain, and lacquerware.",
                'foundation_year'  => 1925,
                'website'          => 'https://www.npm.gov.tw/en',
                'address'          => 'No. 221, Sec. 2, Zhishan Rd, Shilin District',
                'city'             => 'Taipei',
                'country'          => 'Taiwan',
                'latitude'         => 25.1024,
                'longitude'        => 121.5484,
                'featured'         => false,
                'verification_status' => Museum::VERIFICATION_VERIFIED,
                'opening_hours'    => ['monday' => 'Closed', 'tuesday' => '09:00–17:00', 'wednesday' => '09:00–17:00', 'thursday' => '09:00–17:00', 'friday' => '09:00–21:00', 'saturday' => '09:00–21:00', 'sunday' => '09:00–17:00'],
                'social_links'     => ['instagram' => 'https://instagram.com/npmuseum', 'facebook' => 'https://facebook.com/npmgov'],
                'contacts'         => [
                    ['label' => 'Visitor Services', 'email' => 'service@npm.gov.tw', 'phone' => '+886 2 2881 2021'],
                ],
                'artifacts' => [
                    [
                        'name'                => 'Jadeite Cabbage',
                        'short_description'   => 'A 19th-century Qing-dynasty carving of a cabbage in pure jadeite, one of the most recognizable artworks in Chinese culture.',
                        'description'         => "The Jadeite Cabbage (翠玉白菜) is a Chinese carved jadeite sculpture measuring approximately 18.7 cm × 9.1 cm × 5.07 cm. It was carved in the Qing dynasty, likely during the reign of the Guangxu Emperor (1875–1908). The sculpture depicts a Chinese cabbage (napa cabbage) and is celebrated for its masterful use of the natural coloration in the jadeite stone — the sculptor used the pale white section for the base of the cabbage and the green section for the upper leaves.\n\nThe piece is unique in that it incorporates two insects — a katydid and a locust — both traditional symbols of fertility and prosperity perched on the leaves. The cabbage itself is a symbol of purity and fertility. It is one of the most recognizable and beloved artifacts in East Asian culture and consistently draws the largest crowds at the National Palace Museum.",
                        'civilization'        => 'Chinese (Qing Dynasty)',
                        'era'                 => 'Qing Dynasty',
                        'century'             => '19th century',
                        'country_of_origin'   => 'China',
                        'region'              => 'East Asia',
                        'dimensions'          => '18.7 cm × 9.1 cm × 5.07 cm',
                        'condition'           => 'Excellent',
                        'estimated_value'     => null,
                        'status'              => 'public',
                        'category'            => 'Sculpture',
                        'material'            => 'Ivory',
                        'tags'                => ['jadeite', 'jade', 'qing', 'chinese', 'sculpture', 'cabbage'],
                    ],
                ],
            ],
            [
                'curator'          => $curator,
                'name'             => 'Pergamon Museum',
                'tagline'          => 'Gateway to the ancient world — where monumental Greek, Roman, and Islamic architecture lives indoors.',
                'cover_photo_url'  => '/images/seed/pergamon_museum.jpg',
                'logo_path'        => '/images/seed/logos/pergamon_museum.svg',
                'description'      => "The Pergamon Museum is situated on the Museum Island in Berlin, Germany, and is among the most visited museums in the country. It was built to house the large architectural structures excavated by German archaeologists in the late 19th and early 20th centuries from Pergamon and other ancient sites in the Middle East and Asia Minor.\n\nThe museum houses three collections: the Collection of Classical Antiquities, the Museum of the Ancient Near East, and the Museum of Islamic Art. Its most famous exhibits are the Pergamon Altar (c. 180–160 BC), the Market Gate of Miletus (c. 120 AD), the Ishtar Gate of Babylon (c. 575 BC), and the Mshatta Facade. The building itself was inaugurated in 1930 and designed by Alfred Messel and Ludwig Hoffmann.",
                'foundation_year'  => 1930,
                'website'          => 'https://www.smb.museum/en/museums-institutions/pergamonmuseum',
                'address'          => 'Am Kupfergraben 5, Museum Island',
                'city'             => 'Berlin',
                'country'          => 'Germany',
                'latitude'         => 52.5213,
                'longitude'        => 13.3967,
                'featured'         => false,
                'verification_status' => Museum::VERIFICATION_PENDING,
                'opening_hours'    => ['monday' => '10:00–18:00', 'tuesday' => '10:00–18:00', 'wednesday' => '10:00–20:00', 'thursday' => '10:00–18:00', 'friday' => '10:00–18:00', 'saturday' => '10:00–18:00', 'sunday' => '10:00–18:00'],
                'social_links'     => ['twitter' => 'https://twitter.com/smbmuseen', 'instagram' => 'https://instagram.com/staatliche_museen_berlin'],
                'contacts'         => [
                    ['label' => 'Visitor Services', 'email' => 'service@smb.spk-berlin.de', 'phone' => '+49 30 266 42 42 42'],
                ],
                'artifacts' => [
                    [
                        'name'                => 'Ishtar Gate',
                        'short_description'   => 'The reconstructed eighth gate of Babylon, built c. 575 BC by Nebuchadnezzar II and decorated with glazed brick dragons and bulls.',
                        'description'         => "The Ishtar Gate was the eighth gate of the inner city of Babylon (in the area of present-day Iraq). It was constructed in about 575 BC by the Neo-Babylonian king Nebuchadnezzar II on the north side of the city. It was part of a grand processional way leading into the city, flanked on both sides by glazed brick walls featuring rows of animals — 120 lions, 575 dragons (mushussu), and 360 aurochs (wild bulls).\n\nThe gate was dedicated to Ishtar, the Babylonian goddess of love and war. When excavated by German archaeologist Robert Koldewey starting in 1902, he found thousands of glazed brick fragments which were shipped to Berlin. The museum's reconstruction stands 14 m (46 ft) high in a specially built hall. The reconstructed gate is only part of the original, which would have been much taller.",
                        'civilization'        => 'Neo-Babylonian',
                        'era'                 => 'Neo-Babylonian Period',
                        'century'             => '6th century BC',
                        'country_of_origin'   => 'Iraq',
                        'region'              => 'Mesopotamia',
                        'discovery_location'  => 'Babylon, Al Hillah, Iraq',
                        'dimensions'          => '14 m height (reconstructed)',
                        'condition'           => 'Reconstructed',
                        'estimated_value'     => null,
                        'status'              => 'public',
                        'category'            => 'Architecture',
                        'material'            => 'Terracotta',
                        'tags'                => ['babylon', 'ishtar', 'gate', 'glazed-brick', 'nebuchadnezzar'],
                    ],
                ],
            ],
        ];

        $artifactImageMap = [
            'The Rosetta Stone'                => '/images/seed/rosetta_stone.jpg',
            'Sutton Hoo Helmet'                => '/images/seed/sutton_hoo_helmet.jpg',
            'Temple of Dendur'                 => '/images/seed/temple_of_dendur.jpg',
            'Washington Crossing the Delaware' => '/images/seed/washington_crossing.jpg',
            'Venus de Milo'                    => '/images/seed/venus_de_milo.jpg',
            'Code of Hammurabi'                => '/images/seed/code_of_hammurabi.jpg',
            'Golden Death Mask of Tutankhamun' => '/images/seed/tut_mask.jpg',
            'Narmer Palette'                   => '/images/seed/narmer_palette.jpg',
            'Laocoön and His Sons'             => '/images/seed/laocoon.jpg',
            'Bull\'s Head Lyre of Ur'          => '/images/seed/lyre_of_ur.jpg',
            'Jadeite Cabbage'                  => '/images/seed/jadeite_cabbage.jpg',
            'Ishtar Gate'                      => '/images/seed/ishtar_gate.jpg',
            'Roman Bronze Fibula'              => '/images/seed/roman_fibula.jpg',
            'Greek Black-Figure Amphora'       => '/images/seed/greek_amphora.jpg',
        ];

        // ── Seed museums and artifacts ───────────────────────────────────────
        foreach ($museumsData as $museumDef) {
            $museum = Museum::updateOrCreate(
                ['name' => $museumDef['name']],
                [
                    'curator_id'          => $museumDef['curator']->id,
                    'tagline'             => $museumDef['tagline'],
                    'description'         => $museumDef['description'],
                    'foundation_year'     => $museumDef['foundation_year'],
                    'website'             => $museumDef['website'],
                    'address'             => $museumDef['address'],
                    'city'                => $museumDef['city'],
                    'country'             => $museumDef['country'],
                    'latitude'            => $museumDef['latitude'],
                    'longitude'           => $museumDef['longitude'],
                    'featured'            => $museumDef['featured'],
                    'verification_status' => $museumDef['verification_status'],
                    'opening_hours'       => $museumDef['opening_hours'],
                    'social_links'        => $museumDef['social_links'],
                    'cover_photo_url'     => $museumDef['cover_photo_url'] ?? null,
                    'logo_path'           => $museumDef['logo_path'] ?? null,
                    'views_count'         => rand(200, 15000),
                ]
            );

            // Contacts
            foreach ($museumDef['contacts'] as $contact) {
                MuseumContact::firstOrCreate(
                    ['museum_id' => $museum->id, 'email' => $contact['email']],
                    ['label' => $contact['label'], 'phone' => $contact['phone'] ?? null]
                );
            }

            // Artifacts
            foreach ($museumDef['artifacts'] as $artDef) {
                $catModel  = $catByName->get($artDef['category']);
                $matModel  = $matByName->get($artDef['material']);

                $artifact = Artifact::updateOrCreate(
                    ['name' => $artDef['name']],
                    [
                        'created_by'          => $museumDef['curator']->id,
                        'museum_id'           => $museum->id,
                        'category_id'         => $catModel?->id ?? $categories[0]->id,
                        'material_id'         => $matModel?->id,
                        'short_description'   => $artDef['short_description'],
                        'description'         => $artDef['description'],
                        'civilization'        => $artDef['civilization'],
                        'era'                 => $artDef['era'],
                        'century'             => $artDef['century'],
                        'country_of_origin'   => $artDef['country_of_origin'],
                        'region'              => $artDef['region'] ?? null,
                        'discovery_location'  => $artDef['discovery_location'] ?? null,
                        'dimensions'          => $artDef['dimensions'] ?? null,
                        'weight'              => $artDef['weight'] ?? null,
                        'condition'           => $artDef['condition'] ?? null,
                        'estimated_value'     => $artDef['estimated_value'] ?? null,
                        'status'              => $artDef['status'],
                        'verification_status' => 'verified',
                    ]
                );

                // Tags
                if (!empty($artDef['tags'])) {
                    $tagIds = [];
                    foreach ($artDef['tags'] as $tagName) {
                        $tag = ArtifactTag::firstOrCreate(
                            ['slug' => Str::slug($tagName)],
                            ['name' => $tagName]
                        );
                        $tagIds[] = $tag->id;
                    }
                    $artifact->tags()->syncWithoutDetaching($tagIds);
                }

                if (isset($artifactImageMap[$artDef['name']])) {
                    ArtifactImage::updateOrCreate(
                        ['artifact_id' => $artifact->id, 'is_primary' => true],
                        [
                            'image_path' => $artifactImageMap[$artDef['name']],
                            'caption'    => $artDef['name'] . ' - Primary View',
                            'sort_order' => 1
                        ]
                    );
                }
            }
        }

        // ── Collector artifacts (Marcus Webb's personal collection) ─────────
        $collectorArtifacts = [
            [
                'name'              => 'Roman Bronze Fibula',
                'short_description' => 'A 2nd-century Roman bronze bow fibula (brooch) used to fasten garments, found in the Rhine frontier region.',
                'description'       => "A Roman bronze fibula (plural fibulae) is a brooch or pin used for fastening garments. This example is a \"bow fibula\" of Aucissa type, widely produced across the Roman Empire during the 1st–2nd centuries AD. The spring-and-pin mechanism is still functional. It was discovered during agricultural work near the Rhine limes — the ancient Roman frontier — in the 1960s and passed through several private collections before being acquired by Marcus Webb.",
                'civilization'      => 'Roman',
                'era'               => 'Imperial Roman Period',
                'century'           => '2nd century AD',
                'country_of_origin' => 'Germany',
                'region'            => 'Rhine Frontier',
                'dimensions'        => '8.2 cm length',
                'weight'            => '42 g',
                'condition'         => 'Good',
                'estimated_value'   => 2800,
                'status'            => 'public',
                'category'          => 'Jewelry',
                'material'          => 'Bronze',
                'tags'              => ['roman', 'fibula', 'brooch', 'bronze', 'imperial'],
            ],
            [
                'name'              => 'Greek Black-Figure Amphora',
                'short_description' => 'An Attic black-figure amphora depicting the hero Heracles wrestling the Nemean Lion, c. 540–520 BC.',
                'description'       => "A two-handled Attic black-figure amphora depicting one of the canonical scenes from Greek mythology: the hero Heracles (Hercules) wrestling the Nemean Lion as his First Labour. The composition is attributed to the manner of the Affecter, an anonymous Athenian vase-painter active c. 560–510 BC, known for his elongated, expressive figures. The reverse shows a procession of horsemen.\n\nThe amphora stands 38 cm tall and retains about 90% of its original surface. Minor restoration is present at the rim. The vessel was exported to Etruria in antiquity, where it was found in a tomb in the late 19th century and entered the European art market.",
                'civilization'      => 'Ancient Greek',
                'era'               => 'Archaic Period',
                'century'           => '6th century BC',
                'country_of_origin' => 'Greece',
                'region'            => 'Attica',
                'discovery_location'=> 'Etruria, Central Italy',
                'dimensions'        => '38 cm height',
                'condition'         => 'Good (minor restoration)',
                'estimated_value'   => 45000,
                'status'            => 'public',
                'category'          => 'Pottery & Ceramics',
                'material'          => 'Terracotta',
                'tags'              => ['greek', 'attic', 'black-figure', 'amphora', 'heracles', 'nemean-lion'],
            ],
        ];

        foreach ($collectorArtifacts as $artDef) {
            $catModel = $catByName->get($artDef['category']);
            $matModel = $matByName->get($artDef['material']);

            $artifact = Artifact::updateOrCreate(
                ['name' => $artDef['name']],
                [
                    'created_by'        => $collector->id,
                    'collector_id'      => $collector->id,
                    'category_id'       => $catModel?->id ?? $categories[0]->id,
                    'material_id'       => $matModel?->id,
                    'short_description' => $artDef['short_description'],
                    'description'       => $artDef['description'],
                    'civilization'      => $artDef['civilization'],
                    'era'               => $artDef['era'],
                    'century'           => $artDef['century'],
                    'country_of_origin' => $artDef['country_of_origin'],
                    'region'            => $artDef['region'] ?? null,
                    'discovery_location'=> $artDef['discovery_location'] ?? null,
                    'dimensions'        => $artDef['dimensions'] ?? null,
                    'weight'            => $artDef['weight'] ?? null,
                    'condition'         => $artDef['condition'] ?? null,
                    'estimated_value'   => $artDef['estimated_value'] ?? null,
                    'status'            => $artDef['status'],
                    'verification_status' => 'verified',
                ]
            );

            if (!empty($artDef['tags'])) {
                $tagIds = [];
                foreach ($artDef['tags'] as $tagName) {
                    $tag = ArtifactTag::firstOrCreate(
                        ['slug' => Str::slug($tagName)],
                        ['name' => $tagName]
                    );
                    $tagIds[] = $tag->id;
                }
                $artifact->tags()->syncWithoutDetaching($tagIds);
            }

            if (isset($artifactImageMap[$artDef['name']])) {
                ArtifactImage::updateOrCreate(
                    ['artifact_id' => $artifact->id, 'is_primary' => true],
                    [
                        'image_path' => $artifactImageMap[$artDef['name']],
                        'caption'    => $artDef['name'] . ' - Primary View',
                        'sort_order' => 1
                    ]
                );
            }
        }

        // ── Seed Collections ───────────────────────────────────────────────
        $egyptMuseum = Museum::where('name', 'Egyptian Museum, Cairo')->first();
        $britishMuseum = Museum::where('name', 'The British Museum')->first();
        $louvreMuseum = Museum::where('name', 'The Louvre')->first();
        $vaticanMuseum = Museum::where('name', 'The Vatican Museums')->first();

        if ($egyptMuseum) {
            $col1 = Collection::updateOrCreate(
                ['name' => 'Masterpieces of Ancient Egypt'],
                [
                    'slug'              => Str::slug('Masterpieces of Ancient Egypt'),
                    'description'       => 'Explore the royal treasures, monumental stone inscriptions, and sacred funerary gold of pharaonic Egypt across three millennia of dynastic power and divine kingship.',
                    'cover_image_path'  => '/images/seed/col_egypt.jpg',
                    'is_public'         => true,
                    'is_featured'       => true,
                    'museum_id'         => $egyptMuseum->id,
                    'created_by'        => $egyptMuseum->curator_id,
                ]
            );
            $egyptArtifactIds = Artifact::whereIn('name', ['The Rosetta Stone', 'Golden Death Mask of Tutankhamun', 'Narmer Palette', 'Temple of Dendur'])->pluck('id');
            $col1->artifacts()->syncWithoutDetaching($egyptArtifactIds);
        }

        if ($louvreMuseum) {
            $col2 = Collection::updateOrCreate(
                ['name' => 'Greco-Roman Classical Sculpture'],
                [
                    'slug'              => Str::slug('Greco-Roman Classical Sculpture'),
                    'description'       => 'A curated journey through the marble perfection and dramatic emotional intensity of ancient Greek and Roman sculpture, representing gods, emperors, and mythical tragedies.',
                    'cover_image_path'  => '/images/seed/col_classical.jpg',
                    'is_public'         => true,
                    'is_featured'       => true,
                    'museum_id'         => $louvreMuseum->id,
                    'created_by'        => $louvreMuseum->curator_id,
                ]
            );
            $classArtifactIds = Artifact::whereIn('name', ['Venus de Milo', 'Laocoön and His Sons', 'Statue of Augustus of Prima Porta'])->pluck('id');
            $col2->artifacts()->syncWithoutDetaching($classArtifactIds);
        }

        if ($britishMuseum) {
            $col3 = Collection::updateOrCreate(
                ['name' => 'Mesopotamian Treasures & Royal Monuments'],
                [
                    'slug'              => Str::slug('Mesopotamian Treasures & Royal Monuments'),
                    'description'       => 'Witness the dawn of civilization in the fertile crescent through monumental legal codes, cuneiform decrees, and golden lyres of Sumer and Babylon.',
                    'cover_image_path'  => '/images/seed/col_mesopotamia.jpg',
                    'is_public'         => true,
                    'is_featured'       => true,
                    'museum_id'         => $britishMuseum->id,
                    'created_by'        => $britishMuseum->curator_id,
                ]
            );
            $mesoArtifactIds = Artifact::whereIn('name', ['Code of Hammurabi', 'Bull\'s Head Lyre of Ur', 'Ishtar Gate', 'The Cyrus Cylinder'])->pluck('id');
            $col3->artifacts()->syncWithoutDetaching($mesoArtifactIds);
        }

        // ── Seed Exhibitions ───────────────────────────────────────────────
        if ($egyptMuseum) {
            $exh1 = Exhibition::updateOrCreate(
                ['name' => 'Egypt\'s Sun Kings: Tutankhamun & Ramses II'],
                [
                    'slug'             => Str::slug('Egypt\'s Sun Kings: Tutankhamun & Ramses II'),
                    'tagline'          => 'The golden zenith of the New Kingdom and the divine pharaohs who shaped ancient history.',
                    'description'      => 'A once-in-a-generation gathering of royal gold, monumental statues, and sacred relics celebrating the zenith of the Egyptian New Kingdom and the divine pharaohs who shaped the ancient world.',
                    'museum_id'        => $egyptMuseum->id,
                    'created_by'       => $egyptMuseum->curator_id,
                    'cover_image_path' => '/images/seed/exh_sun_kings.jpg',
                    'starts_at'        => now()->subDays(10),
                    'ends_at'          => now()->addDays(90),
                    'status'           => 'published',
                    'is_featured'      => true,
                    'location'         => 'Tahrir Square Main Gallery, Wing B — Egyptian Museum, Cairo',
                    'admission_fee'    => 25.00,
                ]
            );

            $sec1 = ExhibitionSection::updateOrCreate(
                ['exhibition_id' => $exh1->id, 'title' => 'Royal Gold & Funerary Regalia'],
                [
                    'body'       => 'An intimate exploration of the royal burial practices of the 18th Dynasty, showcasing intact golden relics and ceremonial objects meant to protect the pharaoh in the afterlife.',
                    'sort_order' => 1,
                ]
            );
            $tut = Artifact::where('name', 'Golden Death Mask of Tutankhamun')->first();
            $dendur = Artifact::where('name', 'Temple of Dendur')->first();
            if ($tut) $sec1->artifacts()->syncWithoutDetaching([$tut->id => ['sort_order' => 1]]);
            if ($dendur) $sec1->artifacts()->syncWithoutDetaching([$dendur->id => ['sort_order' => 2]]);

            $sec2 = ExhibitionSection::updateOrCreate(
                ['exhibition_id' => $exh1->id, 'title' => 'Origins of Dynastic Kingship'],
                [
                    'body'       => 'Witness the unification of Upper and Lower Egypt through monumental stone palettes and historic inscriptions that laid the foundation for three millennia of pharaonic rule.',
                    'sort_order' => 2,
                ]
            );
            $narmer = Artifact::where('name', 'Narmer Palette')->first();
            $rosetta = Artifact::where('name', 'The Rosetta Stone')->first();
            if ($narmer) $sec2->artifacts()->syncWithoutDetaching([$narmer->id => ['sort_order' => 1]]);
            if ($rosetta) $sec2->artifacts()->syncWithoutDetaching([$rosetta->id => ['sort_order' => 2]]);
        }

        if ($louvreMuseum) {
            $exh2 = Exhibition::updateOrCreate(
                ['name' => 'Gods and Heroes: Sculpture of Antiquity'],
                [
                    'slug'             => Str::slug('Gods and Heroes: Sculpture of Antiquity'),
                    'tagline'          => 'Marble perfection and dramatic emotional intensity from ancient Greece to Imperial Rome.',
                    'description'      => 'Immerse yourself in the Hellenistic masterpieces of ancient Greece and Rome. This exhibition brings together iconic marbles portraying Aphrodite, Heracles, and the Trojan priest Laocoön.',
                    'museum_id'        => $louvreMuseum->id,
                    'created_by'       => $louvreMuseum->curator_id,
                    'cover_image_path' => '/images/seed/exh_athena.jpg',
                    'starts_at'        => now()->subDays(5),
                    'ends_at'          => now()->addDays(120),
                    'status'           => 'published',
                    'is_featured'      => true,
                    'location'         => 'Sully Wing, Salle des Caryatides — Musée du Louvre, Paris',
                    'admission_fee'    => 22.00,
                ]
            );

            $sec1 = ExhibitionSection::updateOrCreate(
                ['exhibition_id' => $exh2->id, 'title' => 'The Ideal Form: Divine Beauty'],
                [
                    'body'       => 'Classical Greek sculptors pursued perfection through mathematical proportions and anatomical mastery, elevating gods and mortals alike.',
                    'sort_order' => 1,
                ]
            );
            $venus = Artifact::where('name', 'Venus de Milo')->first();
            if ($venus) $sec1->artifacts()->syncWithoutDetaching([$venus->id => ['sort_order' => 1]]);

            $sec2 = ExhibitionSection::updateOrCreate(
                ['exhibition_id' => $exh2->id, 'title' => 'Myth, Tragedy, and Imperial Power'],
                [
                    'body'       => 'In the Hellenistic and Roman eras, sculpture became a powerful vehicle for dramatic emotion, theatrical suffering, and imperial propaganda.',
                    'sort_order' => 2,
                ]
            );
            $laocoon = Artifact::where('name', 'Laocoön and His Sons')->first();
            $fibula = Artifact::where('name', 'Roman Bronze Fibula')->first();
            $amphora = Artifact::where('name', 'Greek Black-Figure Amphora')->first();
            if ($laocoon) $sec2->artifacts()->syncWithoutDetaching([$laocoon->id => ['sort_order' => 1]]);
            if ($fibula) $sec2->artifacts()->syncWithoutDetaching([$fibula->id => ['sort_order' => 2]]);
            if ($amphora) $sec2->artifacts()->syncWithoutDetaching([$amphora->id => ['sort_order' => 3]]);
        }

        if ($britishMuseum) {
            $exh3 = Exhibition::updateOrCreate(
                ['name' => 'The Birth of Writing: From Cuneiform to Hieroglyphs'],
                [
                    'slug'             => Str::slug('The Birth of Writing: From Cuneiform to Hieroglyphs'),
                    'tagline'          => 'How humanity first recorded laws, epic poetry, and imperial history across Mesopotamia and Egypt.',
                    'description'      => 'Discover how humanity first recorded laws, poetry, and history. Featuring the Rosetta Stone, the Code of Hammurabi, and early administrative clay tablets.',
                    'museum_id'        => $britishMuseum->id,
                    'created_by'       => $britishMuseum->curator_id,
                    'cover_image_path' => '/images/seed/exh_silk_road.jpg',
                    'starts_at'        => now()->addDays(15),
                    'ends_at'          => now()->addDays(180),
                    'status'           => 'published',
                    'is_featured'      => false,
                    'location'         => 'Room 55: Mesopotamia & Room 4: Egyptian Sculpture — The British Museum, London',
                    'admission_fee'    => 0.00,
                ]
            );

            $sec1 = ExhibitionSection::updateOrCreate(
                ['exhibition_id' => $exh3->id, 'title' => 'Mesopotamian Decrees & Sacred Music'],
                [
                    'body'       => 'Explore the clay tablets and diorite stelae that established the earliest written legal codes, along with instruments that accompanied royal temple rituals.',
                    'sort_order' => 1,
                ]
            );
            $hammurabi = Artifact::where('name', 'Code of Hammurabi')->first();
            $lyre = Artifact::where('name', 'Bull\'s Head Lyre of Ur')->first();
            $ishtar = Artifact::where('name', 'Ishtar Gate')->first();
            if ($hammurabi) $sec1->artifacts()->syncWithoutDetaching([$hammurabi->id => ['sort_order' => 1]]);
            if ($lyre) $sec1->artifacts()->syncWithoutDetaching([$lyre->id => ['sort_order' => 2]]);
            if ($ishtar) $sec1->artifacts()->syncWithoutDetaching([$ishtar->id => ['sort_order' => 3]]);

            $sec2 = ExhibitionSection::updateOrCreate(
                ['exhibition_id' => $exh3->id, 'title' => 'Deciphering the Sacred Script'],
                [
                    'body'       => 'For centuries, Egyptian hieroglyphs remained a silent mystery until the discovery of multi-script monuments provided the key to unlocking the ancient world.',
                    'sort_order' => 2,
                ]
            );
            $rosetta = Artifact::where('name', 'The Rosetta Stone')->first();
            if ($rosetta) $sec2->artifacts()->syncWithoutDetaching([$rosetta->id => ['sort_order' => 1]]);
        }

        // ── Seed Auctions ──────────────────────────────────────────────────
        $fibula = Artifact::where('name', 'Roman Bronze Fibula')->first();
        if ($fibula) {
            Auction::updateOrCreate(
                ['title' => 'Classical & Antiquities Autumn Sale — Roman Bronze Fibula'],
                [
                    'artifact_id'   => $fibula->id,
                    'created_by'    => $fibula->created_by,
                    'description'   => 'An exceptional 2nd-century AD Roman bronze bow fibula with intact spring-and-pin mechanism, discovered along the ancient Rhine frontier.',
                    'starts_at'     => now()->subDays(2),
                    'ends_at'       => now()->addDays(12),
                    'reserve_price' => 2500,
                    'current_price' => 3200,
                    'status'        => 'active',
                ]
            );
        }

        $amphora = Artifact::where('name', 'Greek Black-Figure Amphora')->first();
        if ($amphora) {
            Auction::updateOrCreate(
                ['title' => 'Important Archaic Greek Antiquities — Attic Amphora'],
                [
                    'artifact_id'   => $amphora->id,
                    'created_by'    => $amphora->created_by,
                    'description'   => 'A stunning 6th-century BC Attic black-figure amphora depicting Heracles wrestling the Nemean Lion. Exported to Etruria in antiquity.',
                    'starts_at'     => now()->subDays(1),
                    'ends_at'       => now()->addDays(20),
                    'reserve_price' => 40000,
                    'current_price' => 48500,
                    'status'        => 'active',
                ]
            );
        }
    }
}
