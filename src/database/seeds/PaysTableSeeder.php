<?php
namespace Ipsum\Reservation\database\seeds;

use Illuminate\Database\Seeder;
use Ipsum\Reservation\app\Models\Reservation\Pays;

class PaysTableSeeder extends Seeder
{
    public function run()
    {
        foreach ($this->getDatas() as $data) {
            Pays::create($data);
        }

    }

    private function getDatas()
    {
        return array(
            array('id' => '1','code' => '4','alpha2' => 'AF','alpha3' => 'AFG','nom' => 'Afghanistan'),
            array('id' => '2','code' => '8','alpha2' => 'AL','alpha3' => 'ALB','nom' => 'Albanie'),
            array('id' => '3','code' => '10','alpha2' => 'AQ','alpha3' => 'ATA','nom' => 'Antarctique'),
            array('id' => '4','code' => '12','alpha2' => 'DZ','alpha3' => 'DZA','nom' => 'Algérie'),
            array('id' => '5','code' => '16','alpha2' => 'AS','alpha3' => 'ASM','nom' => 'Samoa Américaines'),
            array('id' => '6','code' => '20','alpha2' => 'AD','alpha3' => 'AND','nom' => 'Andorre'),
            array('id' => '7','code' => '24','alpha2' => 'AO','alpha3' => 'AGO','nom' => 'Angola'),
            array('id' => '8','code' => '28','alpha2' => 'AG','alpha3' => 'ATG','nom' => 'Antigua-et-Barbuda'),
            array('id' => '9','code' => '31','alpha2' => 'AZ','alpha3' => 'AZE','nom' => 'Azerbaïdjan'),
            array('id' => '10','code' => '32','alpha2' => 'AR','alpha3' => 'ARG','nom' => 'Argentine'),
            array('id' => '11','code' => '36','alpha2' => 'AU','alpha3' => 'AUS','nom' => 'Australie'),
            array('id' => '12','code' => '40','alpha2' => 'AT','alpha3' => 'AUT','nom' => 'Autriche'),
            array('id' => '13','code' => '44','alpha2' => 'BS','alpha3' => 'BHS','nom' => 'Bahamas'),
            array('id' => '14','code' => '48','alpha2' => 'BH','alpha3' => 'BHR','nom' => 'Bahreïn'),
            array('id' => '15','code' => '50','alpha2' => 'BD','alpha3' => 'BGD','nom' => 'Bangladesh'),
            array('id' => '16','code' => '51','alpha2' => 'AM','alpha3' => 'ARM','nom' => 'Arménie'),
            array('id' => '17','code' => '52','alpha2' => 'BB','alpha3' => 'BRB','nom' => 'Barbade'),
            array('id' => '18','code' => '56','alpha2' => 'BE','alpha3' => 'BEL','nom' => 'Belgique'),
            array('id' => '19','code' => '60','alpha2' => 'BM','alpha3' => 'BMU','nom' => 'Bermudes'),
            array('id' => '20','code' => '64','alpha2' => 'BT','alpha3' => 'BTN','nom' => 'Bhoutan'),
            array('id' => '21','code' => '68','alpha2' => 'BO','alpha3' => 'BOL','nom' => 'Bolivie'),
            array('id' => '22','code' => '70','alpha2' => 'BA','alpha3' => 'BIH','nom' => 'Bosnie-Herzégovine'),
            array('id' => '23','code' => '72','alpha2' => 'BW','alpha3' => 'BWA','nom' => 'Botswana'),
            array('id' => '24','code' => '74','alpha2' => 'BV','alpha3' => 'BVT','nom' => 'Île Bouvet'),
            array('id' => '25','code' => '76','alpha2' => 'BR','alpha3' => 'BRA','nom' => 'Brésil'),
            array('id' => '26','code' => '84','alpha2' => 'BZ','alpha3' => 'BLZ','nom' => 'Belize'),
            array('id' => '27','code' => '86','alpha2' => 'IO','alpha3' => 'IOT','nom' => 'Territoire Britannique de l\'Océan Indien'),
            array('id' => '28','code' => '90','alpha2' => 'SB','alpha3' => 'SLB','nom' => 'Îles Salomon'),
            array('id' => '29','code' => '92','alpha2' => 'VG','alpha3' => 'VGB','nom' => 'Îles Vierges Britanniques'),
            array('id' => '30','code' => '96','alpha2' => 'BN','alpha3' => 'BRN','nom' => 'Brunéi Darussalam'),
            array('id' => '31','code' => '100','alpha2' => 'BG','alpha3' => 'BGR','nom' => 'Bulgarie'),
            array('id' => '32','code' => '104','alpha2' => 'MM','alpha3' => 'MMR','nom' => 'Myanmar'),
            array('id' => '33','code' => '108','alpha2' => 'BI','alpha3' => 'BDI','nom' => 'Burundi'),
            array('id' => '34','code' => '112','alpha2' => 'BY','alpha3' => 'BLR','nom' => 'Bélarus'),
            array('id' => '35','code' => '116','alpha2' => 'KH','alpha3' => 'KHM','nom' => 'Cambodge'),
            array('id' => '36','code' => '120','alpha2' => 'CM','alpha3' => 'CMR','nom' => 'Cameroun'),
            array('id' => '37','code' => '124','alpha2' => 'CA','alpha3' => 'CAN','nom' => 'Canada'),
            array('id' => '38','code' => '132','alpha2' => 'CV','alpha3' => 'CPV','nom' => 'Cap-vert'),
            array('id' => '39','code' => '136','alpha2' => 'KY','alpha3' => 'CYM','nom' => 'Îles Caïmanes'),
            array('id' => '40','code' => '140','alpha2' => 'CF','alpha3' => 'CAF','nom' => 'République Centrafricaine'),
            array('id' => '41','code' => '144','alpha2' => 'LK','alpha3' => 'LKA','nom' => 'Sri Lanka'),
            array('id' => '42','code' => '148','alpha2' => 'TD','alpha3' => 'TCD','nom' => 'Tchad'),
            array('id' => '43','code' => '152','alpha2' => 'CL','alpha3' => 'CHL','nom' => 'Chili'),
            array('id' => '44','code' => '156','alpha2' => 'CN','alpha3' => 'CHN','nom' => 'Chine'),
            array('id' => '45','code' => '158','alpha2' => 'TW','alpha3' => 'TWN','nom' => 'Taïwan'),
            array('id' => '46','code' => '162','alpha2' => 'CX','alpha3' => 'CXR','nom' => 'Île Christmas'),
            array('id' => '47','code' => '166','alpha2' => 'CC','alpha3' => 'CCK','nom' => 'Îles Cocos (Keeling)'),
            array('id' => '48','code' => '170','alpha2' => 'CO','alpha3' => 'COL','nom' => 'Colombie'),
            array('id' => '49','code' => '174','alpha2' => 'KM','alpha3' => 'COM','nom' => 'Comores'),
            array('id' => '50','code' => '175','alpha2' => 'YT','alpha3' => 'MYT','nom' => 'Mayotte'),
            array('id' => '51','code' => '178','alpha2' => 'CG','alpha3' => 'COG','nom' => 'République du Congo'),
            array('id' => '52','code' => '180','alpha2' => 'CD','alpha3' => 'COD','nom' => 'République Démocratique du Congo'),
            array('id' => '53','code' => '184','alpha2' => 'CK','alpha3' => 'COK','nom' => 'Îles Cook'),
            array('id' => '54','code' => '188','alpha2' => 'CR','alpha3' => 'CRI','nom' => 'Costa Rica'),
            array('id' => '55','code' => '191','alpha2' => 'HR','alpha3' => 'HRV','nom' => 'Croatie'),
            array('id' => '56','code' => '192','alpha2' => 'CU','alpha3' => 'CUB','nom' => 'Cuba'),
            array('id' => '57','code' => '196','alpha2' => 'CY','alpha3' => 'CYP','nom' => 'Chypre'),
            array('id' => '58','code' => '203','alpha2' => 'CZ','alpha3' => 'CZE','nom' => 'République Tchèque'),
            array('id' => '59','code' => '204','alpha2' => 'BJ','alpha3' => 'BEN','nom' => 'Bénin'),
            array('id' => '60','code' => '208','alpha2' => 'DK','alpha3' => 'DNK','nom' => 'Danemark'),
            array('id' => '61','code' => '212','alpha2' => 'DM','alpha3' => 'DMA','nom' => 'Dominique'),
            array('id' => '62','code' => '214','alpha2' => 'DO','alpha3' => 'DOM','nom' => 'République Dominicaine'),
            array('id' => '63','code' => '218','alpha2' => 'EC','alpha3' => 'ECU','nom' => 'Équateur'),
            array('id' => '64','code' => '222','alpha2' => 'SV','alpha3' => 'SLV','nom' => 'El Salvador'),
            array('id' => '65','code' => '226','alpha2' => 'GQ','alpha3' => 'GNQ','nom' => 'Guinée Équatoriale'),
            array('id' => '66','code' => '231','alpha2' => 'ET','alpha3' => 'ETH','nom' => 'Éthiopie'),
            array('id' => '67','code' => '232','alpha2' => 'ER','alpha3' => 'ERI','nom' => 'Érythrée'),
            array('id' => '68','code' => '233','alpha2' => 'EE','alpha3' => 'EST','nom' => 'Estonie'),
            array('id' => '69','code' => '234','alpha2' => 'FO','alpha3' => 'FRO','nom' => 'Îles Féroé'),
            array('id' => '70','code' => '238','alpha2' => 'FK','alpha3' => 'FLK','nom' => 'Îles (malvinas) Falkland'),
            array('id' => '71','code' => '239','alpha2' => 'GS','alpha3' => 'SGS','nom' => 'Géorgie du Sud et les Îles Sandwich du Sud'),
            array('id' => '72','code' => '242','alpha2' => 'FJ','alpha3' => 'FJI','nom' => 'Fidji'),
            array('id' => '73','code' => '246','alpha2' => 'FI','alpha3' => 'FIN','nom' => 'Finlande'),
            array('id' => '74','code' => '248','alpha2' => 'AX','alpha3' => 'ALA','nom' => 'Îles Åland'),
            array('id' => '75','code' => '250','alpha2' => 'FR','alpha3' => 'FRA','nom' => 'France métropolitaine'),
            array('id' => '76','code' => '254','alpha2' => 'GF','alpha3' => 'GUF','nom' => 'Guyane Française'),
            array('id' => '77','code' => '258','alpha2' => 'PF','alpha3' => 'PYF','nom' => 'Polynésie Française'),
            array('id' => '78','code' => '260','alpha2' => 'TF','alpha3' => 'ATF','nom' => 'Terres Australes Françaises'),
            array('id' => '79','code' => '262','alpha2' => 'DJ','alpha3' => 'DJI','nom' => 'Djibouti'),
            array('id' => '80','code' => '266','alpha2' => 'GA','alpha3' => 'GAB','nom' => 'Gabon'),
            array('id' => '81','code' => '268','alpha2' => 'GE','alpha3' => 'GEO','nom' => 'Géorgie'),
            array('id' => '82','code' => '270','alpha2' => 'GM','alpha3' => 'GMB','nom' => 'Gambie'),
            array('id' => '83','code' => '275','alpha2' => 'PS','alpha3' => 'PSE','nom' => 'Territoire Palestinien Occupé'),
            array('id' => '84','code' => '276','alpha2' => 'DE','alpha3' => 'DEU','nom' => 'Allemagne'),
            array('id' => '85','code' => '288','alpha2' => 'GH','alpha3' => 'GHA','nom' => 'Ghana'),
            array('id' => '86','code' => '292','alpha2' => 'GI','alpha3' => 'GIB','nom' => 'Gibraltar'),
            array('id' => '87','code' => '296','alpha2' => 'KI','alpha3' => 'KIR','nom' => 'Kiribati'),
            array('id' => '88','code' => '300','alpha2' => 'GR','alpha3' => 'GRC','nom' => 'Grèce'),
            array('id' => '89','code' => '304','alpha2' => 'GL','alpha3' => 'GRL','nom' => 'Groenland'),
            array('id' => '90','code' => '308','alpha2' => 'GD','alpha3' => 'GRD','nom' => 'Grenade'),
            array('id' => '91','code' => '312','alpha2' => 'GP','alpha3' => 'GLP','nom' => 'Guadeloupe'),
            array('id' => '92','code' => '316','alpha2' => 'GU','alpha3' => 'GUM','nom' => 'Guam'),
            array('id' => '93','code' => '320','alpha2' => 'GT','alpha3' => 'GTM','nom' => 'Guatemala'),
            array('id' => '94','code' => '324','alpha2' => 'GN','alpha3' => 'GIN','nom' => 'Guinée'),
            array('id' => '95','code' => '328','alpha2' => 'GY','alpha3' => 'GUY','nom' => 'Guyana'),
            array('id' => '96','code' => '332','alpha2' => 'HT','alpha3' => 'HTI','nom' => 'Haïti'),
            array('id' => '97','code' => '334','alpha2' => 'HM','alpha3' => 'HMD','nom' => 'Îles Heard et Mcdonald'),
            array('id' => '98','code' => '336','alpha2' => 'VA','alpha3' => 'VAT','nom' => 'Saint-Siège (état de la Cité du Vatican)'),
            array('id' => '99','code' => '340','alpha2' => 'HN','alpha3' => 'HND','nom' => 'Honduras'),
            array('id' => '100','code' => '344','alpha2' => 'HK','alpha3' => 'HKG','nom' => 'Hong-Kong'),
            array('id' => '101','code' => '348','alpha2' => 'HU','alpha3' => 'HUN','nom' => 'Hongrie'),
            array('id' => '102','code' => '352','alpha2' => 'IS','alpha3' => 'ISL','nom' => 'Islande'),
            array('id' => '103','code' => '356','alpha2' => 'IN','alpha3' => 'IND','nom' => 'Inde'),
            array('id' => '104','code' => '360','alpha2' => 'ID','alpha3' => 'IDN','nom' => 'Indonésie'),
            array('id' => '105','code' => '364','alpha2' => 'IR','alpha3' => 'IRN','nom' => 'République Islamique d\'Iran'),
            array('id' => '106','code' => '368','alpha2' => 'IQ','alpha3' => 'IRQ','nom' => 'Iraq'),
            array('id' => '107','code' => '372','alpha2' => 'IE','alpha3' => 'IRL','nom' => 'Irlande'),
            array('id' => '108','code' => '376','alpha2' => 'IL','alpha3' => 'ISR','nom' => 'Israël'),
            array('id' => '109','code' => '380','alpha2' => 'IT','alpha3' => 'ITA','nom' => 'Italie'),
            array('id' => '110','code' => '384','alpha2' => 'CI','alpha3' => 'CIV','nom' => 'Côte d\'Ivoire'),
            array('id' => '111','code' => '388','alpha2' => 'JM','alpha3' => 'JAM','nom' => 'Jamaïque'),
            array('id' => '112','code' => '392','alpha2' => 'JP','alpha3' => 'JPN','nom' => 'Japon'),
            array('id' => '113','code' => '398','alpha2' => 'KZ','alpha3' => 'KAZ','nom' => 'Kazakhstan'),
            array('id' => '114','code' => '400','alpha2' => 'JO','alpha3' => 'JOR','nom' => 'Jordanie'),
            array('id' => '115','code' => '404','alpha2' => 'KE','alpha3' => 'KEN','nom' => 'Kenya'),
            array('id' => '116','code' => '408','alpha2' => 'KP','alpha3' => 'PRK','nom' => 'République Populaire Démocratique de Corée'),
            array('id' => '117','code' => '410','alpha2' => 'KR','alpha3' => 'KOR','nom' => 'République de Corée'),
            array('id' => '118','code' => '414','alpha2' => 'KW','alpha3' => 'KWT','nom' => 'Koweït'),
            array('id' => '119','code' => '417','alpha2' => 'KG','alpha3' => 'KGZ','nom' => 'Kirghizistan'),
            array('id' => '120','code' => '418','alpha2' => 'LA','alpha3' => 'LAO','nom' => 'République Démocratique Populaire Lao'),
            array('id' => '121','code' => '422','alpha2' => 'LB','alpha3' => 'LBN','nom' => 'Liban'),
            array('id' => '122','code' => '426','alpha2' => 'LS','alpha3' => 'LSO','nom' => 'Lesotho'),
            array('id' => '123','code' => '428','alpha2' => 'LV','alpha3' => 'LVA','nom' => 'Lettonie'),
            array('id' => '124','code' => '430','alpha2' => 'LR','alpha3' => 'LBR','nom' => 'Libéria'),
            array('id' => '125','code' => '434','alpha2' => 'LY','alpha3' => 'LBY','nom' => 'Jamahiriya Arabe Libyenne'),
            array('id' => '126','code' => '438','alpha2' => 'LI','alpha3' => 'LIE','nom' => 'Liechtenstein'),
            array('id' => '127','code' => '440','alpha2' => 'LT','alpha3' => 'LTU','nom' => 'Lituanie'),
            array('id' => '128','code' => '442','alpha2' => 'LU','alpha3' => 'LUX','nom' => 'Luxembourg'),
            array('id' => '129','code' => '446','alpha2' => 'MO','alpha3' => 'MAC','nom' => 'Macao'),
            array('id' => '130','code' => '450','alpha2' => 'MG','alpha3' => 'MDG','nom' => 'Madagascar'),
            array('id' => '131','code' => '454','alpha2' => 'MW','alpha3' => 'MWI','nom' => 'Malawi'),
            array('id' => '132','code' => '458','alpha2' => 'MY','alpha3' => 'MYS','nom' => 'Malaisie'),
            array('id' => '133','code' => '462','alpha2' => 'MV','alpha3' => 'MDV','nom' => 'Maldives'),
            array('id' => '134','code' => '466','alpha2' => 'ML','alpha3' => 'MLI','nom' => 'Mali'),
            array('id' => '135','code' => '470','alpha2' => 'MT','alpha3' => 'MLT','nom' => 'Malte'),
            array('id' => '136','code' => '474','alpha2' => 'MQ','alpha3' => 'MTQ','nom' => 'Martinique'),
            array('id' => '137','code' => '478','alpha2' => 'MR','alpha3' => 'MRT','nom' => 'Mauritanie'),
            array('id' => '138','code' => '480','alpha2' => 'MU','alpha3' => 'MUS','nom' => 'Maurice'),
            array('id' => '139','code' => '484','alpha2' => 'MX','alpha3' => 'MEX','nom' => 'Mexique'),
            array('id' => '140','code' => '492','alpha2' => 'MC','alpha3' => 'MCO','nom' => 'Monaco'),
            array('id' => '141','code' => '496','alpha2' => 'MN','alpha3' => 'MNG','nom' => 'Mongolie'),
            array('id' => '142','code' => '498','alpha2' => 'MD','alpha3' => 'MDA','nom' => 'République de Moldova'),
            array('id' => '143','code' => '500','alpha2' => 'MS','alpha3' => 'MSR','nom' => 'Montserrat'),
            array('id' => '144','code' => '504','alpha2' => 'MA','alpha3' => 'MAR','nom' => 'Maroc'),
            array('id' => '145','code' => '508','alpha2' => 'MZ','alpha3' => 'MOZ','nom' => 'Mozambique'),
            array('id' => '146','code' => '512','alpha2' => 'OM','alpha3' => 'OMN','nom' => 'Oman'),
            array('id' => '147','code' => '516','alpha2' => 'NA','alpha3' => 'NAM','nom' => 'Namibie'),
            array('id' => '148','code' => '520','alpha2' => 'NR','alpha3' => 'NRU','nom' => 'Nauru'),
            array('id' => '149','code' => '524','alpha2' => 'NP','alpha3' => 'NPL','nom' => 'Népal'),
            array('id' => '150','code' => '528','alpha2' => 'NL','alpha3' => 'NLD','nom' => 'Pays-Bas'),
            array('id' => '151','code' => '530','alpha2' => 'AN','alpha3' => 'ANT','nom' => 'Antilles Néerlandaises'),
            array('id' => '152','code' => '533','alpha2' => 'AW','alpha3' => 'ABW','nom' => 'Aruba'),
            array('id' => '153','code' => '540','alpha2' => 'NC','alpha3' => 'NCL','nom' => 'Nouvelle-Calédonie'),
            array('id' => '154','code' => '548','alpha2' => 'VU','alpha3' => 'VUT','nom' => 'Vanuatu'),
            array('id' => '155','code' => '554','alpha2' => 'NZ','alpha3' => 'NZL','nom' => 'Nouvelle-Zélande'),
            array('id' => '156','code' => '558','alpha2' => 'NI','alpha3' => 'NIC','nom' => 'Nicaragua'),
            array('id' => '157','code' => '562','alpha2' => 'NE','alpha3' => 'NER','nom' => 'Niger'),
            array('id' => '158','code' => '566','alpha2' => 'NG','alpha3' => 'NGA','nom' => 'Nigéria'),
            array('id' => '159','code' => '570','alpha2' => 'NU','alpha3' => 'NIU','nom' => 'Niué'),
            array('id' => '160','code' => '574','alpha2' => 'NF','alpha3' => 'NFK','nom' => 'Île Norfolk'),
            array('id' => '161','code' => '578','alpha2' => 'NO','alpha3' => 'NOR','nom' => 'Norvège'),
            array('id' => '162','code' => '580','alpha2' => 'MP','alpha3' => 'MNP','nom' => 'Îles Mariannes du Nord'),
            array('id' => '163','code' => '581','alpha2' => 'UM','alpha3' => 'UMI','nom' => 'Îles Mineures Éloignées des États-Unis'),
            array('id' => '164','code' => '583','alpha2' => 'FM','alpha3' => 'FSM','nom' => 'États Fédérés de Micronésie'),
            array('id' => '165','code' => '584','alpha2' => 'MH','alpha3' => 'MHL','nom' => 'Îles Marshall'),
            array('id' => '166','code' => '585','alpha2' => 'PW','alpha3' => 'PLW','nom' => 'Palaos'),
            array('id' => '167','code' => '586','alpha2' => 'PK','alpha3' => 'PAK','nom' => 'Pakistan'),
            array('id' => '168','code' => '591','alpha2' => 'PA','alpha3' => 'PAN','nom' => 'Panama'),
            array('id' => '169','code' => '598','alpha2' => 'PG','alpha3' => 'PNG','nom' => 'Papouasie-Nouvelle-Guinée'),
            array('id' => '170','code' => '600','alpha2' => 'PY','alpha3' => 'PRY','nom' => 'Paraguay'),
            array('id' => '171','code' => '604','alpha2' => 'PE','alpha3' => 'PER','nom' => 'Pérou'),
            array('id' => '172','code' => '608','alpha2' => 'PH','alpha3' => 'PHL','nom' => 'Philippines'),
            array('id' => '173','code' => '612','alpha2' => 'PN','alpha3' => 'PCN','nom' => 'Pitcairn'),
            array('id' => '174','code' => '616','alpha2' => 'PL','alpha3' => 'POL','nom' => 'Pologne'),
            array('id' => '175','code' => '620','alpha2' => 'PT','alpha3' => 'PRT','nom' => 'Portugal'),
            array('id' => '176','code' => '624','alpha2' => 'GW','alpha3' => 'GNB','nom' => 'Guinée-Bissau'),
            array('id' => '177','code' => '626','alpha2' => 'TL','alpha3' => 'TLS','nom' => 'Timor-Leste'),
            array('id' => '178','code' => '630','alpha2' => 'PR','alpha3' => 'PRI','nom' => 'Porto Rico'),
            array('id' => '179','code' => '634','alpha2' => 'QA','alpha3' => 'QAT','nom' => 'Qatar'),
            array('id' => '180','code' => '638','alpha2' => 'RE','alpha3' => 'REU','nom' => 'Réunion'),
            array('id' => '181','code' => '642','alpha2' => 'RO','alpha3' => 'ROU','nom' => 'Roumanie'),
            array('id' => '182','code' => '643','alpha2' => 'RU','alpha3' => 'RUS','nom' => 'Fédération de Russie'),
            array('id' => '183','code' => '646','alpha2' => 'RW','alpha3' => 'RWA','nom' => 'Rwanda'),
            array('id' => '184','code' => '654','alpha2' => 'SH','alpha3' => 'SHN','nom' => 'Sainte-Hélène'),
            array('id' => '185','code' => '659','alpha2' => 'KN','alpha3' => 'KNA','nom' => 'Saint-Kitts-et-Nevis'),
            array('id' => '186','code' => '660','alpha2' => 'AI','alpha3' => 'AIA','nom' => 'Anguilla'),
            array('id' => '187','code' => '662','alpha2' => 'LC','alpha3' => 'LCA','nom' => 'Sainte-Lucie'),
            array('id' => '188','code' => '666','alpha2' => 'PM','alpha3' => 'SPM','nom' => 'Saint-Pierre-et-Miquelon'),
            array('id' => '189','code' => '670','alpha2' => 'VC','alpha3' => 'VCT','nom' => 'Saint-Vincent-et-les Grenadines'),
            array('id' => '190','code' => '674','alpha2' => 'SM','alpha3' => 'SMR','nom' => 'Saint-Marin'),
            array('id' => '191','code' => '678','alpha2' => 'ST','alpha3' => 'STP','nom' => 'Sao Tomé-et-Principe'),
            array('id' => '192','code' => '682','alpha2' => 'SA','alpha3' => 'SAU','nom' => 'Arabie Saoudite'),
            array('id' => '193','code' => '686','alpha2' => 'SN','alpha3' => 'SEN','nom' => 'Sénégal'),
            array('id' => '194','code' => '690','alpha2' => 'SC','alpha3' => 'SYC','nom' => 'Seychelles'),
            array('id' => '195','code' => '694','alpha2' => 'SL','alpha3' => 'SLE','nom' => 'Sierra Leone'),
            array('id' => '196','code' => '702','alpha2' => 'SG','alpha3' => 'SGP','nom' => 'Singapour'),
            array('id' => '197','code' => '703','alpha2' => 'SK','alpha3' => 'SVK','nom' => 'Slovaquie'),
            array('id' => '198','code' => '704','alpha2' => 'VN','alpha3' => 'VNM','nom' => 'Viet Nam'),
            array('id' => '199','code' => '705','alpha2' => 'SI','alpha3' => 'SVN','nom' => 'Slovénie'),
            array('id' => '200','code' => '706','alpha2' => 'SO','alpha3' => 'SOM','nom' => 'Somalie'),
            array('id' => '201','code' => '710','alpha2' => 'ZA','alpha3' => 'ZAF','nom' => 'Afrique du Sud'),
            array('id' => '202','code' => '716','alpha2' => 'ZW','alpha3' => 'ZWE','nom' => 'Zimbabwe'),
            array('id' => '203','code' => '724','alpha2' => 'ES','alpha3' => 'ESP','nom' => 'Espagne'),
            array('id' => '204','code' => '732','alpha2' => 'EH','alpha3' => 'ESH','nom' => 'Sahara Occidental'),
            array('id' => '205','code' => '736','alpha2' => 'SD','alpha3' => 'SDN','nom' => 'Soudan'),
            array('id' => '206','code' => '740','alpha2' => 'SR','alpha3' => 'SUR','nom' => 'Suriname'),
            array('id' => '207','code' => '744','alpha2' => 'SJ','alpha3' => 'SJM','nom' => 'Svalbard etÎle Jan Mayen'),
            array('id' => '208','code' => '748','alpha2' => 'SZ','alpha3' => 'SWZ','nom' => 'Swaziland'),
            array('id' => '209','code' => '752','alpha2' => 'SE','alpha3' => 'SWE','nom' => 'Suède'),
            array('id' => '210','code' => '756','alpha2' => 'CH','alpha3' => 'CHE','nom' => 'Suisse'),
            array('id' => '211','code' => '760','alpha2' => 'SY','alpha3' => 'SYR','nom' => 'République Arabe Syrienne'),
            array('id' => '212','code' => '762','alpha2' => 'TJ','alpha3' => 'TJK','nom' => 'Tadjikistan'),
            array('id' => '213','code' => '764','alpha2' => 'TH','alpha3' => 'THA','nom' => 'Thaïlande'),
            array('id' => '214','code' => '768','alpha2' => 'TG','alpha3' => 'TGO','nom' => 'Togo'),
            array('id' => '215','code' => '772','alpha2' => 'TK','alpha3' => 'TKL','nom' => 'Tokelau'),
            array('id' => '216','code' => '776','alpha2' => 'TO','alpha3' => 'TON','nom' => 'Tonga'),
            array('id' => '217','code' => '780','alpha2' => 'TT','alpha3' => 'TTO','nom' => 'Trinité-et-Tobago'),
            array('id' => '218','code' => '784','alpha2' => 'AE','alpha3' => 'ARE','nom' => 'Émirats Arabes Unis'),
            array('id' => '219','code' => '788','alpha2' => 'TN','alpha3' => 'TUN','nom' => 'Tunisie'),
            array('id' => '220','code' => '792','alpha2' => 'TR','alpha3' => 'TUR','nom' => 'Turquie'),
            array('id' => '221','code' => '795','alpha2' => 'TM','alpha3' => 'TKM','nom' => 'Turkménistan'),
            array('id' => '222','code' => '796','alpha2' => 'TC','alpha3' => 'TCA','nom' => 'Îles Turks et Caïques'),
            array('id' => '223','code' => '798','alpha2' => 'TV','alpha3' => 'TUV','nom' => 'Tuvalu'),
            array('id' => '224','code' => '800','alpha2' => 'UG','alpha3' => 'UGA','nom' => 'Ouganda'),
            array('id' => '225','code' => '804','alpha2' => 'UA','alpha3' => 'UKR','nom' => 'Ukraine'),
            array('id' => '226','code' => '807','alpha2' => 'MK','alpha3' => 'MKD','nom' => 'L\'ex-République Yougoslave de Macédoine'),
            array('id' => '227','code' => '818','alpha2' => 'EG','alpha3' => 'EGY','nom' => 'Égypte'),
            array('id' => '228','code' => '826','alpha2' => 'GB','alpha3' => 'GBR','nom' => 'Royaume-Uni'),
            array('id' => '229','code' => '833','alpha2' => 'IM','alpha3' => 'IMN','nom' => 'Île de Man'),
            array('id' => '230','code' => '834','alpha2' => 'TZ','alpha3' => 'TZA','nom' => 'République-Unie de Tanzanie'),
            array('id' => '231','code' => '840','alpha2' => 'US','alpha3' => 'USA','nom' => 'États-Unis'),
            array('id' => '232','code' => '850','alpha2' => 'VI','alpha3' => 'VIR','nom' => 'Îles Vierges des États-Unis'),
            array('id' => '233','code' => '854','alpha2' => 'BF','alpha3' => 'BFA','nom' => 'Burkina Faso'),
            array('id' => '234','code' => '858','alpha2' => 'UY','alpha3' => 'URY','nom' => 'Uruguay'),
            array('id' => '235','code' => '860','alpha2' => 'UZ','alpha3' => 'UZB','nom' => 'Ouzbékistan'),
            array('id' => '236','code' => '862','alpha2' => 'VE','alpha3' => 'VEN','nom' => 'Venezuela'),
            array('id' => '237','code' => '876','alpha2' => 'WF','alpha3' => 'WLF','nom' => 'Wallis et Futuna'),
            array('id' => '238','code' => '882','alpha2' => 'WS','alpha3' => 'WSM','nom' => 'Samoa'),
            array('id' => '239','code' => '887','alpha2' => 'YE','alpha3' => 'YEM','nom' => 'Yémen'),
            array('id' => '240','code' => '891','alpha2' => 'CS','alpha3' => 'SCG','nom' => 'Serbie-et-Monténégro'),
            array('id' => '241','code' => '894','alpha2' => 'ZM','alpha3' => 'ZMB','nom' => 'Zambie')
        );
    }
}
