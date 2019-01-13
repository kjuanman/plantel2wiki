<?PHP

# Data

$markup_colors = array (
	'good' =>  '#66CC66' ,
	'bad' => '#CC6666' ,
	'maybe' => '#FFFF66' ,
	'oncommons' => '#6666CC' ,
	'unknown' => '#CCCCCC',
) ;

$license_indicators = array (
	'gfdl' => 'GFDL',
	'gnufdl' => 'GFDL',
	'gnufreedocumentationlicense' => 'GFDL',
	'gnufreiedokumentationslizenz' => 'GFDL',
	'gnulizenz' => 'GFDL',
	'publicdomain' => 'PD',
	'gemeinfrei' => 'PD',
	'ccbysa' => 'CC-by-sa',
) ;

$language_image_licenses = array (
	'de:pd' => 'Bild-PD',
	'de:gfdl' => 'Bild-GFDL',
	'de:CC-by-sa' => 'Bild-CC-by-sa/2.0',
) ;

$forbidden_commonsense_categories = array (
	'cities in germany',
	'villages in germany',
) ;

$image_aliases = array ( 
	'image:' , 
	'bild:' , 
	'grafika:' , 
	'bilde:' , 
	'kuva:' , 
	'mynd:' ,
	'billede:' ,
	'dosiero:' ,
	'imatge:' ,
	'imago:' ,
	'afbeelding:' ,
	'gambar:' ,
	'pilt:' ,
	'imagine:' ,
	'slika:' ,
	'delwedd:' ,
	'immagine:' ,
	'imagen:' ,
	'soubor:' ,
	'resim:' ,
) ;

$ignore_templates = array (
	'wikimediacopyright',
	'wappen-ch',
	'wappen-at',
	'dinero',
) ;

$good_templates = array (
	"pd" ,
	"gfdl" ,
	"bsd",
	"bsdu",
	"lgpl",
	"cc-by" ,
	"cc-by-sa" ,
	"pd-ineligible",
	"attribution",
	"bild-gfdl" ,
	"bild-cc-by",
	"bild-pd" ,
	'pd-art' ,
	'pd-self' ,
	'pd-author' ,
	'bild-cc-by-sa/3.0' ,
	'bild-cc-by-sa/3.0/de' ,
	'bild-gfdl-neu' ,
	"gemeinfrei" ,
	"wappen-pd" ,
	'pd-bain',
	'gfdl-no-disclaimers',
	'pd-usgov-fsa',
	'bild-frei',
	'gfdl-self-no-disclaimers',
	'gfdl-with-disclaimers',
	'cc-by-2.5',
	'pd-usgov-military-army',
	'cc-by-sa-2.0',
	'pd-because',
	"gfdl-self",
	'gfdl-con-disclaimer', #it
	"bild-pd-us",
	"bild-cc-by/2.0",
	"pd-netherlands",
	"gfdl-self-with-disclaimers",
	'bild-wikimediacopyright',
	"bild-gpl" ,
	"bild-wahrscheinlich-gfdl" ,
	"bild-frei",
	"bild-pd-alt",
	"bild-pd-alt-100",
	"bild-pd-schöpfungshöhe",
	"bild-cc-by-sa/2.0/de",
	"pd-usgov",
	"pd-layout",
	"pd-user",
	"pd-nasa",
	"bild-lgpl" ,
	"bild-by" ,
	'Frianvändning' ,
	'cc-sa',
	'cc-sa-1.0',
	"norightsreserved" ,
	"gpl" ,
	'bild-picswiss',
	'strukturformel',
	'copyrightedfreeuse',
	'copyrighted free use',
	'free media',
	'copyrightedfreeuse-link',
	'self2|gfdl', # From commons
	'self2|gfdl|cc-by-sa-2.5', # From commons
	'self2|cc-by-sa-2.5', # From commons
	'self|cc-by-sa' , # From commons
	'self|gfdl' , # From commons
	'self|gfdl|cc-by-sa-2.5', # From commons
	'eigenwerk' , # From NL
	'propio|gfdl', # From ES
	'propio|cc-by-sa-2.5', # From ES
	'propio|cc-by-sa', # From ES
	'propio|cc-by', # From ES
	'propio|dp', # From ES
	'propio|pd', # From ES
	'dp', # From ES
	'pd-czechgov', # From CS
	'artlibre', # From FR
	'fal', # Free Art License
	'vapaa', # FI
	'vapaa/nimimainittava', # FI
	'statistics netherlands map', # NL
	'GFDL-lastno', #sl
	
	# Hebrew now...
	'ייחוס',
	'צילום משתמש',
	'שימוש חופשי',
	'שימוש חופשי מוגן',
	'שימוש חופשי מוגן בתנאי',
	'דו-רישיוני',
	'דו-רשיוני',
) ;

$disambigs = array (
	'Begriffsklärung' ,
	'BKL' ,
	'disambig' ,
) ;

