<?php
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'ewduwpmSettings' ) ) {
/**
 * Class to handle configurable settings for Ultimate WP Mail
 * @since 1.0.0
 */
class ewduwpmSettings {

	public $action_type_options = array();

	public $include_options = array();

	public $cpt_options = array();

	public $email_options = array();

	public $count_options = array();

	public $country_phone_array = array(
		// 'AD' => array( 'name' => 'ANDORRA', 'code' => '376' ),
		// 'AE' => array( 'name' => 'UNITED ARAB EMIRATES', 'code' => '971' ),
		// 'AF' => array( 'name' => 'AFGHANISTAN', 'code' => '93' ),
		// 'AG' => array( 'name' => 'ANTIGUA AND BARBUDA', 'code' => '1268' ),
		// 'AI' => array( 'name' => 'ANGUILLA', 'code' => '1264' ),
		// 'AL' => array( 'name' => 'ALBANIA', 'code' => '355' ),
		// 'AM' => array( 'name' => 'ARMENIA', 'code' => '374' ),
		// 'AN' => array( 'name' => 'NETHERLANDS ANTILLES', 'code' => '599' ),
		// 'AO' => array( 'name' => 'ANGOLA', 'code' => '244' ),
		// 'AQ' => array( 'name' => 'ANTARCTICA', 'code' => '672' ),
		'AR' => array( 'name' => 'ARGENTINA', 'code' => '54' ),
		// 'AS' => array( 'name' => 'AMERICAN SAMOA', 'code' => '1684' ),
		'AT' => array( 'name' => 'AUSTRIA', 'code' => '43' ),
		'AU' => array( 'name' => 'AUSTRALIA', 'code' => '61' ),
		// 'AW' => array( 'name' => 'ARUBA', 'code' => '297' ),
		// 'AZ' => array( 'name' => 'AZERBAIJAN', 'code' => '994' ),
		// 'BA' => array( 'name' => 'BOSNIA AND HERZEGOVINA', 'code' => '387' ),
		// 'BB' => array( 'name' => 'BARBADOS', 'code' => '1246' ),
		// 'BD' => array( 'name' => 'BANGLADESH', 'code' => '880' ),
		'BE' => array( 'name' => 'BELGIUM', 'code' => '32' ),
		// 'BF' => array( 'name' => 'BURKINA FASO', 'code' => '226' ),
		'BG' => array( 'name' => 'BULGARIA', 'code' => '359' ),
		// 'BH' => array( 'name' => 'BAHRAIN', 'code' => '973' ),
		// 'BI' => array( 'name' => 'BURUNDI', 'code' => '257' ),
		// 'BJ' => array( 'name' => 'BENIN', 'code' => '229' ),
		// 'BL' => array( 'name' => 'SAINT BARTHELEMY', 'code' => '590' ),
		// 'BM' => array( 'name' => 'BERMUDA', 'code' => '1441' ),
		// 'BN' => array( 'name' => 'BRUNEI DARUSSALAM', 'code' => '673' ),
		// 'BO' => array( 'name' => 'BOLIVIA', 'code' => '591' ),
		'BR' => array( 'name' => 'BRAZIL', 'code' => '55' ),
		// 'BS' => array( 'name' => 'BAHAMAS', 'code' => '1242' ),
		// 'BT' => array( 'name' => 'BHUTAN', 'code' => '975' ),
		// 'BW' => array( 'name' => 'BOTSWANA', 'code' => '267' ),
		// 'BY' => array( 'name' => 'BELARUS', 'code' => '375' ),
		// 'BZ' => array( 'name' => 'BELIZE', 'code' => '501' ),
		'CA' => array( 'name' => 'CANADA', 'code' => '1' ),
		// 'CC' => array( 'name' => 'COCOS (KEELING) ISLANDS', 'code' => '61' ),
		// 'CD' => array( 'name' => 'CONGO, THE DEMOCRATIC REPUBLIC OF THE', 'code' => '243' ),
		// 'CF' => array( 'name' => 'CENTRAL AFRICAN REPUBLIC', 'code' => '236' ),
		// 'CG' => array( 'name' => 'CONGO', 'code' => '242' ),
		'CH' => array( 'name' => 'SWITZERLAND', 'code' => '41' ),
		// 'CI' => array( 'name' => 'COTE D IVOIRE', 'code' => '225' ),
		// 'CK' => array( 'name' => 'COOK ISLANDS', 'code' => '682' ),
		// 'CL' => array( 'name' => 'CHILE', 'code' => '56' ),
		// 'CM' => array( 'name' => 'CAMEROON', 'code' => '237' ),
		'CN' => array( 'name' => 'CHINA', 'code' => '86' ),
		// 'CO' => array( 'name' => 'COLOMBIA', 'code' => '57' ),
		// 'CR' => array( 'name' => 'COSTA RICA', 'code' => '506' ),
		// 'CU' => array( 'name' => 'CUBA', 'code' => '53' ),
		// 'CV' => array( 'name' => 'CAPE VERDE', 'code' => '238' ),
		// 'CX' => array( 'name' => 'CHRISTMAS ISLAND', 'code' => '61' ),
		// 'CY' => array( 'name' => 'CYPRUS', 'code' => '357' ),
		'CZ' => array( 'name' => 'CZECH REPUBLIC', 'code' => '420' ),
		'DE' => array( 'name' => 'GERMANY', 'code' => '49' ),
		// 'DJ' => array( 'name' => 'DJIBOUTI', 'code' => '253' ),
		'DK' => array( 'name' => 'DENMARK', 'code' => '45' ),
		// 'DM' => array( 'name' => 'DOMINICA', 'code' => '1767' ),
		// 'DO' => array( 'name' => 'DOMINICAN REPUBLIC', 'code' => '1809' ),
		// 'DZ' => array( 'name' => 'ALGERIA', 'code' => '213' ),
		// 'EC' => array( 'name' => 'ECUADOR', 'code' => '593' ),
		'EE' => array( 'name' => 'ESTONIA', 'code' => '372' ),
		// 'EG' => array( 'name' => 'EGYPT', 'code' => '20' ),
		// 'ER' => array( 'name' => 'ERITREA', 'code' => '291' ),
		'ES' => array( 'name' => 'SPAIN', 'code' => '34' ),
		// 'ET' => array( 'name' => 'ETHIOPIA', 'code' => '251' ),
		'FI' => array( 'name' => 'FINLAND', 'code' => '358' ),
		// 'FJ' => array( 'name' => 'FIJI', 'code' => '679' ),
		// 'FK' => array( 'name' => 'FALKLAND ISLANDS (MALVINAS)', 'code' => '500' ),
		// 'FM' => array( 'name' => 'MICRONESIA, FEDERATED STATES OF', 'code' => '691' ),
		// 'FO' => array( 'name' => 'FAROE ISLANDS', 'code' => '298' ),
		'FR' => array( 'name' => 'FRANCE', 'code' => '33' ),
		// 'GA' => array( 'name' => 'GABON', 'code' => '241' ),
		'GB' => array( 'name' => 'UNITED KINGDOM', 'code' => '44' ),
		// 'GD' => array( 'name' => 'GRENADA', 'code' => '1473' ),
		// 'GE' => array( 'name' => 'GEORGIA', 'code' => '995' ),
		// 'GH' => array( 'name' => 'GHANA', 'code' => '233' ),
		// 'GI' => array( 'name' => 'GIBRALTAR', 'code' => '350' ),
		'GL' => array( 'name' => 'GREENLAND', 'code' => '299' ),
		// 'GM' => array( 'name' => 'GAMBIA', 'code' => '220' ),
		// 'GN' => array( 'name' => 'GUINEA', 'code' => '224' ),
		// 'GQ' => array( 'name' => 'EQUATORIAL GUINEA', 'code' => '240' ),
		'GR' => array( 'name' => 'GREECE', 'code' => '30' ),
		// 'GT' => array( 'name' => 'GUATEMALA', 'code' => '502' ),
		// 'GU' => array( 'name' => 'GUAM', 'code' => '1671' ),
		// 'GW' => array( 'name' => 'GUINEA-BISSAU', 'code' => '245' ),
		// 'GY' => array( 'name' => 'GUYANA', 'code' => '592' ),
		'HK' => array( 'name' => 'HONG KONG', 'code' => '852' ),
		// 'HN' => array( 'name' => 'HONDURAS', 'code' => '504' ),
		'HR' => array( 'name' => 'CROATIA', 'code' => '385' ),
		// 'HT' => array( 'name' => 'HAITI', 'code' => '509' ),
		'HU' => array( 'name' => 'HUNGARY', 'code' => '36' ),
		'ID' => array( 'name' => 'INDONESIA', 'code' => '62' ),
		'IE' => array( 'name' => 'IRELAND', 'code' => '353' ),
		'IL' => array( 'name' => 'ISRAEL', 'code' => '972' ),
		// 'IM' => array( 'name' => 'ISLE OF MAN', 'code' => '44' ),
		'IN' => array( 'name' => 'INDIA', 'code' => '91' ),
		// 'IQ' => array( 'name' => 'IRAQ', 'code' => '964' ),
		// 'IR' => array( 'name' => 'IRAN, ISLAMIC REPUBLIC OF', 'code' => '98' ),
		'IS' => array( 'name' => 'ICELAND', 'code' => '354' ),
		'IT' => array( 'name' => 'ITALY', 'code' => '39' ),
		// 'JM' => array( 'name' => 'JAMAICA', 'code' => '1876' ),
		// 'JO' => array( 'name' => 'JORDAN', 'code' => '962' ),
		'JP' => array( 'name' => 'JAPAN', 'code' => '81' ),
		// 'KE' => array( 'name' => 'KENYA', 'code' => '254' ),
		// 'KG' => array( 'name' => 'KYRGYZSTAN', 'code' => '996' ),
		// 'KH' => array( 'name' => 'CAMBODIA', 'code' => '855' ),
		// 'KI' => array( 'name' => 'KIRIBATI', 'code' => '686' ),
		// 'KM' => array( 'name' => 'COMOROS', 'code' => '269' ),
		// 'KN' => array( 'name' => 'SAINT KITTS AND NEVIS', 'code' => '1869' ),
		// 'KP' => array( 'name' => 'KOREA DEMOCRATIC PEOPLES REPUBLIC OF', 'code' => '850' ),
		'KR' => array( 'name' => 'KOREA REPUBLIC OF', 'code' => '82' ),
		// 'KW' => array( 'name' => 'KUWAIT', 'code' => '965' ),
		// 'KY' => array( 'name' => 'CAYMAN ISLANDS', 'code' => '1345' ),
		// 'KZ' => array( 'name' => 'KAZAKSTAN', 'code' => '7' ),
		// 'LA' => array( 'name' => 'LAO PEOPLES DEMOCRATIC REPUBLIC', 'code' => '856' ),
		// 'LB' => array( 'name' => 'LEBANON', 'code' => '961' ),
		// 'LC' => array( 'name' => 'SAINT LUCIA', 'code' => '1758' ),
		'LI' => array( 'name' => 'LIECHTENSTEIN', 'code' => '423' ),
		// 'LK' => array( 'name' => 'SRI LANKA', 'code' => '94' ),
		// 'LR' => array( 'name' => 'LIBERIA', 'code' => '231' ),
		// 'LS' => array( 'name' => 'LESOTHO', 'code' => '266' ),
		'LT' => array( 'name' => 'LITHUANIA', 'code' => '370' ),
		'LU' => array( 'name' => 'LUXEMBOURG', 'code' => '352' ),
		'LV' => array( 'name' => 'LATVIA', 'code' => '371' ),
		// 'LY' => array( 'name' => 'LIBYAN ARAB JAMAHIRIYA', 'code' => '218' ),
		// 'MA' => array( 'name' => 'MOROCCO', 'code' => '212' ),
		// 'MC' => array( 'name' => 'MONACO', 'code' => '377' ),
		// 'MD' => array( 'name' => 'MOLDOVA, REPUBLIC OF', 'code' => '373' ),
		'ME' => array( 'name' => 'MONTENEGRO', 'code' => '382' ),
		// 'MF' => array( 'name' => 'SAINT MARTIN', 'code' => '1599' ),
		// 'MG' => array( 'name' => 'MADAGASCAR', 'code' => '261' ),
		// 'MH' => array( 'name' => 'MARSHALL ISLANDS', 'code' => '692' ),
		// 'MK' => array( 'name' => 'MACEDONIA, THE FORMER YUGOSLAV REPUBLIC OF', 'code' => '389' ),
		// 'ML' => array( 'name' => 'MALI', 'code' => '223' ),
		// 'MM' => array( 'name' => 'MYANMAR', 'code' => '95' ),
		// 'MN' => array( 'name' => 'MONGOLIA', 'code' => '976' ),
		// 'MO' => array( 'name' => 'MACAU', 'code' => '853' ),
		// 'MP' => array( 'name' => 'NORTHERN MARIANA ISLANDS', 'code' => '1670' ),
		// 'MR' => array( 'name' => 'MAURITANIA', 'code' => '222' ),
		// 'MS' => array( 'name' => 'MONTSERRAT', 'code' => '1664' ),
		// 'MT' => array( 'name' => 'MALTA', 'code' => '356' ),
		// 'MU' => array( 'name' => 'MAURITIUS', 'code' => '230' ),
		// 'MV' => array( 'name' => 'MALDIVES', 'code' => '960' ),
		// 'MW' => array( 'name' => 'MALAWI', 'code' => '265' ),
		'MX' => array( 'name' => 'MEXICO', 'code' => '52' ),
		'MY' => array( 'name' => 'MALAYSIA', 'code' => '60' ),
		// 'MZ' => array( 'name' => 'MOZAMBIQUE', 'code' => '258' ),
		// 'NA' => array( 'name' => 'NAMIBIA', 'code' => '264' ),
		// 'NC' => array( 'name' => 'NEW CALEDONIA', 'code' => '687' ),
		// 'NE' => array( 'name' => 'NIGER', 'code' => '227' ),
		// 'NG' => array( 'name' => 'NIGERIA', 'code' => '234' ),
		// 'NI' => array( 'name' => 'NICARAGUA', 'code' => '505' ),
		'NL' => array( 'name' => 'NETHERLANDS', 'code' => '31' ),
		'NO' => array( 'name' => 'NORWAY', 'code' => '47' ),
		// 'NP' => array( 'name' => 'NEPAL', 'code' => '977' ),
		// 'NR' => array( 'name' => 'NAURU', 'code' => '674' ),
		// 'NU' => array( 'name' => 'NIUE', 'code' => '683' ),
		'NZ' => array( 'name' => 'NEW ZEALAND', 'code' => '64' ),
		// 'OM' => array( 'name' => 'OMAN', 'code' => '968' ),
		// 'PA' => array( 'name' => 'PANAMA', 'code' => '507' ),
		// 'PE' => array( 'name' => 'PERU', 'code' => '51' ),
		// 'PF' => array( 'name' => 'FRENCH POLYNESIA', 'code' => '689' ),
		// 'PG' => array( 'name' => 'PAPUA NEW GUINEA', 'code' => '675' ),
		// 'PH' => array( 'name' => 'PHILIPPINES', 'code' => '63' ),
		// 'PK' => array( 'name' => 'PAKISTAN', 'code' => '92' ),
		'PL' => array( 'name' => 'POLAND', 'code' => '48' ),
		// 'PM' => array( 'name' => 'SAINT PIERRE AND MIQUELON', 'code' => '508' ),
		// 'PN' => array( 'name' => 'PITCAIRN', 'code' => '870' ),
		'PR' => array( 'name' => 'PUERTO RICO', 'code' => '1' ),
		'PT' => array( 'name' => 'PORTUGAL', 'code' => '351' ),
		// 'PW' => array( 'name' => 'PALAU', 'code' => '680' ),
		// 'PY' => array( 'name' => 'PARAGUAY', 'code' => '595' ),
		// 'QA' => array( 'name' => 'QATAR', 'code' => '974' ),
		'RO' => array( 'name' => 'ROMANIA', 'code' => '40' ),
		'RS' => array( 'name' => 'SERBIA', 'code' => '381' ),
		'RU' => array( 'name' => 'RUSSIAN FEDERATION', 'code' => '7' ),
		// 'RW' => array( 'name' => 'RWANDA', 'code' => '250' ),
		// 'SA' => array( 'name' => 'SAUDI ARABIA', 'code' => '966' ),
		// 'SB' => array( 'name' => 'SOLOMON ISLANDS', 'code' => '677' ),
		// 'SC' => array( 'name' => 'SEYCHELLES', 'code' => '248' ),
		// 'SD' => array( 'name' => 'SUDAN', 'code' => '249' ),
		'SE' => array( 'name' => 'SWEDEN', 'code' => '46' ),
		'SG' => array( 'name' => 'SINGAPORE', 'code' => '65' ),
		// 'SH' => array( 'name' => 'SAINT HELENA', 'code' => '290' ),
		'SI' => array( 'name' => 'SLOVENIA', 'code' => '386' ),
		'SK' => array( 'name' => 'SLOVAKIA', 'code' => '421' ),
		// 'SL' => array( 'name' => 'SIERRA LEONE', 'code' => '232' ),
		// 'SM' => array( 'name' => 'SAN MARINO', 'code' => '378' ),
		// 'SN' => array( 'name' => 'SENEGAL', 'code' => '221' ),
		// 'SO' => array( 'name' => 'SOMALIA', 'code' => '252' ),
		// 'SR' => array( 'name' => 'SURINAME', 'code' => '597' ),
		// 'ST' => array( 'name' => 'SAO TOME AND PRINCIPE', 'code' => '239' ),
		// 'SV' => array( 'name' => 'EL SALVADOR', 'code' => '503' ),
		// 'SY' => array( 'name' => 'SYRIAN ARAB REPUBLIC', 'code' => '963' ),
		// 'SZ' => array( 'name' => 'SWAZILAND', 'code' => '268' ),
		// 'TC' => array( 'name' => 'TURKS AND CAICOS ISLANDS', 'code' => '1649' ),
		// 'TD' => array( 'name' => 'CHAD', 'code' => '235' ),
		// 'TG' => array( 'name' => 'TOGO', 'code' => '228' ),
		'TH' => array( 'name' => 'THAILAND', 'code' => '66' ),
		// 'TJ' => array( 'name' => 'TAJIKISTAN', 'code' => '992' ),
		// 'TK' => array( 'name' => 'TOKELAU', 'code' => '690' ),
		// 'TL' => array( 'name' => 'TIMOR-LESTE', 'code' => '670' ),
		// 'TM' => array( 'name' => 'TURKMENISTAN', 'code' => '993' ),
		// 'TN' => array( 'name' => 'TUNISIA', 'code' => '216' ),
		// 'TO' => array( 'name' => 'TONGA', 'code' => '676' ),
		// 'TR' => array( 'name' => 'TURKEY', 'code' => '90' ),
		// 'TT' => array( 'name' => 'TRINIDAD AND TOBAGO', 'code' => '1868' ),
		// 'TV' => array( 'name' => 'TUVALU', 'code' => '688' ),
		'TW' => array( 'name' => 'TAIWAN', 'code' => '886' ),
		// 'TZ' => array( 'name' => 'TANZANIA, UNITED REPUBLIC OF', 'code' => '255' ),
		'UA' => array( 'name' => 'UKRAINE', 'code' => '380' ),
		// 'UG' => array( 'name' => 'UGANDA', 'code' => '256' ),
		'US' => array( 'name' => 'UNITED STATES', 'code' => '1' ),
		'UY' => array( 'name' => 'URUGUAY', 'code' => '598' ),
		// 'UZ' => array( 'name' => 'UZBEKISTAN', 'code' => '998' ),
		// 'VA' => array( 'name' => 'HOLY SEE (VATICAN CITY STATE)', 'code' => '39' ),
		// 'VC' => array( 'name' => 'SAINT VINCENT AND THE GRENADINES', 'code' => '1784' ),
		// 'VE' => array( 'name' => 'VENEZUELA', 'code' => '58' ),
		// 'VG' => array( 'name' => 'VIRGIN ISLANDS, BRITISH', 'code' => '1284' ),
		// 'VI' => array( 'name' => 'VIRGIN ISLANDS, U.S.', 'code' => '1340' ),
		'VN' => array( 'name' => 'VIETNAM', 'code' => '84' ),
		// 'VU' => array( 'name' => 'VANUATU', 'code' => '678' ),
		// 'WF' => array( 'name' => 'WALLIS AND FUTUNA', 'code' => '681' ),
		// 'WS' => array( 'name' => 'SAMOA', 'code' => '685' ),
		// 'XK' => array( 'name' => 'KOSOVO', 'code' => '381' ),
		// 'YE' => array( 'name' => 'YEMEN', 'code' => '967' ),
		// 'YT' => array( 'name' => 'MAYOTTE', 'code' => '262' ),
		'ZA' => array( 'name' => 'SOUTH AFRICA', 'code' => '27' ),
		// 'ZM' => array( 'name' => 'ZAMBIA', 'code' => '260' ),
		// 'ZW' => array( 'name' => 'ZIMBABWE', 'code' => '263' )
	);

	/**
	 * Default values for settings
	 * @since 1.0.0
	 */
	public $defaults = array();

	/**
	 * Stored values for settings
	 * @since 1.0.0
	 */
	public $settings = array();

	public function __construct() {

		add_action( 'init', array( $this, 'set_defaults' ) );

		add_action( 'init', array( $this, 'set_field_options' ) );

		add_action( 'init', array( $this, 'load_settings_panel' ) );
	}

	/**
	 * Load the plugin's default settings
	 * @since 1.0.0
	 */
	public function set_defaults() {

		$this->defaults = array(

			'access-role'				=> __( 'manage_options', 'ultimate-wp-mail' ),

			'send-actions'				=> json_encode( array() ),
			'admin-email'				=> get_option( 'admin_email' ),
			'ultimate-purchase-email'	=> get_option( 'admin_email' ),

			'label-unsubscribe'			=> __( 'Unsubscribe', 'ultimate-wp-mail' ),

			'schedule-check-delay' 		=> 5,

			'maximum-email-logs'		=> 200,

			// Email template sent to an admin when a new booking request is made
			'notify-admin-subject'			=> _x( 'Email Sending Failed', 'Default email subject for admin email failure notifications', 'ultimate-wp-mail' ),
			'notify-admin-message'			=> _x( 'An email has failed to send with the following details:

Date: {date}
Status: {status}
To: {recipient}
Subject: {subject}
Message: {message}

&nbsp;

<em>This message was sent by {site_link}.</em>',
				'Default email sent to the admin when an email fails to send. The tags in {brackets} will be replaced by the appropriate content and should be left in place. HTML is allowed, but be aware that many email clients do not handle HTML very well.',
				'ultimate-wp-mail'
			),
		);

		$this->defaults = apply_filters( 'ewd_uwpm_defaults', $this->defaults, $this );
	}

	/**
	 * Put all of the available possible select options into key => value arrays
	 * @since 1.0.0
	 */
	public function set_field_options() {
		global $ewd_uwpm_controller;

		$this->action_type_options = array(
			'User Events'			=> array(
				'user_registers'					=> __( 'On Registration', 'ultimate-wp-mail' ),
				'user_profile_updated'				=> __( 'When Profile Updated', 'ultimate-wp-mail' ),
				'user_role_changed'					=> __( 'When Role Changes', 'ultimate-wp-mail' ),
				'user_password_reset'				=> __( 'Password is Reset', 'ultimate-wp-mail' ),
				'user_x_time_since_login'			=> __( 'X Time Since Last Login', 'ultimate-wp-mail' ),
			),
			'Site Events'			=> array(
				'post_published_interest'			=> __( 'Post Published in Interest', 'ultimate-wp-mail' ),
				'new_comment_on_post'				=> __( 'New Comment after Commenting', 'ultimate-wp-mail' ),
			),
			'Custom Post Events'	=> array(
				'cpt_created'						=> __( 'Custom Post Type Created', 'ultimate-wp-mail' ),
				'cpt_updated'						=> __( 'Custom Post Type Updated', 'ultimate-wp-mail' ),
				'cpt_deleted'						=> __( 'Custom Post Type Deleted', 'ultimate-wp-mail' ),
			),
			'WooCommerce Events'	=> array(
				'wc_x_time_since_cart_abandoned'	=> __( 'X Time after Cart Abandoned', 'ultimate-wp-mail' ),
				'wc_x_time_after_purchase'			=> __( 'X Time after Purchase', 'ultimate-wp-mail' ),
				'product_added'						=> __( 'Product Added', 'ultimate-wp-mail' ),
				'product_purchased'					=> __( 'Product Purchased', 'ultimate-wp-mail' ),
				'subscription_created'				=> __( 'Subscription Created', 'ultimate-wp-mail' ),
			),
		);

		$this->include_options = array(
			'any'						=> __( 'Any Product', 'ultimate-wp-mail' ),
			'WooCommerce Categories'	=> array(),
			'Products'					=> array(),
		);

		if ( taxonomy_exists( 'product_cat' ) ) {

			$args = array(
				'hide_empty'     => false, 
				'taxonomy'       => 'product_cat',
				'posts_per_page' => -1, 

				'cache_results'  => false
			);

			$wc_categories = get_terms( $args );
	
			foreach ( $wc_categories as $wc_category ) {
	
				$this->include_options['WooCommerce Categories'][ 'c_' . $wc_category->term_id ] = $wc_category->name;
			}
		}

		if ( post_type_exists( 'product' ) ) {

			$args = array(
				'posts_per_page' => -1, 
				'post_type'      => 'product', 
				'orderby'        => 'title', 
				'order'          => 'ASC',
				'cache_results'  => false
			);

			$products = get_posts( $args );

			foreach ( $products as $product ) {
				$this->include_options['Products'][ 'p_' . $product->ID ] = $product->post_title;
			}
		}

		$args = array(
			'public'   => true,
			'_builtin' => false,
		);

		$post_types = get_post_types( $args, 'objects' ); 

		foreach ( $post_types as $post_type ) {

			if ( $post_type->name == 'uwpm_email_log' ) { continue; }

			$this->cpt_options[ $post_type->name ] = $post_type->label;
		}

		$args = array(
			'post_type'     => EWD_UWPM_EMAIL_POST_TYPE,
			'numberposts'   => -1,
			
			'cache_results' => false
		);

		$emails = get_posts( $args );

		foreach ( $emails as $email ) { 

			$this->email_options[ $email->ID ] = $email->post_title;
		}

		for ( $i = 0; $i <= 60; $i++ ) {

			$this->count_options[ $i ] = $i;
		}
	}

	/**
	 * Get a setting's value or fallback to a default if one exists
	 * @since 1.0.0
	 */
	public function get_setting( $setting ) { 

		if ( empty( $this->settings ) ) {
			$this->settings = get_option( 'ewd-uwpm-settings' );
		}
		
		if ( ! empty( $this->settings[ $setting ] ) ) {
			return apply_filters( 'ewd-uwpm-settings-' . $setting, $this->settings[ $setting ] );
		}

		if ( ! empty( $this->defaults[ $setting ] ) ) { 
			return apply_filters( 'ewd-uwpm-settings-' . $setting, $this->defaults[ $setting ] );
		}

		return apply_filters( 'ewd-uwpm-settings-' . $setting, null );
	}

	/**
	 * Set a setting to a particular value
	 * @since 1.0.0
	 */
	public function set_setting( $setting, $value ) {

		$this->settings[ $setting ] = $value;
	}

	/**
	 * Save all settings, to be used with set_setting
	 * @since 1.0.0
	 */
	public function save_settings() {
		
		update_option( 'ewd-uwpm-settings', $this->settings );
	}

	/**
	 * Load the admin settings page
	 * @since 1.0.0
	 * @sa https://github.com/NateWr/simple-admin-pages
	 */
	public function load_settings_panel() {
		
		require_once( EWD_UWPM_PLUGIN_DIR . '/lib/simple-admin-pages/simple-admin-pages.php' );
		$sap = sap_initialize_library(
			$args = array(
				'version'       => '2.6.19',
				'lib_url'       => EWD_UWPM_PLUGIN_URL . '/lib/simple-admin-pages/',
				'theme'			=> 'purple',
			)
		);
		
		$sap->add_page(
			'submenu',
			array(
				'id'            => 'ewd-uwpm-settings',
				'title'         => __( 'Settings', 'ultimate-wp-mail' ),
				'menu_title'    => __( 'Settings', 'ultimate-wp-mail' ),
				'parent_menu'	=> 'edit.php?post_type=uwpm_mail_template',
				'description'   => '',
				'capability'    => $this->get_setting( 'access-role' ),
				'default_tab'   => 'ewd-uwpm-basic-tab',
			)
		);

		$sap->add_section(
			'ewd-uwpm-settings',
			array(
				'id'            => 'ewd-uwpm-basic-tab',
				'title'         => __( 'Basic', 'ultimate-wp-mail' ),
				'is_tab'		=> true,
			)
		);

		$sap->add_section(
			'ewd-uwpm-settings',
			array(
				'id'            => 'ewd-uwpm-general',
				'title'         => __( 'General', 'ultimate-wp-mail' ),
				'tab'	        => 'ewd-uwpm-basic-tab',
			)
		);

		$sap->add_setting(
			'ewd-uwpm-settings',
			'ewd-uwpm-general',
			'textarea',
			array(
				'id'			=> 'custom-css',
				'title'			=> __( 'Custom CSS', 'ultimate-wp-mail' ),
				'description'	=> __( 'You can add custom CSS styles to your appointment booking page in the box above.', 'ultimate-wp-mail' ),			
			)
		);

		$sap->add_setting(
			'ewd-uwpm-settings',
			'ewd-uwpm-general',
			'toggle',
			array(
				'id'			=> 'add-unsubscribe-link',
				'title'			=> __( 'Add Unsubscribe Link', 'ultimate-wp-mail' ),
				'description'	=> __( 'Should an unsubscribe link be added to the bottom of your emails?', 'ultimate-wp-mail' )
			)
		);

		$sap->add_setting(
			'ewd-uwpm-settings',
			'ewd-uwpm-general',
			'text',
			array(
				'id'            => 'unsubscribe-redirect-url',
				'title'         => __( 'Unsubscribe Redirect URL', 'ultimate-wp-mail' ),
				'description'	=> __( 'What URL should someone be redirected to when they unsubscribe?', 'ultimate-wp-mail' ),
				'conditional_on'		=> 'add-unsubscribe-link',
				'conditional_on_value'	=> true
			)
		);

		$sap->add_setting(
			'ewd-uwpm-settings',
			'ewd-uwpm-general',
			'toggle',
			array(
				'id'			=> 'add-subscribe-checkbox',
				'title'			=> __( 'Add Subscribe Checkbox', 'ultimate-wp-mail' ),
				'description'	=> __( 'Should a subscribe checkbox be added to the bottom of WordPress registration forms and the edit profile page? (This can be used to email only those users who specifically sign up for emails)', 'ultimate-wp-mail' )
			)
		);

		$sap->add_setting(
			'ewd-uwpm-settings',
			'ewd-uwpm-general',
			'toggle',
			array(
				'id'			=> 'add-unsubscribe-checkbox',
				'title'			=> __( 'Add Unsubscribe Checkbox', 'ultimate-wp-mail' ),
				'description'	=> __( 'Should an unsubscribe checkbox be added to the bottom of WordPress registration forms and the edit profile page?', 'ultimate-wp-mail' )
			)
		);

		$sap->add_setting(
			'ewd-uwpm-settings',
			'ewd-uwpm-general',
			'toggle',
			array(
				'id'			=> 'track-opens',
				'title'			=> __( 'Track Opens', 'ultimate-wp-mail' ),
				'description'	=> __( 'Should the number of users who open each email be tracked?', 'ultimate-wp-mail' )
			)
		);

		$sap->add_setting(
			'ewd-uwpm-settings',
			'ewd-uwpm-general',
			'toggle',
			array(
				'id'			=> 'track-clicks',
				'title'			=> __( 'Track Clicks', 'ultimate-wp-mail' ),
				'description'	=> __( 'Should the number of clicks and the which links have been clicked be tracked?', 'ultimate-wp-mail' )
			)
		);

		$sap->add_setting(
			'ewd-uwpm-settings',
			'ewd-uwpm-general',
			'toggle',
			array(
				'id'			=> 'woocommerce-integration',
				'title'			=> __( 'WooCommerce Integration', 'ultimate-wp-mail' ),
				'description'	=> __( 'Should automatic lists based on WooCommerce purchases be added to the plugin?', 'ultimate-wp-mail' )
			)
		);

		$sap->add_setting(
			'ewd-uwpm-settings',
			'ewd-uwpm-general',
			'checkbox',
			array(
				'id'            => 'display-interests',
				'title'         => __( 'Listed in Interests', 'ultimate-wp-mail' ),
				'description'   => __( 'What interest options should be displayed by default when using the "Subcribe to Interests" shortcode or widget?', 'ultimate-wp-mail' ), 
				'options'       => array(
					'post_categories'			=> __( 'Post Categories', 'ultimate-wp-mail' ),
					'uwpm_categories'			=> __( 'Ultimate WP Mail Categories', 'ultimate-wp-mail' ),
					'woocommerce_categories'	=> __( 'WooCommerce Categories', 'ultimate-wp-mail' ),
				)
			)
		);

		$sap->add_setting(
			'ewd-uwpm-settings',
			'ewd-uwpm-general',
			'radio',
			array(
				'id'			=> 'display-post-interests',
				'title'			=> __( 'Display Post Interests', 'ultimate-wp-mail' ),
				'description'	=> __( 'Should an interests sign-up box be added to all posts, with the specific categories of that post as options?<br/>NOTE: You need to make sure at least one box is checked for the previous option (Listed in Interests) in order for this to have anything in it.', 'ultimate-wp-mail' ),
				'options'		=> array(
					'before'		=> __( 'Before', 'ultimate-wp-mail' ),
					'after'			=> __( 'After', 'ultimate-wp-mail' ),
					'none'			=> __( 'None', 'ultimate-wp-mail' ),
				)
			)
		);

		$sap->add_setting(
			'ewd-uwpm-settings',
			'ewd-uwpm-general',
			'text',
			array(
				'id'            => 'email-from-name',
				'title'         => __( 'Email "From" Name', 'ultimate-wp-mail' ),
				'description'	=> __( 'Who should the emails be sent from? Leave blank to use the default "From" address for your site.', 'ultimate-wp-mail' )
			)
		);

		$sap->add_setting(
			'ewd-uwpm-settings',
			'ewd-uwpm-general',
			'text',
			array(
				'id'            => 'email-from-email',
				'title'         => __( 'Email "From" Email Address', 'ultimate-wp-mail' ),
				'description'	=> __( 'What email address should the emails be sent from? Leave blank to use the default "From" address for your site.', 'ultimate-wp-mail' )
			)
		);

		$sap->add_section(
			'ewd-uwpm-settings',
			array(
				'id'            => 'ewd-uwpm-smtp-tab',
				'title'         => __( 'SMTP', 'ultimate-wp-mail' ),
				'is_tab'		=> true,
			)
		);

		$sap->add_section(
			'ewd-uwpm-settings',
			array(
				'id'            => 'ewd-uwpm-smtp',
				'title'         => __( 'SMTP', 'ultimate-wp-mail' ),
				'tab'	        => 'ewd-uwpm-smtp-tab',
			)
		);

		$sap->add_setting(
			'ewd-uwpm-settings',
			'ewd-uwpm-smtp',
			'toggle',
			array(
				'id'			=> 'smtp-enable',
				'title'			=> __( 'Enable SMTP', 'ultimate-wp-mail' ),
				'description'	=> __( 'These settings will affect all outgoing emails from WordPress', 'ultimate-wp-mail' )
			)
		);

		$sap->add_setting(
			'ewd-uwpm-settings',
			'ewd-uwpm-smtp',
			'text',
			array(
				'id'            => 'smtp-host',
				'title'         => __( 'SMTP Host', 'ultimate-wp-mail' ),
				'description'	=> __( 'What email server you are using?', 'ultimate-wp-mail' ),
				'conditional_on'		=> 'smtp-enable',
				'conditional_on_value'	=> true
			)
		);

		$sap->add_setting(
			'ewd-uwpm-settings',
			'ewd-uwpm-smtp',
			'radio',
			array(
				'id'			=> 'smtp-encryption',
				'title'			=> __( 'Encryption', 'ultimate-wp-mail' ),
				'description'	=> __( 'Is your email encrypted using SSL, TLS or none (insecure)?', 'ultimate-wp-mail' ),
				'options'		=> array(
					'none'		=> __( 'None', 'ultimate-wp-mail' ),
					'ssl'		=> __( 'SSL', 'ultimate-wp-mail' ),
					'tls'		=> __( 'TLS', 'ultimate-wp-mail' ),
				),
				'conditional_on'		=> 'smtp-enable',
				'conditional_on_value'	=> true
			)
		);

		$sap->add_setting(
			'ewd-uwpm-settings',
			'ewd-uwpm-smtp',
			'number',
			array(
				'id'            => 'smtp-port',
				'title'         => __( 'SMTP Port', 'ultimate-wp-mail' ),
				'description'	=> __( 'Generally SMTP Port depends on your encryption type.', 'ultimate-wp-mail' ),
				'conditional_on'		=> 'smtp-enable',
				'conditional_on_value'	=> true
			)
		);

		$sap->add_setting(
			'ewd-uwpm-settings',
			'ewd-uwpm-smtp',
			'toggle',
			array(
				'id'			=> 'smtp-autotls-disable',
				'title'			=> __( 'Disable Auto TLS', 'ultimate-wp-mail' ),
				'description'	=> __( 'By default, when "Encryption" is not set to "TLS", TLS encryption is automatically used if the server supports it (recommended). In some cases, due to server misconfigurations, this can cause issues and may need to be disabled.', 'ultimate-wp-mail' ),
				'conditional_on'		=> 'smtp-enable',
				'conditional_on_value'	=> true
			)
		);

		$sap->add_setting(
			'ewd-uwpm-settings',
			'ewd-uwpm-smtp',
			'toggle',
			array(
				'id'			=> 'smtp-auth',
				'title'			=> __( 'Authentication', 'ultimate-wp-mail' ),
				'description'	=> __( 'Do you access your email server using an account?', 'ultimate-wp-mail' ),
				'conditional_on'		=> 'smtp-enable',
				'conditional_on_value'	=> true
			)
		);

		$sap->add_setting(
			'ewd-uwpm-settings',
			'ewd-uwpm-smtp',
			'text',
			array(
				'id'            => 'smtp-username',
				'title'         => __( 'SMTP Username', 'ultimate-wp-mail' ),
				'description'	=> __( 'Email server account username', 'ultimate-wp-mail' ),
				'conditional_on'		=> 'smtp-auth',
				'conditional_on_value'	=> true
			)
		);

		$sap->add_setting(
			'ewd-uwpm-settings',
			'ewd-uwpm-smtp',
			'password',
			array(
				'id'            => 'smtp-password',
				'title'         => __( 'SMTP Password', 'ultimate-wp-mail' ),
				'description'	=> __( 'Email server account password', 'ultimate-wp-mail' ),
				'conditional_on'		=> 'smtp-auth',
				'conditional_on_value'	=> true
			)
		);

		$sap->add_setting(
			'ewd-uwpm-settings',
			'ewd-uwpm-smtp',
			'toggle',
			array(
				'id'			=> 'smtp-force-from-email',
				'title'			=> __( 'Force From Email', 'ultimate-wp-mail' ),
				'description'	=> __( 'Should the \'From\' Email address be set to the SMTP Username? This prevents the following error log message: SMTP Error: data not accepted', 'ultimate-wp-mail' ),
				'conditional_on'		=> 'smtp-auth',
				'conditional_on_value'	=> true
			)
		);

		$sap->add_section(
			'ewd-uwpm-settings',
			array(
				'id'            => 'ewd-uwpm-send-events-tab',
				'title'         => __( 'Send Events', 'ultimate-wp-mail' ),
				'is_tab'		=> true,
			)
		);

		$sap->add_section(
			'ewd-uwpm-settings',
			array(
				'id'            => 'ewd-uwpm-send-events',
				'title'         => __( 'Send Events', 'ultimate-wp-mail' ),
				'tab'	        => 'ewd-uwpm-send-events-tab',
			)
		);

		$sap->add_setting(
			'ewd-uwpm-settings',
			'ewd-uwpm-send-events',
			'text',
			array(
				'id'          => 'schedule-check-delay',
				'title'       => __( 'Check Scheduled Emails Interval (in minutes)', 'ultimate-wp-mail' ),
				'description' => __( 'How often should the plugin check to see if there are any scheduled emails to be sent? It is in minutes. The minimum is 1 and the default is 5.', 'ultimate-wp-mail' ),
				'placeholder' => '5'
			)
		);

		$send_events_description = '';
		
		$sap->add_setting(
			'ewd-uwpm-settings',
			'ewd-uwpm-send-events',
			'infinite_table',
			array(
				'id'			=> 'send-actions',
				'title'			=> __( 'Send Events', 'ultimate-wp-mail' ),
				'add_label'		=> __( '+ ADD', 'ultimate-wp-mail' ),
				'del_label'		=> __( 'Delete', 'ultimate-wp-mail' ),
				'description'	=> $send_events_description,
				'fields'		=> array(
					'id' 	=> array(
						'type' 		=> 'hidden',
						'label' 	=> 'ID',
						'classes' 	=> array( 'sap-hidden' ),
					),
					'enabled' => array(
						'type' 		=> 'toggle',
						'label' 	=> 'Enabled',
						'required' 	=> true
					),
					'action_type' => array(
						'type' 			=> 'select',
						'label' 		=> __( 'Action Type', 'ultimate-wp-mail' ), 
						'options' 		=> $this->action_type_options
					),
					'includes' => array(
						'type' 			=> 'select',
						'label' 		=> __( 'WC Include', 'ultimate-wp-mail' ),
						'options' 		=> $this->include_options,
						'conditional_on' 		=> 'action_type',
              			'conditional_on_value' 	=> array( 'wc_x_time_since_cart_abandoned', 'wc_x_time_after_purchase', 'product_added', 'product_purchased', 'subscription_created' )
					),
					'cpt_select' => array(
						'type' 			=> 'select',
						'label' 		=> __( 'Custom Post Type', 'ultimate-wp-mail' ),
						'options' 		=> $this->cpt_options,
						'conditional_on' 		=> 'action_type',
              			'conditional_on_value' 	=> array( 'cpt_created', 'cpt_updated', 'cpt_deleted' )
					),
					'email_id' => array(
						'type' 			=> 'select',
						'label' 		=> __( 'Email', 'ultimate-wp-mail' ),
						'options' 		=> $this->email_options,
					),
					'target' => array(
						'type' 			=> 'select',
						'label' 		=> __( 'Target', 'ultimate-wp-mail' ),
						'options' 		=> array(
							'user'			=> __( 'User', 'ultimate-wp-mail' ),
							'admin'			=> __( 'Admin', 'ultimate-wp-mail' ),
						),
						'conditional_on' 		=> 'action_type',
              			'conditional_on_value' 	=> array( 'user_registers', 'user_profile_updated', 'user_role_changed', 'user_x_time_since_login', 'wc_x_time_since_cart_abandoned', 'wc_x_time_after_purchase', 'product_added', 'product_purchased', 'subscription_created', 'cpt_created', 'cpt_updated', 'cpt_deleted' )
					),
					'interval_count' => array(
						'type' 		=> 'select',
						'label' 	=> __( 'Delay', 'ultimate-wp-mail' ),
						'options' 	=> $this->count_options,
						'conditional_on' 		=> 'action_type',
              			'conditional_on_value' 	=> array( 'user_registers', 'user_profile_updated', 'user_role_changed', 'user_x_time_since_login', 'post_published_interest', 'new_comment_on_post', 'wc_x_time_since_cart_abandoned', 'wc_x_time_after_purchase', 'product_added', 'product_purchased', 'subscription_created', 'cpt_created', 'cpt_updated', 'cpt_deleted' )
					),
					'interval_unit' => array(
						'type' 		=> 'select',
						'label' 	=> __( '', 'ultimate-wp-mail' ),
						'options' 	=> array(
							'Minutes'	=> __( 'Minute(s)', 'ultimate-wp-mail' ),
							'Hours'		=> __( 'Hour(s)', 'ultimate-wp-mail' ),
							'Days'		=> __( 'Day(s)', 'ultimate-wp-mail' ),
							'Weeks'		=> __( 'Week(s)', 'ultimate-wp-mail' ),
						),
						'conditional_on' 		=> 'action_type',
              			'conditional_on_value' 	=> array( 'user_registers', 'user_profile_updated', 'user_role_changed', 'user_x_time_since_login', 'post_published_interest', 'new_comment_on_post', 'wc_x_time_since_cart_abandoned', 'wc_x_time_after_purchase', 'product_added', 'product_purchased', 'subscription_created', 'cpt_created', 'cpt_updated', 'cpt_deleted' )
					)
				)
			)
		);

		$sap->add_section(
			'ewd-uwpm-settings',
			array(
				'id'            => 'ewd-uwpm-send-events-contact',
				'title'         => __( 'Contact Information', 'ultimate-wp-mail' ),
				'tab'	        => 'ewd-uwpm-send-events-tab',
			)
		);

		$sap->add_setting(
			'ewd-uwpm-settings',
			'ewd-uwpm-send-events-contact',
			'text',
			array(
				'id'            => 'admin-email',
				'title'         => __( 'Admin Email', 'ultimate-wp-mail' ),
				'description'	=> __( 'Who should the emails targeted to the admin of the site be sent to?', 'ultimate-wp-mail' ),
				'default'		=> $this->defaults['admin-email'],
			)
		);

		$sap->add_section(
			'ewd-uwpm-settings',
			array(
				'id'            => 'ewd-uwpm-logging-tab',
				'title'         => __( 'Logging', 'ultimate-wp-mail' ),
				'is_tab'		=> true,
			)
		);

		$sap->add_section(
			'ewd-uwpm-settings',
			array(
				'id'            => 'ewd-uwpm-logging-general',
				'title'         => __( 'General', 'ultimate-wp-mail' ),
				'tab'	        => 'ewd-uwpm-logging-tab',
			)
		);

		$sap->add_setting(
			'ewd-uwpm-settings',
			'ewd-uwpm-logging-general',
			'text',
			array(
				'id'            => 'maximum-email-logs',
				'title'         => __( 'Maximum Number of Logged Emails', 'ultimate-wp-mail' ),
				'description'	=> __( 'How many email log records should be kept? The oldest log will be deleted each time a new one is added after that. Default is 200.', 'ultimate-wp-mail' )
			)
		);

		$sap->add_setting(
			'ewd-uwpm-settings',
			'ewd-uwpm-logging-general',
			'toggle',
			array(
				'id'			=> 'notify-admin-on-email-error',
				'title'			=> __( 'Notify Admin on Error', 'ultimate-wp-mail' ),
				'description'	=> __( 'Send a message to the admin when an email fails to send correctly.', 'ultimate-wp-mail' )
			)
		);

		$sap->add_setting(
			'ewd-uwpm-settings',
			'ewd-uwpm-logging-general',
			'html',
			array(
				'id'			=> 'template-tags-description',
				'title'			=> __( 'Template Tags', 'ultimate-wp-mail' ),
				'html'			=> '
					<p class="description">' . __( 'Use the following tags to automatically add information to the failure email notifications.', 'ultimate-wp-mail' ) . '</p>' .
					$this->render_template_tag_descriptions(),
			)
		);

		$sap->add_setting(
			'ewd-uwpm-settings',
			'ewd-uwpm-logging-general',
			'text',
			array(
				'id'			=> 'notify-admin-subject',
				'title'			=> __( 'Admin Notification Subject', 'ultimate-wp-mail' ),
				'description'	=> __( 'Enter the subject for the email an admin should receive when an email fails to send correctly.', 'ultimate-wp-mail' ),
				'placeholder'	=> $this->defaults['notify-admin-subject'],
			)
		);

		$sap->add_setting(
			'ewd-uwpm-settings',
			'ewd-uwpm-logging-general',
			'editor',
			array(
				'id'			=> 'notify-admin-message',
				'title'			=> __( 'Admin Notification Email', 'ultimate-wp-mail' ),
				'description'	=> __( 'Enter the email an admin should receive when an email fails to send correctly.', 'ultimate-wp-mail' ),
				'default'		=> $this->defaults['notify-admin-message'],
			)
		);

		$sap->add_section(
			'ewd-uwpm-settings',
			array(
				'id'            => 'ewd-uwpm-labelling-tab',
				'title'         => __( 'Labelling', 'ultimate-wp-mail' ),
				'is_tab'		=> true,
			)
		);

		$sap->add_section(
			'ewd-uwpm-settings',
			array(
				'id'            => 'ewd-uwpm-labelling-subscriptions',
				'title'         => __( 'Subscriptions', 'ultimate-wp-mail' ),
				'tab'	        => 'ewd-uwpm-labelling-tab',
			)
		);

		$sap->add_setting(
			'ewd-uwpm-settings',
			'ewd-uwpm-labelling-subscriptions',
			'text',
			array(
				'id'            => 'label-subscribe',
				'title'         => __( 'Subscribe', 'ultimate-wp-mail' ),
				'description'	=> ''
			)
		);

		$sap->add_setting(
			'ewd-uwpm-settings',
			'ewd-uwpm-labelling-subscriptions',
			'text',
			array(
				'id'            => 'label-unsubscribe',
				'title'         => __( 'Unsubscribe', 'ultimate-wp-mail' ),
				'description'	=> ''
			)
		);

		$sap->add_setting(
			'ewd-uwpm-settings',
			'ewd-uwpm-labelling-subscriptions',
			'text',
			array(
				'id'            => 'label-login-select-topics',
				'title'         => __( 'Log in to your account so that you can subscribe to topics you\'re interested in!', 'ultimate-wp-mail' ),
				'description'	=> ''
			)
		);

		$sap->add_setting(
			'ewd-uwpm-settings',
			'ewd-uwpm-labelling-subscriptions',
			'text',
			array(
				'id'            => 'label-select-topics',
				'title'         => __( 'Select topics you\'re interested in below to receive emails when new items are posted!', 'ultimate-wp-mail' ),
				'description'	=> ''
			)
		);

		$sap = apply_filters( 'ewd_uwpm_settings_page', $sap, $this );

		$sap->add_admin_menus();

	}

	/**
	 * Render HTML code of descriptions for the template tags
	 * @since 1.2.0
	 */
	public function render_template_tag_descriptions() {

		$descriptions = array(
			'{date}'			=> __( 'The date/time that the email failed to send.', 'ultimate-wp-mail' ),
			'{subject}'			=> __( 'The subject of the email that failed to send.', 'ultimate-wp-mail' ),
			'{message}'			=> __( 'The message of the email that failed to send.', 'ultimate-wp-mail' ),
			'{status}'			=> __( 'The error status that was returned when the email failed to send.', 'ultimate-wp-mail' ),
			'{recipient}'		=> __( 'The email address of the recipient of the failed email.', 'ultimate-wp-mail' ),
			'{headers}'			=> __( 'The headers of the email that failed to send.', 'ultimate-wp-mail' ),
			'{attachments}'		=> __( 'The attachments included with the email that failed to send.', 'ultimate-wp-mail' ),
			'{site_link}'		=> __( 'A link to the site that is generating the failure message.', 'ultimate-wp-mail' ),
		);

		$output = '';

		foreach ( $descriptions as $tag => $description ) {
			$output .= '
				<div class="ewd-uwpm-template-tags-box">
					<strong>' . $tag . '</strong> ' . $description . '
				</div>';
		}

		return $output;
	}
}
} // endif;
