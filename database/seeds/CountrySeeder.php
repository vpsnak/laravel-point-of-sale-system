<?php

use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('country')->insert([
            [
                'name' => 'Andorra',
                'iso2_code' => 'AD',
                'iso3_code' => 'AND',
            ],
            [
                'name' => 'United Arab Emirates',
                'iso2_code' => 'AE',
                'iso3_code' => 'ARE'
            ],
            [
                'name' => 'Afghanistan',
                'iso2_code' => 'AF',
                'iso3_code' => 'AFG'
            ],
            [
                'name' => 'Antigua and Barbuda',
                'iso2_code' => 'AG',
                'iso3_code' => 'ATG'
            ],
            [
                'name' => 'Aland Islands',
                'iso2_code' => 'AI',
                'iso3_code' => 'AIA'
            ],
            [
                'name' => 'Albania',
                'iso2_code' => 'AL',
                'iso3_code' => 'ALB'
            ],
            [
                'name' => 'Armenia',
                'iso2_code' => 'AM',
                'iso3_code' => 'ARM'
            ],
            [
                'name' => 'Netherlands Antilles',
                'iso2_code' => 'AN',
                'iso3_code' => 'ANT'
            ],
            [
                'name' => 'Angola',
                'iso2_code' => 'AO',
                'iso3_code' => 'AGO'
            ],
            [
                'name' => 'Antarctica',
                'iso2_code' => 'AQ',
                'iso3_code' => 'ATA'
            ],
            [
                'name' => 'Argentina',
                'iso2_code' => 'AR',
                'iso3_code' => 'ARG'
            ],
            [
                'name' => 'American Samoa',
                'iso2_code' => 'AS',
                'iso3_code' => 'ASM'
            ],
            [
                'name' => 'Austria',
                'iso2_code' => 'AT',
                'iso3_code' => 'AUT'
            ],
            [
                'name' => 'Australia',
                'iso2_code' => 'AU',
                'iso3_code' => 'AUS'
            ],
            [
                'name' => 'Aruba',
                'iso2_code' => 'AW',
                'iso3_code' => 'ABW'
            ],
            [
                'name' => 'Aland Islands',
                'iso2_code' => 'AX',
                'iso3_code' => 'ALA'
            ],
            [
                'name' => 'Azerbaijan',
                'iso2_code' => 'AZ',
                'iso3_code' => 'AZE'
            ],
            [
                'name' => 'Bosnia and Herzegovina',
                'iso2_code' => 'BA',
                'iso3_code' => 'BIH'
            ],
            [
                'name' => 'Barbados',
                'iso2_code' => 'BB',
                'iso3_code' => 'BRB'
            ],
            [
                'name' => 'Bangladesh',
                'iso2_code' => 'BD',
                'iso3_code' => 'BGD'
            ],
            [
                'name' => 'Belgium',
                'iso2_code' => 'BE',
                'iso3_code' => 'BEL'
            ],
            [
                'name' => 'Burkina Faso',
                'iso2_code' => 'BF',
                'iso3_code' => 'BFA'
            ],
            [
                'name' => 'Bulgaria',
                'iso2_code' => 'BG',
                'iso3_code' => 'BGR'
            ],
            [
                'name' => 'Bahrain',
                'iso2_code' => 'BH',
                'iso3_code' => 'BHR'
            ],
            [
                'name' => 'Burundi',
                'iso2_code' => 'BI',
                'iso3_code' => 'BDI'
            ],
            [
                'name' => 'Benin',
                'iso2_code' => 'BJ',
                'iso3_code' => 'BEN'
            ],
            [
                'name' => 'Saint-Barthélemy',
                'iso2_code' => 'BL',
                'iso3_code' => 'BLM'
            ],
            [
                'name' => 'Bermuda',
                'iso2_code' => 'BM',
                'iso3_code' => 'BMU'
            ],
            [
                'name' => 'Brunei Darussalam',
                'iso2_code' => 'BN',
                'iso3_code' => 'BRN'
            ],
            [
                'name' => 'Bolivia',
                'iso2_code' => 'BO',
                'iso3_code' => 'BOL'
            ],
            [
                'name' => 'Brazil',
                'iso2_code' => 'BR',
                'iso3_code' => 'BRA'
            ],
            [
                'name' => 'Bahamas',
                'iso2_code' => 'BS',
                'iso3_code' => 'BHS'
            ],
            [
                'name' => 'Bhutan',
                'iso2_code' => 'BT',
                'iso3_code' => 'BTN'
            ],
            [
                'name' => 'Bouvet Island',
                'iso2_code' => 'BV',
                'iso3_code' => 'BVT'
            ],
            [
                'name' => 'Botswana',
                'iso2_code' => 'BW',
                'iso3_code' => 'BWA'
            ],
            [
                'name' => 'Belarus',
                'iso2_code' => 'BY',
                'iso3_code' => 'BLR'
            ],
            [
                'name' => 'Belize',
                'iso2_code' => 'BZ',
                'iso3_code' => 'BLZ'
            ],
            [
                'name' => 'Canada',
                'iso2_code' => 'CA',
                'iso3_code' => 'CAN'
            ],
            [
                'name' => 'Cocos (Keeling) Islands',
                'iso2_code' => 'CC',
                'iso3_code' => 'CCK'
            ],
            [
                'name' => 'Congo (Kinshasa)',
                'iso2_code' => 'CD',
                'iso3_code' => 'COD'
            ],
            [
                'name' => 'Central African Republic',
                'iso2_code' => 'CF',
                'iso3_code' => 'CAF'
            ],
            [
                'name' => 'Congo (Brazzaville)',
                'iso2_code' => 'CG',
                'iso3_code' => 'COG'
            ],
            [
                'name' => 'Switzerland',
                'iso2_code' => 'CH',
                'iso3_code' => 'CHE'
            ],
            [
                'name' => 'Côte d\'Ivoire',
                'iso2_code' => 'CI',
                'iso3_code' => 'CIV'
            ],
            [
                'name' => 'Cook Islands',
                'iso2_code' => 'CK',
                'iso3_code' => 'COK'
            ],
            [
                'name' => 'Chile',
                'iso2_code' => 'CL',
                'iso3_code' => 'CHL'
            ],
            [
                'name' => 'Cameroon',
                'iso2_code' => 'CM',
                'iso3_code' => 'CMR'
            ],
            [
                'name' => 'China',
                'iso2_code' => 'CN',
                'iso3_code' => 'CHN'
            ],
            [
                'name' => 'Colombia',
                'iso2_code' => 'CO',
                'iso3_code' => 'COL'
            ],
            [
                'name' => 'Costa Rica',
                'iso2_code' => 'CR',
                'iso3_code' => 'CRI'
            ],
            [
                'name' => 'Cuba',
                'iso2_code' => 'CU',
                'iso3_code' => 'CUB'
            ],
            [
                'name' => 'Cape Verde',
                'iso2_code' => 'CV',
                'iso3_code' => 'CPV'
            ],
            [
                'name' => 'Christmas Island',
                'iso2_code' => 'CX',
                'iso3_code' => 'CXR'
            ],
            [
                'name' => 'Cyprus',
                'iso2_code' => 'CY',
                'iso3_code' => 'CYP'
            ],
            [
                'name' => 'Czech Republic',
                'iso2_code' => 'CZ',
                'iso3_code' => 'CZE'
            ],
            [
                'name' => 'Germany',
                'iso2_code' => 'DE',
                'iso3_code' => 'DEU'
            ],
            [
                'name' => 'Djibouti',
                'iso2_code' => 'DJ',
                'iso3_code' => 'DJI'
            ],
            [
                'name' => 'Denmark',
                'iso2_code' => 'DK',
                'iso3_code' => 'DNK'
            ],
            [
                'name' => 'Dominica',
                'iso2_code' => 'DM',
                'iso3_code' => 'DMA'
            ],
            [
                'name' => 'Dominican Republic',
                'iso2_code' => 'DO',
                'iso3_code' => 'DOM'
            ],
            [
                'name' => 'Algeria',
                'iso2_code' => 'DZ',
                'iso3_code' => 'DZA'
            ],
            [
                'name' => 'Ecuador',
                'iso2_code' => 'EC',
                'iso3_code' => 'ECU'
            ],
            [
                'name' => 'Estonia',
                'iso2_code' => 'EE',
                'iso3_code' => 'EST'
            ],
            [
                'name' => 'Egypt',
                'iso2_code' => 'EG',
                'iso3_code' => 'EGY'
            ],
            [
                'name' => 'Western Sahara',
                'iso2_code' => 'EH',
                'iso3_code' => 'ESH'
            ],
            [
                'name' => 'Eritrea',
                'iso2_code' => 'ER',
                'iso3_code' => 'ERI'
            ],
            [
                'name' => 'Spain',
                'iso2_code' => 'ES',
                'iso3_code' => 'ESP'
            ],
            [
                'name' => 'Ethiopia',
                'iso2_code' => 'ET',
                'iso3_code' => 'ETH'
            ],
            [
                'name' => 'Finland',
                'iso2_code' => 'FI',
                'iso3_code' => 'FIN'
            ],
            [
                'name' => 'Fiji',
                'iso2_code' => 'FJ',
                'iso3_code' => 'FJI'
            ],
            [
                'name' => 'Falkland Islands (Malvinas)',
                'iso2_code' => 'FK',
                'iso3_code' => 'FLK'
            ],
            [
                'name' => 'Micronesia, Federated States of',
                'iso2_code' => 'FM',
                'iso3_code' => 'FSM'
            ],
            [
                'name' => 'Faroe Islands',
                'iso2_code' => 'FO',
                'iso3_code' => 'FRO'
            ],
            [
                'name' => 'France',
                'iso2_code' => 'FR',
                'iso3_code' => 'FRA'
            ],
            [
                'name' => 'Gabon',
                'iso2_code' => 'GA',
                'iso3_code' => 'GAB'
            ],
            [
                'name' => 'United Kingdom',
                'iso2_code' => 'GB',
                'iso3_code' => 'GBR'
            ],
            [
                'name' => 'Grenada',
                'iso2_code' => 'GD',
                'iso3_code' => 'GRD'
            ],
            [
                'name' => 'Georgia',
                'iso2_code' => 'GE',
                'iso3_code' => 'GEO'
            ],
            [
                'name' => 'French Guiana',
                'iso2_code' => 'GF',
                'iso3_code' => 'GUF'
            ],
            [
                'name' => 'Guernsey',
                'iso2_code' => 'GG',
                'iso3_code' => 'GGY'
            ],
            [
                'name' => 'Ghana',
                'iso2_code' => 'GH',
                'iso3_code' => 'GHA'
            ],
            [
                'name' => 'Gibraltar',
                'iso2_code' => 'GI',
                'iso3_code' => 'GIB'
            ],
            [
                'name' => 'Greenland',
                'iso2_code' => 'GL',
                'iso3_code' => 'GRL'
            ],
            [
                'name' => 'Gambia',
                'iso2_code' => 'GM',
                'iso3_code' => 'GMB'
            ],
            [
                'name' => 'Guinea',
                'iso2_code' => 'GN',
                'iso3_code' => 'GIN'
            ],
            [
                'name' => 'Guadeloupe',
                'iso2_code' => 'GP',
                'iso3_code' => 'GLP'
            ],
            [
                'name' => 'Equatorial Guinea',
                'iso2_code' => 'GQ',
                'iso3_code' => 'GNQ'
            ],
            [
                'name' => '',
                'iso2_code' => 'GR',
                'iso3_code' => 'GRC'
            ],
            [
                'name' => '',
                'iso2_code' => 'GS',
                'iso3_code' => 'SGS'
            ],
            [
                'name' => '',
                'iso2_code' => 'GT',
                'iso3_code' => 'GTM'
            ],
            [
                'name' => '',
                'iso2_code' => 'GU',
                'iso3_code' => 'GUM'
            ],
            [
                'name' => '',
                'iso2_code' => 'GW',
                'iso3_code' => 'GNB'
            ],
            [
                'name' => '',
                'iso2_code' => 'GY',
                'iso3_code' => 'GUY'
            ],
            [
                'name' => '',
                'iso2_code' => 'HK',
                'iso3_code' => 'HKG'
            ],
            [
                'name' => '',
                'iso2_code' => 'HM',
                'iso3_code' => 'HMD'
            ],
            [
                'name' => '',
                'iso2_code' => 'HN',
                'iso3_code' => 'HND'
            ],
            [
                'name' => '',
                'iso2_code' => 'HR',
                'iso3_code' => 'HRV'
            ],
            [
                'name' => '',
                'iso2_code' => 'HT',
                'iso3_code' => 'HTI'
            ],
            [
                'name' => '',
                'iso2_code' => 'HU',
                'iso3_code' => 'HUN'
            ],
            [
                'name' => '',
                'iso2_code' => 'ID',
                'iso3_code' => 'IDN'
            ],
            [
                'name' => '',
                'iso2_code' => 'IE',
                'iso3_code' => 'IRL'
            ],
            [
                'name' => '',
                'iso2_code' => 'IL',
                'iso3_code' => 'ISR'
            ],
            [
                'name' => '',
                'iso2_code' => 'IM',
                'iso3_code' => 'IMN'
            ],
            [
                'name' => '',
                'iso2_code' => 'IN',
                'iso3_code' => 'IND'
            ],
            [
                'name' => '',
                'iso2_code' => 'IO',
                'iso3_code' => 'IOT'
            ],
            [
                'name' => '',
                'iso2_code' => 'IQ',
                'iso3_code' => 'IRQ'
            ],
            [
                'name' => '',
                'iso2_code' => 'IR',
                'iso3_code' => 'IRN'
            ],
            [
                'name' => '',
                'iso2_code' => 'IS',
                'iso3_code' => 'ISL'
            ],
            [
                'name' => '',
                'iso2_code' => 'IT',
                'iso3_code' => 'ITA'
            ],
            [
                'name' => '',
                'iso2_code' => 'JE',
                'iso3_code' => 'JEY'
            ],
            [
                'name' => '',
                'iso2_code' => 'JM',
                'iso3_code' => 'JAM'
            ],
            [
                'name' => '',
                'iso2_code' => 'JO',
                'iso3_code' => 'JOR'
            ],
            [
                'name' => '',
                'iso2_code' => 'JP',
                'iso3_code' => 'JPN'
            ],
            [
                'name' => '',
                'iso2_code' => 'KE',
                'iso3_code' => 'KEN'
            ],
            [
                'name' => '',
                'iso2_code' => 'KG',
                'iso3_code' => 'KGZ'
            ],
            [
                'name' => '',
                'iso2_code' => 'KH',
                'iso3_code' => 'KHM'
            ],
            [
                'name' => '',
                'iso2_code' => 'KI',
                'iso3_code' => 'KIR'
            ],
            [
                'name' => '',
                'iso2_code' => 'KM',
                'iso3_code' => 'COM'
            ],
            [
                'name' => '',
                'iso2_code' => 'KN',
                'iso3_code' => 'KNA'
            ],
            [
                'name' => '',
                'iso2_code' => 'KP',
                'iso3_code' => 'PRK'
            ],
            [
                'name' => '',
                'iso2_code' => 'KR',
                'iso3_code' => 'KOR'
            ],
            [
                'name' => '',
                'iso2_code' => 'KW',
                'iso3_code' => 'KWT'
            ],
            [
                'name' => '',
                'iso2_code' => 'KY',
                'iso3_code' => 'CYM'
            ],
            [
                'name' => '',
                'iso2_code' => 'KZ',
                'iso3_code' => 'KAZ'
            ],
            [
                'name' => '',
                'iso2_code' => 'LA',
                'iso3_code' => 'LAO'
            ],
            [
                'name' => '',
                'iso2_code' => 'LB',
                'iso3_code' => 'LBN'
            ],
            [
                'name' => '',
                'iso2_code' => 'LC',
                'iso3_code' => 'LCA'
            ],
            [
                'name' => '',
                'iso2_code' => 'LI',
                'iso3_code' => 'LIE'
            ],
            [
                'name' => '',
                'iso2_code' => 'LK',
                'iso3_code' => 'LKA'
            ],
            [
                'name' => '',
                'iso2_code' => 'LR',
                'iso3_code' => 'LBR'
            ],
            [
                'name' => '',
                'iso2_code' => 'LS',
                'iso3_code' => 'LSO'
            ],
            [
                'name' => '',
                'iso2_code' => 'LT',
                'iso3_code' => 'LTU'
            ],
            [
                'name' => '',
                'iso2_code' => 'LU',
                'iso3_code' => 'LUX'
            ],
            [
                'name' => '',
                'iso2_code' => 'LV',
                'iso3_code' => 'LVA'
            ],
            [
                'name' => '',
                'iso2_code' => 'LY',
                'iso3_code' => 'LBY'
            ],
            [
                'name' => '',
                'iso2_code' => 'MA',
                'iso3_code' => 'MAR'
            ],
            [
                'name' => '',
                'iso2_code' => 'MC',
                'iso3_code' => 'MCO'
            ],
            [
                'name' => '',
                'iso2_code' => 'MD',
                'iso3_code' => 'MDA'
            ],
            [
                'name' => '',
                'iso2_code' => 'ME',
                'iso3_code' => 'MNE'
            ],
            [
                'name' => '',
                'iso2_code' => 'MF',
                'iso3_code' => 'MAF'
            ],
            [
                'name' => '',
                'iso2_code' => 'MG',
                'iso3_code' => 'MDG'
            ],
            [
                'name' => '',
                'iso2_code' => 'MH',
                'iso3_code' => 'MHL'
            ],
            [
                'name' => '',
                'iso2_code' => 'MK',
                'iso3_code' => 'MKD'
            ],
            [
                'name' => '',
                'iso2_code' => 'ML',
                'iso3_code' => 'MLI'
            ],
            [
                'name' => '',
                'iso2_code' => 'MM',
                'iso3_code' => 'MMR'
            ],
            [
                'name' => '',
                'iso2_code' => 'MN',
                'iso3_code' => 'MNG'
            ],
            [
                'name' => '',
                'iso2_code' => 'MO',
                'iso3_code' => 'MAC'
            ],
            [
                'name' => '',
                'iso2_code' => 'MP',
                'iso3_code' => 'MNP'
            ],
            [
                'name' => '',
                'iso2_code' => 'MQ',
                'iso3_code' => 'MTQ'
            ],
            [
                'name' => '',
                'iso2_code' => 'MR',
                'iso3_code' => 'MRT'
            ],
            [
                'name' => '',
                'iso2_code' => 'MS',
                'iso3_code' => 'MSR'
            ],
            [
                'name' => '',
                'iso2_code' => 'MT',
                'iso3_code' => 'MLT'
            ],
            [
                'name' => '',
                'iso2_code' => 'MU',
                'iso3_code' => 'MUS'
            ],
            [
                'name' => '',
                'iso2_code' => 'MV',
                'iso3_code' => 'MDV'
            ],
            [
                'name' => '',
                'iso2_code' => 'MW',
                'iso3_code' => 'MWI'
            ],
            [
                'name' => '',
                'iso2_code' => 'MX',
                'iso3_code' => 'MEX'
            ],
            [
                'name' => '',
                'iso2_code' => 'MY',
                'iso3_code' => 'MYS'
            ],
            [
                'name' => '',
                'iso2_code' => 'MZ',
                'iso3_code' => 'MOZ'
            ],
            [
                'name' => '',
                'iso2_code' => 'NA',
                'iso3_code' => 'NAM'
            ],
            [
                'name' => '',
                'iso2_code' => 'NC',
                'iso3_code' => 'NCL'
            ],
            [
                'name' => '',
                'iso2_code' => 'NE',
                'iso3_code' => 'NER'
            ],
            [
                'name' => '',
                'iso2_code' => 'NF',
                'iso3_code' => 'NFK'
            ],
            [
                'name' => '',
                'iso2_code' => 'NG',
                'iso3_code' => 'NGA'
            ],
            [
                'name' => '',
                'iso2_code' => 'NI',
                'iso3_code' => 'NIC'
            ],
            [
                'name' => '',
                'iso2_code' => 'NL',
                'iso3_code' => 'NLD'
            ],
            [
                'name' => '',
                'iso2_code' => 'NO',
                'iso3_code' => 'NOR'
            ],
            [
                'name' => '',
                'iso2_code' => 'NP',
                'iso3_code' => 'NPL'
            ],
            [
                'name' => '',
                'iso2_code' => 'NR',
                'iso3_code' => 'NRU'
            ],
            [
                'name' => '',
                'iso2_code' => 'NU',
                'iso3_code' => 'NIU'
            ],
            [
                'name' => '',
                'iso2_code' => 'NZ',
                'iso3_code' => 'NZL'
            ],
            [
                'name' => '',
                'iso2_code' => 'OM',
                'iso3_code' => 'OMN'
            ],
            [
                'name' => '',
                'iso2_code' => 'PA',
                'iso3_code' => 'PAN'
            ],
            [
                'name' => '',
                'iso2_code' => 'PE',
                'iso3_code' => 'PER'
            ],
            [
                'name' => '',
                'iso2_code' => 'PF',
                'iso3_code' => 'PYF'
            ],
            [
                'name' => '',
                'iso2_code' => 'PG',
                'iso3_code' => 'PNG'
            ],
            [
                'name' => '',
                'iso2_code' => 'PH',
                'iso3_code' => 'PHL'
            ],
            [
                'name' => '',
                'iso2_code' => 'PK',
                'iso3_code' => 'PAK'
            ],
            [
                'name' => '',
                'iso2_code' => 'PL',
                'iso3_code' => 'POL'
            ],
            [
                'name' => '',
                'iso2_code' => 'PM',
                'iso3_code' => 'SPM'
            ],
            [
                'name' => '',
                'iso2_code' => 'PN',
                'iso3_code' => 'PCN'
            ],
            [
                'name' => '',
                'iso2_code' => 'PR',
                'iso3_code' => 'PRI'
            ],
            [
                'name' => '',
                'iso2_code' => 'PS',
                'iso3_code' => 'PSE'
            ],
            [
                'name' => '',
                'iso2_code' => 'PT',
                'iso3_code' => 'PRT'
            ],
            [
                'name' => '',
                'iso2_code' => 'PW',
                'iso3_code' => 'PLW'
            ],
            [
                'name' => '',
                'iso2_code' => 'PY',
                'iso3_code' => 'PRY'
            ],
            [
                'name' => '',
                'iso2_code' => 'QA',
                'iso3_code' => 'QAT'
            ],
            [
                'name' => '',
                'iso2_code' => 'RE',
                'iso3_code' => 'REU'
            ],
            [
                'name' => '',
                'iso2_code' => 'RO',
                'iso3_code' => 'ROU'
            ],
            [
                'name' => '',
                'iso2_code' => 'RS',
                'iso3_code' => 'SRB'
            ],
            [
                'name' => '',
                'iso2_code' => 'RU',
                'iso3_code' => 'RUS'
            ],
            [
                'name' => '',
                'iso2_code' => 'RW',
                'iso3_code' => 'RWA'
            ],
            [
                'name' => '',
                'iso2_code' => 'SA',
                'iso3_code' => 'SAU'
            ],
            [
                'name' => '',
                'iso2_code' => 'SB',
                'iso3_code' => 'SLB'
            ],
            [
                'name' => '',
                'iso2_code' => 'SC',
                'iso3_code' => 'SYC'
            ],
            [
                'name' => '',
                'iso2_code' => 'SD',
                'iso3_code' => 'SDN'
            ],
            [
                'name' => '',
                'iso2_code' => 'SE',
                'iso3_code' => 'SWE'
            ],
            [
                'name' => '',
                'iso2_code' => 'SG',
                'iso3_code' => 'SGP'
            ],
            [
                'name' => '',
                'iso2_code' => 'SH',
                'iso3_code' => 'SHN'
            ],
            [
                'name' => '',
                'iso2_code' => 'SI',
                'iso3_code' => 'SVN'
            ],
            [
                'name' => '',
                'iso2_code' => 'SJ',
                'iso3_code' => 'SJM'
            ],
            [
                'name' => '',
                'iso2_code' => 'SK',
                'iso3_code' => 'SVK'
            ],
            [
                'name' => '',
                'iso2_code' => 'SL',
                'iso3_code' => 'SLE'
            ],
            [
                'name' => '',
                'iso2_code' => 'SM',
                'iso3_code' => 'SMR'
            ],
            [
                'name' => '',
                'iso2_code' => 'SN',
                'iso3_code' => 'SEN'
            ],
            [
                'name' => '',
                'iso2_code' => 'SO',
                'iso3_code' => 'SOM'
            ],
            [
                'name' => '',
                'iso2_code' => 'SR',
                'iso3_code' => 'SUR'
            ],
            [
                'name' => '',
                'iso2_code' => 'ST',
                'iso3_code' => 'STP'
            ],
            [
                'name' => '',
                'iso2_code' => 'SV',
                'iso3_code' => 'SLV'
            ],
            [
                'name' => '',
                'iso2_code' => 'SY',
                'iso3_code' => 'SYR'
            ],
            [
                'name' => '',
                'iso2_code' => 'SZ',
                'iso3_code' => 'SWZ'
            ],
            [
                'name' => '',
                'iso2_code' => 'TC',
                'iso3_code' => 'TCA'
            ],
            [
                'name' => '',
                'iso2_code' => 'TD',
                'iso3_code' => 'TCD'
            ],
            [
                'name' => '',
                'iso2_code' => 'TF',
                'iso3_code' => 'ATF'
            ],
            [
                'name' => '',
                'iso2_code' => 'TG',
                'iso3_code' => 'TGO'
            ],
            [
                'name' => '',
                'iso2_code' => 'TH',
                'iso3_code' => 'THA'
            ],
            [
                'name' => '',
                'iso2_code' => 'TJ',
                'iso3_code' => 'TJK'
            ],
            [
                'name' => '',
                'iso2_code' => 'TK',
                'iso3_code' => 'TKL'
            ],
            [
                'name' => '',
                'iso2_code' => 'TL',
                'iso3_code' => 'TLS'
            ],
            [
                'name' => '',
                'iso2_code' => 'TM',
                'iso3_code' => 'TKM'
            ],
            [
                'name' => '',
                'iso2_code' => 'TN',
                'iso3_code' => 'TUN'
            ],
            [
                'name' => '',
                'iso2_code' => 'TO',
                'iso3_code' => 'TON'
            ],
            [
                'name' => '',
                'iso2_code' => 'TR',
                'iso3_code' => 'TUR'
            ],
            [
                'name' => '',
                'iso2_code' => 'TT',
                'iso3_code' => 'TTO'
            ],
            [
                'name' => '',
                'iso2_code' => 'TV',
                'iso3_code' => 'TUV'
            ],
            [
                'name' => '',
                'iso2_code' => 'TW',
                'iso3_code' => 'TWN'
            ],
            [
                'name' => '',
                'iso2_code' => 'TZ',
                'iso3_code' => 'TZA'
            ],
            [
                'name' => '',
                'iso2_code' => 'UA',
                'iso3_code' => 'UKR'
            ],
            [
                'name' => '',
                'iso2_code' => 'UG',
                'iso3_code' => 'UGA'
            ],
            [
                'name' => '',
                'iso2_code' => 'UM',
                'iso3_code' => 'UMI'
            ],
            [
                'name' => '',
                'iso2_code' => 'US',
                'iso3_code' => 'USA'
            ],
            [
                'name' => '',
                'iso2_code' => 'UY',
                'iso3_code' => 'URY'
            ],
            [
                'name' => '',
                'iso2_code' => 'UZ',
                'iso3_code' => 'UZB'
            ],
            [
                'name' => '',
                'iso2_code' => 'VA',
                'iso3_code' => 'VAT'
            ],
            [
                'name' => '',
                'iso2_code' => 'VC',
                'iso3_code' => 'VCT'
            ],
            [
                'name' => '',
                'iso2_code' => 'VE',
                'iso3_code' => 'VEN'
            ],
            [
                'name' => '',
                'iso2_code' => 'VG',
                'iso3_code' => 'VGB'
            ],
            [
                'name' => '',
                'iso2_code' => 'VI',
                'iso3_code' => 'VIR'
            ],
            [
                'name' => '',
                'iso2_code' => 'VN',
                'iso3_code' => 'VNM'
            ],
            [
                'name' => '',
                'iso2_code' => 'VU',
                'iso3_code' => 'VUT'
            ],
            [
                'name' => '',
                'iso2_code' => 'WF',
                'iso3_code' => 'WLF'
            ],
            [
                'name' => '',
                'iso2_code' => 'WS',
                'iso3_code' => 'WSM'
            ],
            [
                'name' => '',
                'iso2_code' => 'YE',
                'iso3_code' => 'YEM'
            ],
            [
                'name' => '',
                'iso2_code' => 'YT',
                'iso3_code' => 'MYT'
            ],
            [
                'name' => '',
                'iso2_code' => 'ZA',
                'iso3_code' => 'ZAF'
            ],
            [
                'name' => '',
                'iso2_code' => 'ZM',
                'iso3_code' => 'ZMB'
            ],
            [
                'name' => '',
                'iso2_code' => 'ZW',
                'iso3_code' => 'ZWE'
            ]
        ]);
    }
}