$evil_templates = array ( 
	'screenshot' , 
	'movie-screenshot' , 
	'film-screenshot' ,
	'non-free fair use in',
	'non-free media',
	'non-free media rationale',
	'non-free fair use in',
	'non-free use rationale',
	'non-free media',
	'non-free promotional',
	'non-free music video screenshot',
	'non-free television screenshot',
	'non-free logo',
	'non-free poster',
	'non-free film screenshot',
	'non-free fair use in',
	'do not move to commons',
	'di-disputed fair use rationale',
	'non-free media rationale',
	'withpermission',
	'non-free unsure',
	'tv screenshot',
	'fairusereview',
	'non-free dvd cover',
	'game-screenshot' ,
	'tv-screenshot' ,
	'windows-software-screenshot' ,
	'musicpromo-screenshot' ,
	'web-screenshot' ,
	'software-screenshot' ,
	'nintendo-screenshot' ,
	'mac-software-screenshot' ,
	'fairuse' , 
	'fair use' ,
	'mep image' ,
	'promophoto' , 
	'movieposter' , 
	'logo' ,
	'non-free media',
	'album-cover' ,
	'promotional' ,
	'copyright' ,
	'sportsposter',
	'albumcover' ,
	'comicpanel' ,
	'digimonimage' ,
	'publicity' ,
	'symbol' ,
	'newspapercover' ,
	'magazinecover' ,
	'dvdcover' ,
	'cdcover' ,
	'comiccover' ,
	'bookcover' ,
	'stamp' ,
	'gamecover' ,
	'sports-logo' ,
	'permission' ,
	'marque déposée',
	'money' ,
	'video tape cover' ,
	'video tape_cover' ,
	'hqfl logos' ,
	'hqfl_logos' ,
	'art' ,
	'kansikuva' ,
	'tna-photo' ,
	"capture d'écran de jeu vidéo" , # fr:
	'portada' ,
	'noncommercial' ,
	'icon' ,
	'pokeimage' ,
	'music sample' ,
	'disneylogo' ,
	'character-artwork' ,
	'wwe-photo' ,
	'book cover' ,
	'promo' ,
	'fotwpic' ,
	'uspsstamp' ,
	'bild-lizenz-unbekannt' , # de:
	'blu' , # de:
	'gfdl-presumed', # en:
	'bild-wahrscheinlich-gfdl', # de:
	'sin origen ni licencia', # es:
	'sol', # es:
	'sl', # es:
  'fair use in' , # ru:
  'تصویر فیلم', #ar
  'обложка', #bg
  'screenshot-film', #bs
  'kapak-dergi', #tr
  'promosyon', #tr
  'kapak-albüm', #tr
  'kapak-albüm-tc', #tr
  'kapak-kitap', #tr
  'ภาพจากโทรทัศน', #th
  'โปสเตอร', #th
  'ปกซีดี', #th
  'Омот албума', #sr
  'Снимак_екрана-ТВ', #sr
  'Промотивна_фотографија', #sr
  'Εξώφυλλο_άλμπουμ', #el
  'جلد آلبوم', #fa
  'kansikuva/äänite', #fi
  'kansikuva/videotallenne', #fi
  'marque déposée', #fr
  'audio-omot', #hr
  'sampulalbum', #id
  'bild-fu', #lb
  'tampilan-filem#', #ms
  'copyrighted', #it
  'dp-machetă', #ro
  'nevereficat', #ro
  'album-copertă', #ro
  'utilizare cinstită', #ro
  'utilizarecinstită', #ro
  'albumborító', #hu
  'könyvborító', #hu
  'نگاره قدیمی', #fa
  'поштена употреба', #sr
  'омот албума', #sr
  'justa uzo', #eo
  'albumborító', #hu
  
  #ru:
  'кадр',
  'обложка_музыкального_альбома',
  'плакат',
  'fu-layout',
  'fu-text',
  'обложка_музыкального_альбома',
  'fairuse in',
  
  # he:
  'non-free album cover',
  'עטיפת אלבום|אקון',
  'כרזת סרט|אקון',
  'עטיפת אלבום',
  'עטיפת אלבום|אלישה קיז',
  'שימוש הוגן',
  'עטיפת אלבום|בילי ג\'ואל',
  'שימוש הוגן',
) ;

$language_codes = array (
	'aa','af','ak','als','am','ang','ab','ar','an','roa-rup','frp','as',	'ast','gn','av','ay','az','bm','bn','zh-min-nan','map-mbs','ba','be',	'bh','bi','bo','bs','br','bg','ca','cv','ceb','cs','ch','ny','sn',	'tum','cho','co','za','cy','da','pdc','de','dv','arc','nv','dz','mh',	'et','el','en','es','eo','eu','ee','fa','fo','fr','fy','ff','fur','ga',	'gv','gd','gl','ki','gu','got','ko','ha','haw','hy','hi','ho','hr',	'io','ig','ilo','id','ia','ie','iu','ik','os','xh','zu','is','it','he',	'jv','kl','xal','kn','kr','ka','ks','csb','kk','kw','rw','ky','rn',	'sw','kv','kg','ht','kj','ku','lo','lad','la','lv','lb','lij','lt',	'li','ln','jbo','lg','lmo','hu','mk','mg','ml','mt','mi','mr','ms',	'mo','mn','mus','my','nah','na','fj','nl','nds-nl','cr','ne','ja','nap',	'ce','pih','no','nn','nrm','oc','or','om','ng','hz','ug','pa','pi',	'pam','pap','ps','km','pms','nds','pl','pt','ty','ksh','ro','rmy','rm',	'qu','ru','war','se','sm','sa','sg','sc','sco','st','tn','sq','scn',	'si','simple','sd','ss','sk','sl','so','sr','sh','su','fi','sv','tl',	'ta','tt','te','tet','th','vi','ti','tg','tpi','to','chr','chy','ve',	'tr','tk','tw','udm','bug','uk','ur','uz','vec','vo','fiu-vro','wa',	'vls','wo','ts','ii','yi','yo','zh-yue','bat-smg','zh','zh-tw','zh-cn'
) ;

$prefilled_requests = array () ;

$ansi2ascii = array (
  'ä' => 'a',
  'ö' => 'o',
  'ü' => 'u',
  'Ä' => 'A',
  'Ö' => 'O',
  'Ü' => 'U',
  'é' => 'e',
  'è' => 'e',
  'ë' => 'e',
  '’' => '',
  '"' => '',
  "'" => '',
  'ß' => 'ss',
) ;
